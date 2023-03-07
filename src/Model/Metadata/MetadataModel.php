<?php

namespace Drupal\arche_gui_api\Model\Metadata;

/**
 * Description of MetadataGuiModel
 *
 * @author nczirjak
 */
class MetadataModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getData(string $lang, array $searchProp): array
    {
        $result = array();

        //run the actual query
        try {
            $this->setSqlTimeout('60000');
            
            $query = $this->repodb->query(
                "SELECT 
                    id, title, avdate, string_agg(DISTINCT description, '.') as description, acdhid
                from gui.root_views_func( :lang ) 
                where title is not null
                group by id, title, avdate, acdhid
                order by " . $searchProp['orderby'] . $searchProp['order'] . " limit " . $searchProp['limit'] . " offset " . $searchProp['offset'] . "
                 ; ",
                array(
                        ':lang' => $lang
                    )
            );

            //$this->sqlResult = $query->fetchAll();
           
            $result = $query->fetchAll(\PDO::FETCH_OBJ);
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
