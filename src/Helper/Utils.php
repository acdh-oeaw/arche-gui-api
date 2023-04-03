<?php

namespace Drupal\arche_gui_api\Helper;

/**
 * Description of Utils
 *
 * @author nczirjak
 */
class Utils
{
    private $config;
    private $repoDb;
    private $rdfProps;
    private $properties;
    private $data;
    private $siteLang;
    
    public function __construct($cfg = null)
    {
        ($cfg && is_string($cfg)) ?  $this->config = $cfg : $this->config = \Drupal::service('extension.list.module')->getPath('acdh_repo_gui').'/config/config.yaml';
        $this->repoDb = \acdhOeaw\arche\lib\RepoDb::factory($this->config);
        $this->properties = $this->rdfProps();
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language'])  : $this->siteLang = "en";
    }
    
    private function rdfProps(): array
    {
        return [
            'version' => array('shortcut' => 'acdh:hasVersion', 'property' => $this->repoDb->getSchema()->__get('namespaces')->ontology. "hasVersion"),
            'acdhtype' => array('shortcut' => 'rdf:type', 'property' => $this->repoDb->getSchema()->__get('namespaces')->rdfs . "type"),
            'accesres' => array('shortcut' => 'acdh:hasAccessRestriction', 'property' => $this->repoDb->getSchema()->__get('namespaces')->ontology."hasAccessRestriction"),
            'description' => array('shortcut' => 'acdh:hasDescription', 'property' => $this->repoDb->getSchema()->__get('namespaces')->ontology."hasDescription"),
            'avdate' => array('shortcut' => 'acdh:hasAvailableDate', 'property' => $this->repoDb->getSchema()->creationDate),
            'title' => array('shortcut' => 'acdh:hasTitle', 'property' => $this->repoDb->getSchema()->label),
            'id' => array('shortcut' => 'acdh:hasIdentifier', 'property' => $this->repoDb->getSchema()->__get('id')),
            'acdhid' => array('shortcut' => 'acdh:hasIdentifier', 'property' => $this->repoDb->getSchema()->__get('id'))
        ];
    }
    
    public function convertSqlToRdfProps(array $data, string $lang): array
    {
        $obj = [];
        foreach ($data as $k => $v) {
            $this->formatResultToGui($v);
            if (array_key_exists($k, $this->properties)) {
                $obj[$this->properties[$k]['shortcut']][$lang][] = $v;
            }
        }
        return $obj;
    }
    
    private function fetchProperties(string $k, object $v, string $lang): void
    {
        foreach ($this->properties as $pk => $pv) {
            if (isset($v->$pk)) {
                $title = $v->$pk;
                
                if ($v->$pk == 'accesres') {
                    $title = str_replace("https://vocabs.acdh.oeaw.ac.at/archeaccessrestrictions/", "", $v->$pk);
                }
                
                $this->data[$k][$pv['shortcut']][$lang] = array(
                    $this->createObj(
                        $v->id,
                        $pv['property'],
                        $title,
                        $v->$pk
                    )
                );
            }
        }
    }
    
    private function setLanguage(object &$v): string
    {
        if (isset($v->language)) {
            if (!empty($v->language)) {
                return $v->language;
            }
        }
        return $this->siteLang;
    }
    
    private function addTopCollectionProperty(string $lang, int $k, object &$v): void
    {
        $this->data[$k]['rdf:type'][$lang] = array(
            $this->createObj(
                $v->id,
                $this->repoDb->getSchema()->namespaces->rdfs.'type',
                $this->repoDb->getSchema()->__get('namespaces')->ontology. "TopCollection",
                $this->repoDb->getSchema()->__get('namespaces')->ontology. "TopCollection"
            )
        );
    }
    
    private function createObj(int $id, string $property, string $title, string $value): object
    {
        $obj = new \stdClass();
        $obj->id = $id;
        $obj->property = $property;
        $obj->title = $title;
        $obj->value = $value;
        return $obj;
    }
    
    public function formatResultToGui(array $data)
    {
        if (count((array) $data) > 0) {
            foreach ($data as $k => $v) {
                $lang = $this->setLanguage($v);
              
                if (isset($v->id)) {
                    $this->fetchProperties($k, $v, $lang);
                    $this->addTopCollectionProperty($lang, $k, $v);
                }
            }
        }
        return $this->data;
    }
}
