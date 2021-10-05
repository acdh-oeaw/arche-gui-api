<?php

namespace Drupal\arche_gui_api\Model\Ontology;

/**
 * Description of OntolgyJsModel
 *
 * @author nczirjak
 */
class OntolgyJsModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
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
            $this->setSqlTimeout('10000');
            $query = $this->repodb->query(
                "SELECT * from gui.count_binaries_collection_func();"
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
