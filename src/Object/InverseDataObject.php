<?php

namespace Drupal\arche_gui_api\Object;

/**
 * Description of InverseDataObject
 *
 * @author nczirjak
 */
class InverseDataObject extends \Drupal\arche_gui_api\Object\MainObject
{
    protected $model;

    protected function createModel(): void
    {
        $this->model = new \Drupal\arche_gui_api\Model\Detail\InverseDataModel();
    }

    public function init(string $repoid): array
    {
        $this->createModel();
        return $this->processData($this->model->getData($repoid));
    }

    private function processData(array $data): array
    {
        {
            $result = array();
            foreach ($data as $id => $obj) {
                foreach ($obj as $o) {
                    $arr = array(
                        str_replace($this->repoDb->getSchema()->namespaces->ontology, 'acdh:', $o->property),
                        "<a id='archeHref' href='/browser/oeaw_detail/$id'>$o->title</a>"
                    );
                    $result[] = $arr;
                }
            }
            return array("data" => $result);
        }
    }
}
