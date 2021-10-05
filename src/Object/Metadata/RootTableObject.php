<?php

namespace Drupal\arche_gui_api\Object\Metadata;

/**
 * Description of RootTableObject
 *
 * @author nczirjak
 */
class RootTableObject extends \Drupal\arche_gui_api\Object\MainObject {

    protected $model;
    private $lang;

    public function __construct(string $lang) {
        parent::__construct();
        $this->lang = $lang;
    }

    protected function createModel(): void {
        $this->model = new \Drupal\arche_gui_api\Model\Metadata\RootTableModel();
    }

    public function init(): array {
        $this->createModel();
        return $this->processData($this->model->getOntology());
    }

    private function processData(array $data): array {
        $helper = new \Drupal\arche_gui_api\Helper\Metadata\MetadataGuiHelper();
        return array($helper->getRootTable($data));
    }

}
