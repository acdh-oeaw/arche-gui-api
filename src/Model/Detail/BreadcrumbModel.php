<?php

namespace Drupal\arche_gui_api\Model\Detail;

/**
 * Description of BreadcrumbModel
 *
 * @author nczirjak
 */
class BreadcrumbModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getData(string $identifier = ''): array
    {
        if (empty($identifier)) {
            return array();
        }

        $result = [];
        try {
            $this->setSqlTimeout();
            //run the actual query
            $query = $this->drupalDb->query(" select * from gui.breadcrumb_view_func(:id, :lang) order by depth desc ", array(':id' => $identifier, ':lang' => $this->siteLang));
            $result = $query->fetchAll();
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
}
