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
            return new JsonResponse(array("Please provide a repoid string"), 204, ['Content-Type' => 'application/json']);
        }
        
        $object = new \Drupal\arche_gui_api\Object\VersionsObject();
        $content = $object->init($repoid, $lang);

        if (count($content) == 0) {
            return new JsonResponse(array("There is no resource"), 204, ['Content-Type' => 'application/json']);
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
            return new Response(array("Please provide a repoid string"), 204, ['Content-Type' => 'application/json']);
        }
        
        $blockModel = new \Drupal\acdh_repo_gui\Model\BlocksModel();
        $params = array('identifier' => $repoid, 'lang' => $lang);
        $data = $blockModel->getViewData("versions", $params);
        
        if (count((array) $data) < 2) {
            $data = [];
        } else {
            foreach ($data as $k => $v) {
                if ($v->id === $repoid) {
                    $data[$k]->actual = 'version-highlighted';
                    goto end;
                }
            }
        }
       
        end:
            
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
     * Get the alert div Id for the gui detail page header
     * @param string $repoid
     * @param string $lang
     * @return Response
     */
    public function getAlertDiv(string $repoid, string $lang = "en"): Response
    {
        /*
         * Usage:
         *  https://domain.com/browser/api/versions_alert/repoid/lang?_format=json
         */

        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
     
        if (empty($repoid)) {
            return new JsonResponse(array("Please provide a repoid string"), 204, ['Content-Type' => 'application/json']);
        }
        
        $blockModel = new \Drupal\acdh_repo_gui\Model\BlocksModel();
        $params = array('identifier' => $repoid, 'lang' => $lang);
        $data = $blockModel->getViewData("versions", $params);
        $content = null;
        if (count($data) > 0) {
            foreach ($data as $k => $o) {
                if (isset($o->id) && $o->id == $repoid) {
                    if (isset($o->previd) && !empty($o->previd) && !is_null($o->previd)) {
                        $content = $o->previd;
                    }
                }
            }
        }
       
        $build = [
            '#theme' => 'acdh-repo-gui-detail-versions-alert',
            '#result' => $content,
            '#cache' => ['max-age' => 0],
            '#attached' => [
                'library' => [
                    'acdh_repo_gui/repo-styles',
                ]
            ]
        ];
        return new Response(render($build));
    }
}
