<?php

namespace Drupal\arche_gui_api\Object\Collection;

/**
 * Description of CollectionScriptObject
 *
 * @author nczirjak
 */
class CollectionScriptObject extends \Drupal\arche_gui_api\Object\MainObject
{
    public function init(string $repoUrl): string
    {
        return $this->processData($this->repo->getBaseUrl() .$repoUrl);
    }

    private function processData(string $repoUrl): string
    {
        try {
            $text = @file_get_contents(\Drupal::request()->getSchemeAndHttpHost() . '/browser/sites/default/files/coll_dl_script/collection_download_repo.py');
           
            return $this->changeText($text, $repoUrl);
        } catch (\Exception $e) {
            return "";
        }
    }

    private function changeText(string $text, string $repoUrl): string
    {
        if (strpos($text, '{ingest.location}') !== false) {
            $text = str_replace("{ingest.location}", $this->repo->getSchema()->ingest->location, $text);
        }

        if (strpos($text, '{fileName}') !== false) {
            $text = str_replace("{fileName}", $this->repo->getSchema()->fileName, $text);
        }

        if (strpos($text, '{parent}') !== false) {
            $text = str_replace("{parent}", $this->repo->getSchema()->parent, $text);
        }
        if (strpos($text, '{metadataReadMode}') !== false) {
            $text = str_replace("{metadataReadMode}", 'X-METADATA-READ-MODE', $text);
        }

        if (strpos($text, 'args = args.parse_args()') !== false) {
            $text = str_replace("args = args.parse_args()", "args = args.parse_args(['" . $repoUrl . "', '--recursive'])", $text);
        }
        return $text;
    }
}
