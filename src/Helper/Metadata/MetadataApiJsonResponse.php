<?php

namespace Drupal\arche_gui_api\Helper\Metadata;

class MetadataApiJsonResponse
{
    private static $RDF_PROPS = [
        'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle',
        'https://vocabs.acdh.oeaw.ac.at/schema#hasAvailableDate',
        'https://vocabs.acdh.oeaw.ac.at/schema#TopCollection'
    ];
    
    public function getResponse(\Drupal\acdh_repo_gui\Object\ResourceObject $obj)
    {
    }
}
