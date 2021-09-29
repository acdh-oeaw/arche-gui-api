<?php

namespace Drupal\arche_gui_api\Model\Collection;

/**
 * Description of CollectionModel
 *
 * @author nczirjak
 */
class CollectionModel extends \Drupal\arche_gui_api\Model\ArcheApiModel {

    public function __construct() {
        parent::__construct();
    }

    public function getData(string $repoid, string $lang): array {
        $result = array();

        //run the actual query
        try {
            $this->setSqlTimeout('60000');
            $query = $this->repodb->query(
                    "select * from  gui.collection_v2_views_func(:id, :lang) order by title;",
                    array(
                        'id' => $repoid,
                        'lang' => $lang
                    )
            );
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $result = array();
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $result = array();
        }

        $this->changeBackDBConnection();
        return $result;
    }
    
    

}
