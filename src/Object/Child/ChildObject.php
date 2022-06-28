<?php

namespace Drupal\arche_gui_api\Object\Child;

/**
 * Description of RPRObject
 *
 * @author nczirjak
 */
class ChildObject extends \Drupal\arche_gui_api\Object\MainObject
{
    protected $model;

    protected function createModel(): void
    {
        $this->model = new \Drupal\arche_gui_api\Model\Child\ChildModel();
    }

    public function init(string $repoid, string $lang, array $searchProps): array
    {
        $this->createModel();
        return $this->processData($this->model->getData($repoid,  ['https://vocabs.acdh.oeaw.ac.at/schema#hasActor'], 
    (int)$searchProps['offset'], (int)$searchProps['limit'], $searchProps['search'], (int)$searchProps['orderby'], $searchProps['order'], $lang));
    }

   
    private function processData(array $data): array
    {
        $this->result = array();
        foreach ($data as $obj) {
            if (isset($obj->id) && isset($obj->title) && isset($obj->property) ) {
                $this->result[] = array(
                    'title' => "<a id='archeHref' href='/browser/oeaw_detail/$obj->id'>$obj->title</a>",
                    'property' => $obj->property,
                    'sumcount' => $obj->sumcount
                );
            }
        }
        return $this->result;
    }
}
