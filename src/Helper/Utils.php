<?php

/**
 * Description of Utils
 *
 * @author nczirjak
 */
class Utils
{
    private $config;
    private $repo;
    
    public function __construct($cfg = null)
    {
        ($cfg && is_string($cfg)) ?  $this->config = $cfg : $this->config = \Drupal::service('extension.list.module')->getPath('acdh_repo_gui').'/config/config.yaml';
        $this->repo = \acdhOeaw\arche\lib\Repo::factory($this->config);
    }
}
