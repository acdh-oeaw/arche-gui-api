<?php

namespace Drupal\arche_gui_api\Model\Metadata;

/**
 * Description of MetadataGuiModel
 *
 * @author nczirjak
 */
class MetadataGuiModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOntology(): array
    {
        $dbconnStr = yaml_parse_file(\Drupal::service('extension.list.module')->getPath('acdh_repo_gui').'/config/config.yaml')['dbConnStr']['guest'];
        $schema = $this->repo->getSchema();
        
        $conn = new \PDO($dbconnStr);
        $cfg = (object) [
            //'skipNamespace'     => $this->properties->baseUrl.'%', // don't forget the '%' at the end!
            'ontologyNamespace' => $schema->namespaces->ontology,
            'parent'            => $schema->parent,
            'label'             => $schema->label
        ];
       
        $ontology = new \acdhOeaw\arche\lib\schema\Ontology($conn, $cfg);        
        $classesDesc = [];
        foreach (['collection', 'topCollection', 'resource', 'project', 'person', 'publication', 'place', 'organisation'] as $i) {
            $classesDesc[$i] = $ontology->getClass($schema->classes->$i)->properties ?? "";
        }
        return $classesDesc;
    }
}
