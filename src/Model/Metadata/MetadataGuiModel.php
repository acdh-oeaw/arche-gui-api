<?php

namespace Drupal\arche_gui_api\Model\Metadata;

/**
 * Description of MetadataGuiModel
 *
 * @author nczirjak
 */
class MetadataGuiModel extends \Drupal\arche_gui_api\Model\ArcheApiModel {

    public function __construct() {
        parent::__construct();
    }

    public function getOntology(): array {
        $dbconnStr = yaml_parse_file(\Drupal::service('extension.list.module')->getPath('acdh_repo_gui').'/config/config.yaml')['dbConnStr']['guest'];
        $conn = new \PDO($dbconnStr);
        $cfg = (object) [
            //'skipNamespace'     => $this->properties->baseUrl.'%', // don't forget the '%' at the end!
            'ontologyNamespace' => $this->repo->getSchema()->namespaces->ontology,
            'parent'            => $this->repo->getSchema()->namespaces->ontology.'isPartOf',
            'label'             => $this->repo->getSchema()->namespaces->ontology.'hasTitle',
            //'order'             => 'https://vocabs.acdh.oeaw.ac.at/schema#ordering',
            //'cardinality'       => 'https://vocabs.acdh.oeaw.ac.at/schema#cardinality',
            //'recommended'       => 'https://vocabs.acdh.oeaw.ac.at/schema#recommendedClass',
            //'langTag'           => 'https://vocabs.acdh.oeaw.ac.at/schema#langTag',
            //'vocabs'            => 'https://vocabs.acdh.oeaw.ac.at/schema#vocabs',
            //'altLabel'          => 'http://www.w3.org/2004/02/skos/core#altLabel'
        ];
       
        $ontology = new \acdhOeaw\arche\lib\schema\Ontology($conn, $cfg);
        
        $collectionProp = $ontology->getClass($this->repo->getSchema()->namespaces->ontology.'Collection')->properties;
        $projectProp = $ontology->getClass($this->repo->getSchema()->namespaces->ontology.'Project')->properties;
        $resourceProp = $ontology->getClass($this->repo->getSchema()->namespaces->ontology.'Resource')->properties;
        
        return array('collection' => $collectionProp, 'project' => $projectProp, 'resource' => $resourceProp);
    }

}
