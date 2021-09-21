<?php

namespace Drupal\arche_gui_api\Object;

/**
 * Description of OrganisationsObject
 *
 * @author nczirjak
 */
class OrganisationsObject {
    
    private $str = "";
    private $result = array();
    private $model;
    private $repo;
    private $repodb;
   
    public function __construct(string $searchStr)
    {
        $this->str = strtolower($searchStr);
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language']) : $this->siteLang = "en";
        
        $this->config = \Drupal::service('extension.list.module')->getPath('acdh_repo_gui') . '/config/config.yaml';
       
        try {
            $this->repo = \acdhOeaw\arche\lib\Repo::factory($this->config);
            $this->repodb = \acdhOeaw\arche\lib\RepoDb::factory($this->config);
            $this->model = new \Drupal\arche_gui_api\Model\OrganisationsModel();
        } catch (\Exception $ex) {
            \Drupal::messenger()->addWarning($this->t('Error during the BaseController initialization!').' '.$ex->getMessage());
            return array();
        }
        
    }
    
    public function init(): bool {
        
        try {
            $this->formatView($this->model->getData($this->str));
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    public function getData(): array {
        return $this->result;
    }
        
    private function formatView(array $data): void
    {
        $this->result = array();
        foreach ($data as $k => $val) {
            foreach ($val as $v) {
                if (isset($v->value) && !empty($v->value)) {
                    $title = $v->value;
                    $lang = $v->lang;
                    $altTitle = '';
                    if ($v->property == $this->repo->getSchema()->namespaces->ontology.'hasAlternativeTitle') {
                        $altTitle = $v->value;
                    }
                    
                    $this->result[$k] = new \stdClass();
                    $this->result[$k]->title[$lang] = $title;
                    $this->result[$k]->uri = $this->repo->getBaseUrl() . $k;
                    $this->result[$k]->identifier = $k;
                    $this->result[$k]->altTitle = $altTitle;
                }
            }
        }
        $this->result = array_values($this->result);
    }
    
}
