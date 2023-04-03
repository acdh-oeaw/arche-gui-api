<?php

namespace Drupal\arche_gui_api\Model\Metadata;

/**
 * Description of RootTableModel
 *
 * @author nczirjak
 */
class RootTableModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOntology(): array
    {
        $dbconnStr = yaml_parse_file(\Drupal::service('extension.list.module')->getPath('acdh_repo_gui') . '/config/config.yaml')['dbConnStr']['guest'];
        $schema = $this->repoDb->getSchema();

        $conn = new \PDO($dbconnStr);
        $cfg = (object) [
                    'ontologyNamespace' => $schema->namespaces->ontology,
                    'parent' => $schema->parent,
                    'label' => $schema->label,
        ];
       
        $ontology = new \acdhOeaw\arche\lib\schema\Ontology($conn, $cfg);
      
        $classesDesc = [];
        foreach (['project', 'collection', 'topCollection', 'resource', 'metadata', 'publication', 'place', 'organisation', 'person'] as $i) {
            $classesDesc[$i] = $ontology->getClass($schema->classes->$i)->properties ?? "";
        }
        return $classesDesc;
    }
}
