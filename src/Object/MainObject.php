<?php

namespace Drupal\arche_gui_api\Object;

/**
 * Description of MainObject
 *
 * @author nczirjak
 */
class MainObject {

    protected $result = array();
    protected $model;
    protected $repo;
    protected $repodb;
    protected $siteLang = "en";

    public function __construct() {
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language']) : $this->siteLang = "en";

        $this->config = \Drupal::service('extension.list.module')->getPath('acdh_repo_gui') . '/config/config.yaml';

        try {
            $this->repo = \acdhOeaw\arche\lib\Repo::factory($this->config);
            $this->repodb = \acdhOeaw\arche\lib\RepoDb::factory($this->config);
            
        } catch (\Exception $ex) {
            \Drupal::messenger()->addWarning($this->t('Error during the BaseController initialization!') . ' ' . $ex->getMessage());
            return array();
        }
    }
    
    protected function createModel(): void {
        $this->model = new stdClass();
    }
    
    public function getData(): array {
        return $this->result;
    }

}
