<?php

namespace Drupal\arche_gui_api\Object;

/**
 * Description of MembersObject
 *
 * @author nczirjak
 */
class MembersObject extends \Drupal\arche_gui_api\Object\MainObject
{
    protected $model;

    protected function createModel(): void
    {
        $this->model = new \Drupal\arche_gui_api\Model\MembersModel();
    }

    public function init(string $repoid): array
    {
        $this->createModel();
        return $this->processData($this->model->getData($repoid));
    }

   
    private function processData(array $data): array
    {
        $this->result = array();
        foreach ($data as $obj) {
            if (isset($obj->id) && isset($obj->title)) {
                $this->result[] = array("<a id='archeHref' href='/browser/detail/$obj->id'>$obj->title</a>");
            }
        }
        //$this->result = array("data" => $this->result);
        return $this->result;
    }
}
