<?php

namespace Drupal\arche_gui_api\Object\Collection;

/**
 * Description of CollectionObject
 *
 * @author nczirjak
 */
class CollectionObject extends \Drupal\arche_gui_api\Object\MainObject
{
    protected $model;

    protected function createModel(): void
    {
        $this->model = new \Drupal\arche_gui_api\Model\Collection\CollectionModel();
    }

    public function init(string $repoid, string $lang): array
    {
        $this->createModel();
        //return $this->model->getData($repoid, $lang);
        return $this->processData($this->model->getData($repoid, $lang));
    }

    private function processData(array $data): array
    {
        $this->result = array();

        if (count($data) > 0) {
            foreach ($data as $k => $v) {
                $this->createBaseProperties($v);
                $this->isPublic($v);
                $this->isDirOrFile($v);
                $this->result[$k] = $v;
            }
        } else {
            $this->result[0] = array("uri" => 0, "text" => "There are no child elements",
                "userAllowedToDL" => false, "dir" => false, "children" => false);
        }
        return $this->result;
    }

    /**
     * Set up the base parameters
     * @param type $v
     * @return void
     */
    private function createBaseProperties(&$v): void
    {
        $v['uri'] = $v['id'];
        $v['uri_dl'] = $this->repoDb->getBaseUrl() . $v['id'];
        $v['text'] = $v['title'];
        $v['resShortId'] = $v['id'];
        $v['accessRestriction'] = $v['accesres'];
        $v['encodedUri'] = $this->repoDb->getBaseUrl() . $v['id'];
        $v['a_attr'] = array("href" => str_replace('api/', 'browser/oeaw_detail/', $this->repoDb->getBaseUrl()) . $v['id']);
    }

    /**
     * Actual resource accessrestriction
     * @param type $v
     */
    private function isPublic(&$v): void
    {
        if ($v['accesres'] == 'public') {
            $v['userAllowedToDL'] = true;
        } else {
            $v['userAllowedToDL'] = false;
        }
    }

    /**
     * The actual resource is a binary file or a directory
     * @param type $v
     */
    private function isDirOrFile(&$v): void
    {
        $allowedFormats = [$this->repoDb->getSchema()->classes->resource, $this->repoDb->getSchema()->classes->metadata];
        
        if (!empty($v['rdftype']) && in_array($v['rdftype'], $allowedFormats)) {
            $v['dir'] = false;
            $v['icon'] = "jstree-file";
        } else {
            $v['dir'] = true;
            $v['children'] = true;
        }
    }
}
