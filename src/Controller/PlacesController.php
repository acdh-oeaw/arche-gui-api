<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of PersonsController
 *
 * @author nczirjak
 */
class PlacesController {

    public function __construct() {
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language']) : $this->siteLang = "en";

        $this->config = \Drupal::service('extension.list.module')->getPath('acdh_repo_gui') . '/config/config.yaml';

        try {
            $this->repo = \acdhOeaw\arche\lib\Repo::factory($this->config);
            $this->repodb = \acdhOeaw\arche\lib\RepoDb::factory($this->config);
        } catch (\Exception $ex) {
            \Drupal::messenger()->addWarning($this->t('Error during the BaseController initialization!') . ' ' . $ex->getMessage());
            return array();
        }
    }

    public function execute(string $searchStr): Response {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/places/MYVALUE?_format=json
         */

        if (empty($searchStr)) {
            return new JsonResponse(array("Please provide a search string"), 404, ['Content-Type' => 'application/json']);
        }

        $object = new \Drupal\arche_gui_api\Object\PlacesObject($searchStr);
        $object->init();

        if (count($object->getData()) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }
        return new JsonResponse(json_encode($object->getData()), 200, ['Content-Type' => 'application/json']);
    }

}
