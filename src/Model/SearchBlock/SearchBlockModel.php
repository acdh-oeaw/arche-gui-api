<?php

namespace Drupal\arche_gui_api\Model\SearchBlock;

/**
 * Description of CollectionModel
 *
 * @author nczirjak
 */
class SearchBlockModel extends \Drupal\arche_gui_api\Model\ArcheApiModel {
    public function __construct() {
        parent::__construct();
    }
    
    public function getData() {
        $entity = $this->getEntityData();
        $years = $this->getYearsData();
        $category = $this->getCategoryData();
       return ['entity' => $entity, 'year' => $years, 'category' => $category ];
    }
    
    
    /**
     * Generate the entity box data
     *
     * @return array
     */
    private function getEntityData(): array
    {
        $result = array();
        //run the actual query
        try {
            $this->setSqlTimeout();
            $query = $this->repodb->query(
                "
                select count(value), value
                from metadata 
                where property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
                and value LIKE 'https://vocabs.acdh.oeaw.ac.at/schema#%'
                group by value
                order by value asc"
            );
            $result = $query->fetchAll();
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        }

        $this->changeBackDBConnection();
        return $result;
    }

    /**
     * Generate the year box data
     *
     * @return array
     */
    private function getYearsData(): array
    {
        $result = array();
        //run the actual query
        try {
            $this->setSqlTimeout();
            $query = $this->repodb->query(
                "
                select
                    count(EXTRACT(YEAR FROM to_date(value,'YYYY'))), 
                    EXTRACT(YEAR FROM to_date(value,'YYYY')) as year
                from metadata 
                where property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasAvailableDate'
                group by year
                order by year desc"
            );
            $result = $query->fetchAll();
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        }

        $this->changeBackDBConnection();
        return $result;
    }
    
    private function getCategoryData(): array
    {
        $result = array();
        try {
            $this->setSqlTimeout();
            $query = $this->repodb->query(
                "select count(mv.value),  mv2.value, mv2.id
                from metadata_view as mv
                left join metadata_view as mv2 on mv2.id = CAST(mv.value as int)
                where mv.property = :category
                and mv2.property = :title
                and mv2.lang = :lang
                group by mv2.value, mv2.id
                order by mv2.value asc",
                array(
                    ':category' => $this->repo->getSchema()->__get('namespaces')->ontology.'hasCategory',
                    ':title' => $this->repo->getSchema()->__get('label'),
                    ':lang' => $this->siteLang
                    )
            );
            $result = $query->fetchAll();
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        }

        $this->changeBackDBConnection();
        return $result;
    }
    
    
    
}