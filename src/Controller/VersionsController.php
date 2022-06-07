<?php

namespace Drupal\arche_gui_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of VersionsController
 *
 * @author nczirjak
 */
class VersionsController
{
    /**
     * Create Object for the versions tree view
     * @param string $repoid
     * @param string $lang
     * @return JsonResponse
     */
    public function execute(string $repoid, string $lang = "en"): JsonResponse
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/v2/versions/repoid/lang?_format=json
         */

        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
         
        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a repoid string"), 404, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\VersionsObject();
        $content = $object->init($repoid, $lang);

        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 404, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse($content, 200, ['Content-Type' => 'application/json']);
    }
    
   
    /**
     * Create the normal list view API for the versions
     * @param string $repoid
     * @param string $lang
     * @return Response
     */
    public function executeList(string $repoid, string $lang = "en"): Response
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/versions_list/repoid/lang?_format=json
         */

        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
     
        if (empty($repoid)) {
            return new Response(array("Please provide a repoid string"), 404, ['Content-Type' => 'application/json']);
        }
        
        $blockModel = new \Drupal\acdh_repo_gui\Model\BlocksModel();
        $params = array('identifier' => $repoid, 'lang' => $lang);
        $data = $blockModel->getViewData("versions", $params);
        
        if (count((array) $data) > 1) {
            if ($data[0]->id == $repoid) {
                $data = [];
            }
        } else {
            $data = [];
        }
       
        $build = [
            '#theme' => 'acdh-repo-gui-detail-versions-block',
            '#result' => (array) $data,
            '#cache' => ['max-age' => 0],
            '#attached' => [
                'library' => [
                    'acdh_repo_gui/repo-styles',
                ]
            ]
        ];
       
        return new Response(render($build));
    }
    
    
    
    /**
     * Check if the actual resource has a newer version
     * @param string $id
     * @return bool
     */
    private function checkVersions(string $id): string
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        if (count((array) $data) > 1) {
            if ($data[0]->id != $id) {
                return $data[0]->id;
            }
        }
        return "";
    }
}
