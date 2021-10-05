<?php

namespace Drupal\arche_gui_api\Object\Ontology;

/**
 * Description of OntolgyJs
 *
 * @author nczirjak
 */
class OntolgyJsObject extends \Drupal\arche_gui_api\Object\MainObject
{
    protected $model;

    protected function createModel(): void
    {
        $this->model = new \Drupal\arche_gui_api\Model\Ontology\OntolgyJsModel();
    }

    public function init(string $lang = "en"): array
    {
        $this->createModel();
        return $this->processData($this->model->getData(), $lang);
    }

   
    private function processData(array $data, string $lang = "en"): array
    {
        $this->result['$schema'] = "http://json-schema.org/draft-07/schema#";
        $collections = "0";
        $files = "0";
        if (isset($data[0]->collections) && !empty($data[0]->collections)) {
            $collections = $data[0]->collections . " " . t("Collections", array(), array('langcode' => $lang));
        }
        if (isset($data[0]->binaries) && !empty($data[0]->binaries)) {
            $files = $data[0]->binaries . " " . t("Files", array(), array('langcode' => $lang));
        }

        if (empty($files)) {
            $files = "0";
        }
        if (empty($collections)) {
            $collections = "0";
        }
        $this->result['text'] = $collections . " " . t("with", array(), array('langcode' => $lang)) . " " . $files;
        return $this->result;
    }
}
