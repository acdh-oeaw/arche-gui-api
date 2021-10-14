<?php

namespace Drupal\arche_gui_api\Model;

/**
 * Description of GndPersonsModel
 *
 * @author nczirjak
 */
class GndPersonsModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getData(): array
    {
        $result = array();
        //run the actual query
        try {
            $this->setSqlTimeout();
            $query = $this->repodb->query(
                "select 
			DISTINCT(mv.id) as repoid, i.ids as gnd 
                    from metadata_view as mv
                    left join identifiers as i on mv.id = i.id 
                    where
                        mv.property = :type
                        and mv.value = :value
                        and i.ids like '%gnd%';",
                array(
                    ':type' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
                    ':value' => $this->repo->getSchema()->namespaces->ontology.'Person'
                ),
            );
            $result = $query->fetchAll(\PDO::FETCH_CLASS);
        } catch (Exception $ex) {
            \Drupal::logger('arche_gui_api')->notice($ex->getMessage());
            $result = array();
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('arche_gui_api')->notice($ex->getMessage());
            $result = array();
        }
        $this->changeBackDBConnection();
        return $result;
    }
}
