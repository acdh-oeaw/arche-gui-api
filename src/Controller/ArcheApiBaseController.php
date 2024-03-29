<?php

namespace Drupal\arche_gui_api\Controller;

/**
 * Description of ArcheApiBaseController
 *
 * @author nczirjak
 */
class ArcheApiBaseController extends \Drupal\Core\Controller\ControllerBase
{
    protected $config;
    protected $repoDb;
    protected $siteLang;
    
    public function __construct()
    {
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language']) : $this->siteLang = "en";
        $this->config = \Drupal::service('extension.list.module')->getPath('acdh_repo_gui') . '/config/config.yaml';
        try {
            $this->repoDb = \acdhOeaw\arche\lib\RepoDb::factory($this->config);
        } catch (\Exception $ex) {
            \Drupal::messenger()->addWarning($this->t('Error during the BaseController initialization!').' '.$ex->getMessage());
            return array();
        }
    }
}
