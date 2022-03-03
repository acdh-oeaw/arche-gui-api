<?php

namespace Drupal\arche_gui_api\Object;

/**
 * Description of VersionsObject
 *
 * @author nczirjak
 */
class VersionsObject extends \Drupal\arche_gui_api\Object\MainObject
{
    
    protected $model;

    protected function createModel(): void
    {
        $this->model = new \Drupal\acdh_repo_gui\Model\BlocksModel();
    }
    
    public function init(string $repoid, string $lang): array
    {
        $this->createModel();
        $params = array('identifier' => $repoid, 'lang' => $lang);
        //return $this->model->getData($repoid, $lang);
        return $this->processData($this->model->getVersionsData($params), $repoid);
    }
    
    private function processData(array $data, string $repoid): array
    {
        $this->result = array();
        if (count($data) > 0) {
            
            $this->result = $this->createTreeData($data);
        } else {
            $this->result[0] = array("uri" => 0, "text" => "There are no child elements",
                "userAllowedToDL" => false, "dir" => false, "children" => false);
        }
        return $this->result;
    }

   
     private function createTreeData(array $data): array
    {
        $tree = array();       
        $first = array(
            "id" => $data[0]->id,
            "uri" => $data[0]->id,
            "uri_dl" => $this->repo->getBaseUrl() . $data[0]->id,
            "filename" => $this->getDateFromDateTime($data[0]->avdate).' - '.$data[0]->version,
            "resShortId" => $data[0]->id,
            "title" => $this->getDateFromDateTime($data[0]->avdate).' - '.$data[0]->version,
            "text" => $this->getDateFromDateTime($data[0]->avdate).' - '.$data[0]->version,
            "previd" => '',
            "userAllowedToDL" => true,
            "dir" => false,
            "accessRestriction" => 'public',
            "encodedUri" => $this->repo->getBaseUrl() . $data[0]->id
        );

        unset($data[0]);
        
        $new = array();
        foreach ($data as $a) {
            $a = (array) $a;
            $a['dir'] = false;
            $a['userAllowedToDL'] = true;
            if(isset($a['avdate'])) {
                $a['text'] = $this->getDateFromDateTime($a['avdate']).' - '.$a['version'];
            }
            $new[$a['previd']][] = $a;
        }
        
        $tree = $this->convertToTreeById($new, array($first));
        return $tree;
    }

    private function getDateFromDateTime(string $date) :string
    {
        $time = strtotime($date);
        return date('Y-m-d',$time);
    }
    
    /**
     * This func is generating a child based array from a single array by ID
     *
     * @param type $list
     * @param type $parent
     * @return type
     */
    public function convertToTreeById(&$list, $parent)
    {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($list[$l['id']])) {
                $l['children'] = $this->convertToTreeById($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }
}
