<?php

namespace Drupal\arche_gui_api\Model\SmartSearch;

/**
 * Description of SMCoordinatesModel
 *
 * @author nczirjak
 */
class SMCoordinatesModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getData()
    {
      return $this->getCoordinates();
    }
    
    
    /**
     * Generate the entity box data
     *
     * @return array
     */
    private function getCoordinates(): array
    {
        $result = array();
        //run the actual query
        try {
            $this->setSqlTimeout();
            $query = $this->drupalDb->query(
                "
                select *
                from (
                select DISTINCT(mv.id),
                (select mv2.value from metadata as mv2 where mv2.id = mv.id and mv2.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasLatitude' and mv2.value IS NOT NULL AND mv2.value <> '') as lat,
                (select mv3.value from metadata as mv3 where mv3.id = mv.id and mv3.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasLongitude' and mv3.value IS NOT NULL AND mv3.value <> '') as lon,
                (select mv3.value from metadata as mv3 where mv3.id = mv.id and mv3.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasWKT' and mv3.value IS NOT NULL AND mv3.value <> '') as wkt,
                (select mv4.value from metadata as mv4 where mv4.id = mv.id and mv4.property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle' and mv4.value IS NOT NULL AND mv4.value <> '' limit 1) as name
                from metadata as mv 
                where 
                mv.property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' and mv.value = 'https://vocabs.acdh.oeaw.ac.at/schema#Place'
                ) as res where res.lat IS NOT NULL AND res.lat <> '' 
                "
            );
            $result = $query->fetchAll();
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
        }

        $this->closeDBConnection();
        return $result;
    }

    
}