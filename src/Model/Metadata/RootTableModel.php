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
        $schema = $this->repo->getSchema();

        $conn = new \PDO($dbconnStr);
        $cfg = (object) [
                    'skipNamespace' => $this->repo->getBaseUrl() . '%', // don't forget the '%' at the end!
                    'ontologyNamespace' => $schema->namespaces->ontology,
                    'parent' => $schema->parent,
                    'label' => $schema->label,
                    'order' => $schema->ontology->order,
                    'cardinality' => $this->repo->getSchema()->namespaces->ontology . 'cardinality',
                    'recommended' => $schema->ontology->recommended,
                    'langTag' => $schema->ontology->langTag,
                    'vocabs' => $schema->ontology->vocabs,
                    'label' => 'http://www.w3.org/2004/02/skos/core#altLabel'
        ];
       
        $ontology = new \acdhOeaw\arche\lib\schema\Ontology($conn, $cfg);
        $classesDesc = [];
        foreach (['project', 'collection', 'topCollection', 'resource', 'metadata', 'publication', 'place', 'organisation', 'person'] as $i) {
            $classesDesc[$i] = $ontology->getClass($schema->classes->$i)->properties ?? "";
        }
        return $classesDesc;
    }
}
