<?php

namespace Drupal\arche_gui_api\Controller\Dissemination;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of TurtleDissController
 *
 * @author nczirjak
 */
class TurtleDissController extends \Drupal\Core\Controller\ControllerBase
{
    /**
     * It has no public endpoint. Collection download uses it.
     * @param string $repoid
     * @return string
     */
    public function execute(string $repoid): string
    {
        $repoid = \Drupal\Component\Utility\Xss::filter(preg_replace('/[^0-9]/', '', $repoid));
        
        if (empty($repoid)) {
            return "";
        }
        $object = new \Drupal\arche_gui_api\Object\Dissemination\TurtleDissObject();
        return $object->init($repoid);
    }
}
