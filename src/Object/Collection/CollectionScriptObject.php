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
        $schema = $this->repo->getSchema();
        $replace = [
            "{ingest.location}" => $schema->ingest->location,
            "{fileName}" => $schema->fileName,
            "{parent}" => $schema->parent,
            "{metadataReadMode}" => $this->repo->getHeaderName('metadataReadMode'),
            "{searchMatch}" => $schema->searchMatch,
            "{resourceUrl}" => $repoUrl,
        ];
        $text = str_replace(array_keys($replace), array_values($replace), $text);
        return $text;
    }
}
