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

    private function createGNDFile(string $data): array
    {
        $this->createFileLocation();
        return $this->processData($data);
    }

    private function createFileLocation(): void
    {
        $this->host = str_replace('http://', 'https://', \Drupal::request()->getSchemeAndHttpHost() . '/browser/detail/');
        $this->fileLocation = \Drupal::request()->getSchemeAndHttpHost() . '/browser/sites/default/files/beacon.txt';
    }

    private function processData(string $data): array
    {
        if (!empty($data)) {
            return $this->createFileContent($data);
        }
        return array();
    }

    private function createFileContent(string $str): array
    {
        if (!empty($str)) {
            $this->text = "#FORMAT: BEACON \n" . $str;
            if (file_save_data($this->text, "public://beacon.txt", \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE)) {
                return array('status' => 'File created', 'url' => $this->fileLocation);
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    
}
