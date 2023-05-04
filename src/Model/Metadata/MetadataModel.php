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
            
            $query = $this->drupalDb->query(
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
           
            $result = $query->fetchAll(\PDO::FETCH_OBJ);
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $result = array();
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $result = array();
        }

        $this->closeDBConnection();
        return $result;
    }
    
    
    /**
     * Root view API Model
     * @param string $lang
     * @param array $searchProp
     * @return array
     */
    public function getRootData(string $lang, array $searchProp): array
    {
        try {
            $this->setSqlTimeout();
            $query = $this->drupalDb->query(
                "SELECT 
                    title, avdate, id, string_agg(DISTINCT description, '.') as description, acdhid,
                    (select count(rv.id) from gui.root_views_func(:lang ) as rv ) as sumcount
                from gui.root_views_func( :lang ) 
                where title is not null
                group by id, title, avdate, acdhid
                 order by " . $searchProp['orderby'] . $searchProp['order'] . " limit " . $searchProp['limit'] . " offset " . $searchProp['offset'] . "; ",
                array(
                        ':lang' => $this->siteLang
                    )
            );

            $this->sqlResult = $query->fetchAll();
           
            $this->closeDBConnection();
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $this->closeDBConnection();
            return array();
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $this->closeDBConnection();
            return array();
        }
        return $this->sqlResult;
    }
    
    
    /**
     * Detail view overview data
     * @param string $identifier
     * @param string $lang
     * @return array
     */
    public function getOverviewData(string $identifier, string $lang = "en"): array
    {
        $result = array();
        try {
            $this->setSqlTimeout();
            $query = $this->drupalDb->query(" select id, property, type, value, relvalue, acdhid, vocabsid, accessrestriction, language, lastname from gui.detail_view_func(:id, :lang) order by property, lastname, relvalue, value", array(':id' => $identifier, ':lang' => $this->siteLang));
            $result = $query->fetchAll();
            $this->closeDBConnection();
        } catch (\Exception $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $this->closeDBConnection();
            return array();
        } catch (\Drupal\Core\Database\DatabaseExceptionWrapper $ex) {
            \Drupal::logger('acdh_repo_gui')->notice($ex->getMessage());
            $this->closeDBConnection();
            return array();
        }
        return $result;
    }
}
