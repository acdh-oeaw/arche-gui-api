<?php

namespace Drupal\arche_gui_api\Object;

class FileFormatObject {
    private $name;
    private $category;
    private $conformance;
    private $extensions = [];
    private $obj;
    
    public function getName() {
        return $this->name;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getConformance() {
        return $this->conformance;
    }

    public function getExtensions() {
        return $this->extensions;
    }

    private function setName(string $name): void {
        $this->name = $name;
    }

    private function setCategory(string $category): void {
        $this->category = $category;
    }

    private function setConformance(string $conformance): void {
        $this->conformance = $conformance;
    }

    private function setExtensions(array $extensions): void {
        $this->extensions = $extensions;
    }

    public function __construct(object $obj) {
        $this->obj = $obj;
        $this->init();
    }
    
    private function init(): void {
        if(isset($this->obj->name)) {
            $this->setName($this->obj->name);
        }
        if(isset($this->obj->ARCHE_category)) {
            $this->setCategory($this->obj->ARCHE_category);
        }
        if(isset($this->obj->ARCHE_conformance)) {
            $this->setConformance($this->obj->ARCHE_conformance);
        }
        if(isset($this->obj->extensions)) {
            $this->setExtensions($this->obj->extensions);
        }
    }
    
    public function isValid(): bool {
        if(!empty($this->getName()) && !empty($this->category) && !empty($this->conformance) && count($this->getExtensions()) > 0) {
            return true;
        }
        return false;
    }
    
    public function getCategoryData(string $category): array {
        foreach($this->obj as $k => $v) {
            echo "<pre>";
            var_dump($k);
            var_dump($v);
            echo "</pre>";
        }
        
        die();
    }
    
}
