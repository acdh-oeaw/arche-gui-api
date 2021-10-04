<?php

namespace Drupal\arche_gui_api\Model;

/**
 * Description of MembersModel
 *
 * @author nczirjak
 */
class MembersModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getData(string $repoid): array
    {
        $result = array();
        //run the actual query
        try {
            $this->setSqlTimeout('10000');
            $query = $this->repodb->query(
                "SELECT * from gui.get_members_func(:repoid, :lang)",
                array(':repoid' => $repoid,
                        ':lang' => $this->siteLang)
            );
            
            $result = $query->fetchAll();
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
