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
        $dbconnStr = yaml_parse_file(\Drupal::service('extension.list.module')->getPath('acdh_repo_gui').'/config/config.yaml')['dbConnStr']['guest'];
        
        $conn = new \PDO($dbconnStr);
        $cfg = (object) [
                    'skipNamespace' => $this->repo->baseUrl() . '%', // don't forget the '%' at the end!
                    'ontologyNamespace' => $this->repo->getSchema()->namespaces->ontology,
                    'parent' => $this->repo->getSchema()->namespaces->ontology.'isPartOf',
                    'label' => $this->repo->getSchema()->namespaces->ontology.'hasTitle',
                    'order' => $this->repo->getSchema()->namespaces->ontology.'ordering',
                    'cardinality' => $this->repo->getSchema()->namespaces->ontology.'cardinality',
                    'recommended' => $this->repo->getSchema()->namespaces->ontology.'recommendedClass',
                    'langTag' => $this->repo->getSchema()->namespaces->ontology.'langTag',
                    'vocabs' => $this->repo->getSchema()->namespaces->ontology.'vocabs',
                    'label' => 'http://www.w3.org/2004/02/skos/core#altLabel'
        ];

        $ontology = new \acdhOeaw\arche\lib\schema\Ontology($conn, $cfg);
        $project =  $this->getOntologyDataByProperty($ontology, 'Project');
        $collection =  $this->getOntologyDataByProperty($ontology, 'Collection');
        $topCollection =  $this->getOntologyDataByProperty($ontology, 'TopCollection');
        $resource =  $this->getOntologyDataByProperty($ontology, 'Resource');
        $metadata =  $this->getOntologyDataByProperty($ontology, 'Metadata');
        $image =  $this->getOntologyDataByProperty($ontology, 'Image');
        $publication =  $this->getOntologyDataByProperty($ontology, 'Publication');
        $place =  $this->getOntologyDataByProperty($ontology, 'Place');
        $organisation =  $this->getOntologyDataByProperty($ontology, 'Organisation');
        $person =  $this->getOntologyDataByProperty($ontology, 'Person');
      
        return array(
            'project' => $project,
            'topcollection' => $topCollection, 'collection' => $collection,
            'resource' => $resource, 'metadata' => $metadata,
            'publication' => $publication,
            'place' => $place, 'organisation' => $organisation,
            'person' => $person
        );
    }

    private function getOntologyDataByProperty(\acdhOeaw\arche\lib\schema\Ontology &$ontology, string $property): mixed
    {
        return (isset($ontology->getClass($this->repo->getSchema()->namespaces->ontology.$property)->properties)) ?
                $ontology->getClass($this->repo->getSchema()->namespaces->ontology.$property)->properties : "";
    }
}
