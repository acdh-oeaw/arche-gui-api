<?php

namespace Drupal\arche_gui_api\Model\Detail;

/**
 * Description of RPRModel
 *
 * @author nczirjak
 */
class RPRModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getData(string $repoid, string $lang = "en"): array
    {
        $result = array();
        //run the actual query
        try {
            $this->setSqlTimeout('10000');
            $query = $this->drupalDb->query(
                "select * from gui.related_publications_resources_views_func(:repoid, :lang)",
                array(
                    ':repoid' => $repoid,
                    ':lang' => $lang
                )
            );
            
            $result = $query->fetchAll(\PDO::FETCH_CLASS);
        } catch (Exception $ex) {
            \Drupal::logger('arche_gui_api')->notice($ex->getMessage());
            $result = array();
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('arche_gui_api')->notice($ex->getMessage());
            $result = array();
        }
        $this->closeDBConnection();
        return $result;
    }
}
