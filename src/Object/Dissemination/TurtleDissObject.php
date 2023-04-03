<?php

namespace Drupal\arche_gui_api\Object\Dissemination;

/**
 * Description of GndPersonObject
 *
 * @author nczirjak
 */
class TurtleDissObject extends \Drupal\arche_gui_api\Object\MainObject
{
    public function init(string $repoid): string
    {
        return $this->request($repoid);
    }

    private function request(string $repoid): string
    {
        $client = new \GuzzleHttp\Client();
        try {
            $request = $client->request('GET', $this->repoDb->getBaseUrl() . $repoid . '/metadata', ['Accept' => ['application/n-triples']]);
            if ($request->getStatusCode() == 200) {
                return $this->processBody($request->getBody()->getContents());
            }
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            return "";
        } catch (\Exception $ex) {
            return "";
        }
    }

    private function processBody(string $body = ""): string
    {
        if (empty($body)) {
            return "";
        }
        if (class_exists('EasyRdf_Graph')) {
            $graph = new \EasyRdf_Graph();
        } else {
            $graph = new \EasyRdf\Graph();
        }
        $graph->parse($body);
        return $graph->serialise('turtle');
    }
}
