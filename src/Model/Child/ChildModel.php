<?php

namespace Drupal\arche_gui_api\Model\Child;

/**
 * Description of RPRModel
 *
 * @author nczirjak
 */
class ChildModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    private $sqlTypes = "";
    public function __construct()
    {
        parent::__construct();
    }
    
     
    /**
     * Fetch the root type to we can generate the child template
     * @param string $repoid
     * @return string
     */
    public function getRootType(string $repoid) : string
    {
        try {
            $this->setSqlTimeout('10000');
            $query = $this->drupalDb->query(
                "select value from metadata where property = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' and 
                id = :repoid order by property limit 1; ",
                array(
                        ':repoid' => $repoid
                )
            );
            
            return $query->fetchField();
        } catch (Exception $ex) {
            \Drupal::logger('arche_gui_api')->notice($ex->getMessage());
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('arche_gui_api')->notice($ex->getMessage());
        }
        $this->closeDBConnection();
        return "";
    }
    
    public function getData(string $repoid, array $sqlTypes, int $offset, int $limit, string $search = "", int $orderby = 1, string $order = 'asc', string $lang = "en"): array
    {
        $result = array();
        //run the actual query
     
        if (count($sqlTypes) > 0) {
            $this->formatTypeFilter($sqlTypes);
        } else {
            $this->sqlTypes = "ARRAY[]::text[]";
        }
       
        try {
            $this->setSqlTimeout('70000');
            $query = $this->drupalDb->query(
                "select title, id, property, type, accessres, sumcount from gui.get_child_table_func(:repoid, :lang, $this->sqlTypes)"
                    . " where LOWER(title) like  LOWER('%' || :search || '%') "
                    . " order by $orderby $order "
                    . " limit :limit offset :offset;",
                array(
                        ':repoid' => $repoid,
                        ':lang' => $lang,
                        ':search' => $search,
                        ':limit' => $limit,
                        ':offset' => $offset
                ),
                ['allow_delimiter_in_query' => true, 'allow_square_brackets' => true]
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
    
    private function formatTypeFilter(array $sqlTypes): void
    {
        $count = count($sqlTypes);
        if ($count > 0) {
            $this->sqlTypes .= 'ARRAY [ ';
            $i = 0;
            foreach ($sqlTypes as $t) {
                $this->sqlTypes .= "'$t'";
                if ($count - 1 != $i) {
                    $this->sqlTypes .= ', ';
                } else {
                    $this->sqlTypes .= ' ]';
                }
                $i++;
            }
        }
    }
}
