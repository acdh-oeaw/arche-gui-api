<?php

namespace Drupal\arche_gui_api\Object;

/**
 * Description of RPRObject
 *
 * @author nczirjak
 */
class RPRObject extends \Drupal\arche_gui_api\Object\MainObject {

    protected $model;

    protected function createModel(): void {
        $this->model = new \Drupal\arche_gui_api\Model\RPRModel();
    }

    public function init(string $repoid, string $lang): array {
        $this->createModel();
        return $this->processData($this->model->getData($repoid, $lang));
    }

   
    private function processData(array $data): array {
        $this->result = array();
        foreach ($data as $obj) {
            if(isset($obj->id) && isset($obj->title) && isset($obj->relatedtype) && isset($obj->acdhtype)) {
                $this->result[] = array(
                    0 => "<a id='archeHref' href='/browser/oeaw_detail/$obj->id'>$obj->title</a>",
                    1 => str_replace($this->repo->getSchema()->__get('namespaces')->ontology, '', $obj->relatedtype),
                    2 => str_replace($this->repo->getSchema()->__get('namespaces')->ontology, '', $obj->acdhtype)
                );
            }
        }
        return $this->result;
    }

}
