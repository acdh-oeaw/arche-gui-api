<?php

namespace Drupal\arche_gui_api\Object\Child;

/**
 * Description of RPRObject
 *
 * @author nczirjak
 */
class ChildObject extends \Drupal\arche_gui_api\Object\MainObject
{
    protected $model;
    private $childProperties = [];
    private $rootType;
    private $childTitle;

    protected function createModel(): void
    {
        $this->model = new \Drupal\arche_gui_api\Model\Child\ChildModel();
    }

    public function getRootType(): string {
        return $this->rootType;
    }
    
    public function getChildTitle(): string {
        return $this->childTitle;
    }
    
    public function init(string $repoid, string $lang, array $searchProps): array
    {
        $this->createModel();
        
        //get the root
        $this->rootType = $this->model->getRootType($repoid);
        
        //get properties
        $this->checkChildProperties($this->rootType);
       
        return $this->processData($this->model->getData(
            $repoid,
            $this->childProperties,
            (int)$searchProps['offset'],
            (int)$searchProps['limit'],
            $searchProps['search'],
            (int)$searchProps['orderby'],
            $searchProps['order'],
            $lang
        ));
    }
    
    public function getActor(string $repoid, string $lang, array $searchProps): array
    {
       
        $this->createModel();
        return $this->processData($this->model->getData(
            $repoid,
            ['https://vocabs.acdh.oeaw.ac.at/schema#hasActor'],
            (int)$searchProps['offset'],
            (int)$searchProps['limit'],
            $searchProps['search'],
            (int)$searchProps['orderby'],
            $searchProps['order'],
            $lang
        ));
    }

   
    private function processData(array $data): array
    {
     
        $this->result = array();
        foreach ($data as $obj) {
            if (isset($obj->id) && isset($obj->title) && isset($obj->property)) {
                $this->result[] = array(
                    'title' => "<a id='archeHref' href='/browser/detail/$obj->id'>$obj->title</a>",
                    'property' => $obj->property,
                    'type' => $obj->type,
                    'accessres' => $obj->accessres,
                    'acdhid' => $this->repoDb->getBaseUrl().$obj->id,
                    'sumcount' => $obj->sumcount
                );
            }
        }
        return $this->result;
    }
    
    private function checkChildProperties(string $class)
    {
       
        $class= str_replace($this->repoDb->getSchema()->namespaces->ontology, "", $class);
        switch (strtolower($class)) {
            case 'https://vocabs.acdh.oeaw.ac.at/schema#Organisation':
                $this->childProperties = self::getOrganisationTypes();
                $this->childTitle = "Involved in";
                break;
            case 'publication':
                $this->childProperties = self::getPublicationTypes();
                $this->childTitle = "Related Resource(s)";
                break;
            case 'person':
                $this->childProperties = self::getPersonTypes();
                $this->childTitle = "Contributed to";
                break;
            case 'project':
                $this->childProperties = self::getProjectTypes();
                $this->childTitle = "Related Collection(s)";
                break;
            case 'concept':
                $this->childProperties = self::getConceptTypes();
                $this->childTitle = "Narrower(s)";
                break;
            case 'institute':
                $this->childProperties = self::getInstituteTypes();
                $this->childTitle = "Involved in";
                break;
            case 'place':
                $this->childProperties = self::getPlaceTypes();
                $this->childTitle = "Spatial Coverage in";
                break;
            default:
                $this->childProperties = self::getChildTypes();
                $this->childTitle = "Child Resource(s)";
                break;
        }
    }
    
    private function getOrganisationTypes(): array
    {
        return array(
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasContributor', $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasFunder',
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasOwner', $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasLicensor',
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasRightsHolder'
        );
    }

    private function getPublicationTypes(): array
    {
        return array(
            $this->repoDb->getSchema()->parent
        );
    }

    private function getPersonTypes(): array
    {
        return array(
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasContributor', $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasCreator',
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasAuthor', $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasEditor',
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasPrincipalInvestigator'
        );
    }

    private function getProjectTypes(): array
    {
        return array(
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasRelatedProject'
        );
    }

    private function getConceptTypes()
    {
        return array(
            'http://www.w3.org/2004/02/skos/core#narrower'
        );
    }

    private function getInstituteTypes(): array
    {
        return array(
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasMember'
        );
    }

    private function getPlaceTypes(): array
    {
        return array(
            $this->repoDb->getSchema()->__get('namespaces')->ontology . 'hasSpatialCoverage'
        );
    }

    private function getChildTypes(): array
    {
        return array(
            $this->repoDb->getSchema()->parent
        );
    }
}
