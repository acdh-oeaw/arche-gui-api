<?php

namespace Drupal\arche_gui_api\Object;

/**
 * Description of GndPersonObject
 *
 * @author nczirjak
 */
class GndPersonsObject extends \Drupal\arche_gui_api\Object\MainObject
{
    private $fileLocation;
    private $host;
    private $text = "";
    protected $model;

    protected function createModel(): void
    {
        $this->model = new \Drupal\arche_gui_api\Model\GndPersonsModel();
    }

    public function init(): array
    {
        $this->createModel();
        return $this->createGNDFile($this->model->getData());
    }

    private function createGNDFile(array $data): array
    {
        $this->createFileLocation();
        return $this->processData($data);
    }

    private function createFileLocation(): void
    {
        $this->host = str_replace('http://', 'https://', \Drupal::request()->getSchemeAndHttpHost() . '/browser/detail/');
        $this->fileLocation = \Drupal::request()->getSchemeAndHttpHost() . '/browser/sites/default/files/beacon.txt';
    }

    private function processData(array $data): array
    {
        if (count((array) $data) > 0) {
            foreach ($data as $val) {
                if (count($val[$this->repoDb->getSchema()->id] ?? []) > 0) {
                    //$this->text .= $this->getGNDIdentifier($val[$this->repoDb->getSchema()->id]) . "|" . $this->host . $this->getRepoId($val[$this->repoDb->getSchema()->id]) . " \n";
                    $this->text .= $this->getGNDIdentifier($val[$this->repoDb->getSchema()->id]) . "|" . $this->getResourceUrl($val[$this->repoDb->getSchema()->id]) . " \n";
                }
            }
            return $this->createFileContent();
        }
        return array();
    }

    private function createFileContent(): array
    {
        if (!empty($this->text)) {
            $this->text = "#FORMAT: BEACON \n" . $this->text;
            if (file_save_data($this->text, "public://beacon.txt", \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE)) {
                return array('status' => 'File created', 'url' => $this->fileLocation);
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    
    /**
     * Get the GND identifier from the data array
     * @param array $ids
     * @return string
     */
    private function getGNDIdentifier(array $ids): string
    {
        foreach ($ids as $id) {
            if (strpos($id['value'], '/gnd/') !== false) {
                return $id['value'];
            }
        }
        return "";
    }
    
    /**
     * Get the ARCHE GUI Detail view url
     * @param array $ids
     * @return int
     */
    private function getRepoId(array $ids): int
    {
        foreach ($ids as $id) {
            if (str_starts_with($id['value'], $this->repoDb->getBaseUrl())) {
                return (int)str_replace($this->repoDb->getBaseUrl(), "", $id['value']);
            }
        }
        return 0;
    }
    
    /**
     * Return the id.acdh.oeaw.ac.at/pid/gui url
     * @param array $ids
     * @return string
     */
    private function getResourceUrl(array $ids): string
    {
        $urls = $this->fetchUrls($ids);
        return isset($urls['acdhId']) ? $urls['acdhId'] : (isset($urls['pid']) ? $urls['pid'] : (isset($urls['acdhGuiId']) ? $urls['acdhGuiId'] : ""));
    }
    
    /**
     * Collect the urls from the identifiers array
     * @param array $ids
     * @return array
     */
    private function fetchUrls(array $ids): array
    {
        $result = [];
        foreach ($ids as $id) {
            if (str_starts_with($id['value'], $this->repoDb->getSchema()->namespaces->id)) {
                $result['acdhId'] = $id['value'];
            } elseif (str_starts_with($id['value'], $this->repoDb->getSchema()->drupal->epicResolver)) {
                $result['pid'] = $id['value'];
            } else {
                $result['acdhGuiId'] = $this->host . $this->getRepoId($ids);
            }
        }
        return $result;
    }
}
