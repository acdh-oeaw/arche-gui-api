<?php

namespace Drupal\arche_gui_api\Object\Collection;

/**
 * Description of CollectionBinariesObject
 *
 * @author nczirjak
 */
class CollectionBinariesObject extends \Drupal\arche_gui_api\Object\MainObject
{
    protected $model;
    private $tmpDir;
    private $repoUrl;
    protected $repoid;
    private $collectionDate;
    private $collectionTmpDir;
    private $turtle;

    public function __construct()
    {
        parent::__construct();
        if (empty($this->tmpDir)) {
            $this->setTmpDir();
        }
        $this->turtle = new \Drupal\arche_gui_api\Controller\Dissemination\TurtleDissController();
    }

    /**
     *
     * @param array $binaries
     * @param string $repoid
     * @param string $username
     * @param string $password
     * @return string
     */
    public function init(array $binaries, string $repoid, string $username = "", string $password = ""): string
    {
        $this->repoUrl = $this->repo->getBaseUrl() . $repoid;
        
        //1. setup tmp dir
        if ($this->collectionCreateDlDirectory() === false) {
            return '';
        }
        //2. download the selected files
        $this->collectionDownloadFiles($binaries, $username, $password);
        //3. add the turtle file into the collection
        if ($this->collectionGetTurtle($repoid) === false) {
            \Drupal::logger('acdh_repo_gui')->notice('collection turtle file generating error' . $this->repoUrl);
        }
        //4. tar the files
        //5. remove the downloaded files and leave just the tar file.
        if ($this->collectionTarFiles() === false) {
            return false;
        }
        $wwwurl = str_replace('/api/', '', $this->repo->getBaseUrl());
        return $wwwurl . '/browser/sites/default/files/collections/' . $this->collectionDate . '/collection.tar';
    }
    

    /**
     * Setup the collection directory for the downloads
     *
     * @param string $dateID
     * @return string
     */
    private function collectionCreateDlDirectory(): bool
    {
        if (empty($this->tmpDir)) {
            $this->setTmpDir();
        }
        $this->collectionDate = date("Ymd_his");
        //the main dir
        $this->collectionTmpDir = $this->tmpDir . "/collections/";
        //if the main directory is not exists
        if (!file_exists($this->collectionTmpDir)) {
            if (!@mkdir($this->collectionTmpDir, 0777)) {
                \Drupal::logger('acdh_repo_gui')->notice('cant create directory: ' . $this->collectionTmpDir);
                return false;
            }
        }
        //if we have the main directory then create the sub
        if (file_exists($this->collectionTmpDir)) {
            //create the actual dir
            if (!file_exists($this->collectionTmpDir . $this->collectionDate)) {
                if (!@mkdir($this->collectionTmpDir . $this->collectionDate, 0777)) {
                    \Drupal::logger('acdh_repo_gui')->notice('cant create directory: ' . $this->collectionDate);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Download the selected binaries
     *
     * @param array $binaries
     * @param string $username
     * @param string $password
     */
    public function collectionDownloadFiles(array $binaries, string $username = '', string $password = '')
    {
        $client = new \GuzzleHttp\Client(['auth' => [$username, $password], 'verify' => false]);
        ini_set('max_execution_time', 1800);
        
        foreach ($binaries as $b) {
            if (isset($b['path']) && isset($b['filename'])) {
                $url = $this->repo->getBaseUrl() . "/" . $b['uri'];
                $path = $b['path'];
                $filename = $this->createFileNameForCollectionDownload($b['filename']);
                $this->createCollectionDir($path);

                try {
                    $resource = fopen($this->collectionTmpDir . $this->collectionDate . '/' . $path . '/' . $filename, 'w');
                    $client->request('GET', $url, ['save_to' => $resource]);
                    chmod($this->collectionTmpDir . $this->collectionDate . '/' . $path . '/' . $filename, 0777);
                } catch (\GuzzleHttp\Exception $ex) {
                    \Drupal::logger('acdh_repo_gui')->notice('collection dl error:' . $ex->getMessage() . " " . $url);
                    continue;
                } catch (\RuntimeException $ex) {
                    \Drupal::logger('acdh_repo_gui')->notice('collection dl error:' . $ex->getMessage() . " " . $url);
                    continue;
                }
            } elseif (isset($b['path'])) {
                mkdir($this->collectionTmpDir . $this->collectionDate . '/' . $b['path'], 0777);
            }
        }
    }

    /**
     * Get the turtle file and copy it to the collection download directory
     *
     * @return bool
     */
    private function collectionGetTurtle(string $repoid): bool
    {
        $ttl = $this->turtle->execute($repoid);
        if (!empty($ttl)) {
            $turtleFile = fopen($this->collectionTmpDir . $this->collectionDate . '/turtle.ttl', "w");
            fwrite($turtleFile, $ttl);
            fclose($turtleFile);
            chmod($this->collectionTmpDir . $this->collectionDate . '/turtle.ttl', 0777);
        } else {
            return false;
        }
        return true;
    }

    /**
     * update TmpDir value
     */
    private function setTmpDir()
    {
        if (empty($this->tmpDir)) {
            $this->tmpDir = \Drupal::service('file_system')->realpath(\Drupal::config('system.file')->get('default_scheme') . "://");
        }
    }

    /**
     * TAR the downloaded collection files
     * @return bool
     */
    private function collectionTarFiles(): bool
    {
        ini_set('xdebug.max_nesting_level', 2000);
        //if we have files in the directory
        $dirFiles = scandir($this->collectionTmpDir . $this->collectionDate);

        if (count($dirFiles) > 0) {
            chmod($this->collectionTmpDir . $this->collectionDate, 0777);
            $archiveFile = $this->collectionTmpDir . $this->collectionDate . '/collection.tar';
            $file = fopen($archiveFile, "w");
            fclose($file);
            chmod($archiveFile, 0777);
            try {
                $tar = new \Drupal\Core\Archiver\Tar($archiveFile);
                foreach ($dirFiles as $d) {
                    if ($d == "." || $d == ".." || $d == 'collection.tar') {
                        continue;
                    } else {
                        $tarFilename = $d;
                        //if the filename is bigger than 100chars, then we need
                        //to shrink it
                        if (strlen($d) > 100) {
                            $ext = pathinfo($d, PATHINFO_EXTENSION);
                            $tarFilename = $this->createTarFileName($ext, $d);
                        }
                        chdir($this->collectionTmpDir . $this->collectionDate . '/');
                        $tar->add($d);
                    }
                }
                $this->collectionRemoveTempFiles();
            } catch (Exception $e) {
                \Drupal::logger('acdh_repo_gui')->notice('collection tar files error:' . $e->getMessage());
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     * Create the filename
     * @param string $ext
     * @param string $d
     * @return string
     */
    private function createTarFileName(string $ext, string $d): string
    {
        $tarFilename = str_replace($ext, '', $d);
        return substr($tarFilename, 0, 90). '.' . $ext;
    }

    /**
     *
     * Create turtle file from the resource
     *
     * @param string $fedoraUrl
     * @return type
     */
    public function turtleDissService()
    {
        $result = array();
        $client = new \GuzzleHttp\Client();

        try {
            $request = $client->request('GET', $this->repoUrl . '/metadata', ['Accept' => ['application/n-triples']]);
            if ($request->getStatusCode() == 200) {
                $body = "";
                $body = $request->getBody()->getContents();
                if (!empty($body)) {
                    if (class_exists('EasyRdf_Graph')) {
                        $graph = new \EasyRdf_Graph();
                    } else {
                        $graph = new \EasyRdf\Graph();
                    }
                    $graph->parse($body);
                    return $graph->serialise('turtle');
                }
            }
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            return "";
        } catch (\Exception $ex) {
            return "";
        }
    }

    /**
     * Remove the files from the collections directory
     */
    private function collectionRemoveTempFiles()
    {
        //get the collection directory
        $dir = $this->collectionTmpDir . $this->collectionDate;
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            //remove the directory
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                //if the file is the extracted collection then we will keep it
                if (strpos($file->getRealPath(), '/collection.tar') === false) {
                    unlink($file->getRealPath());
                }
            }
        }
    }

    /**
     * Remove the white spaces from the filename
     * @param string $filename
     * @return string
     */
    private function createFileNameForCollectionDownload(string $filename): string
    {
        $exp = explode("/", $filename);
        $last = end($exp);

        $file = "";
        if (strpos($last, '.') !== false) {
            $file = ltrim($last);
            $file = str_replace(' ', "_", $file);
        } else {
            $file = ltrim($filename);
            $file = str_replace(' ', "_", $file);
        }
        return $file;
    }
    
    /**
     * Create the directory for the collection
     * @param string $path
     * @return string
     */
    private function createCollectionDir(string $path): string
    {
        if (empty($this->tmpDir)) {
            $this->setTmpDir();
        }
        
        $dir = "";
        if (!file_exists($this->collectionTmpDir . $this->collectionDate)) {
            mkdir($this->collectionTmpDir . $this->collectionDate, 0777);
            $dir = $this->collectionTmpDir . $this->collectionDate;
        }

        if (!empty($path)) {
            $path = preg_replace('/\s+/', '_', $path);
            mkdir($this->collectionTmpDir . $this->collectionDate . '/' . $path, 0777, true);
            $dir = $this->collectionTmpDir . $this->collectionDate . '/' . $path;
        }
        return $dir;
    }
}
