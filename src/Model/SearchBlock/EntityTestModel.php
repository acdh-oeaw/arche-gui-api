<?php

namespace Drupal\arche_gui_api\Model\SearchBlock;

/**
 * Description of CollectionModel
 *
 * @author nczirjak
 */
class EntityTestModel extends \Drupal\arche_gui_api\Model\ArcheApiModel
{
    public function __construct()
    {
        parent::__construct();
    }
    
    private function getAllRdfType(): array
    {
        $schema = $this->repo->getSchema();
        $searchTerm = new \acdhOeaw\arche\lib\SearchTerm($schema->namespaces->rdfs . 'type');
        $searchCfg = new \acdhOeaw\arche\lib\SearchConfig();
        $searchCfg->metadataMode = 'resource';
        $searchCfg->orderBy = ['https://vocabs.acdh.oeaw.ac.at/schema#hasTitle'];
        $searchCfg->orderByLang = 'en';
        
        $pdoStmt = $this->repoliDB->getPdoStatementBySearchTerms([$searchTerm], $searchCfg);

        # define context
        $result = [];

        while ($triple = $pdoStmt->fetchObject()) {
            if ($triple->property === $schema->namespaces->rdfs . 'type') {
                if (!in_array($triple->value, $result) && strpos($triple->value, 'https://vocabs.acdh.oeaw.ac.at/schema#') !== false) {
                    $result[] = $triple->value;
                }
            }
        }
      
        echo "<pre>";
        var_dump($result);
        echo "</pre>";

        die();
        return [];
    }

    public function getData(): array
    {
        $this->getAllRdfType();
        
        $schema = $this->repo->getSchema();

        //id: $schema->id
        //ispartof: $schema->parent
        //rdf:
        // $schema->namespaces->rdfs.'type'

        $searchTerm = new \acdhOeaw\arche\lib\SearchTerm($schema->namespaces->rdfs . 'type', 'https://vocabs.acdh.oeaw.ac.at/schema#TopCollection');
        $searchCfg = new \acdhOeaw\arche\lib\SearchConfig();
        $searchCfg->metadataMode = 'resource';
        $searchCfg->orderBy = ['https://vocabs.acdh.oeaw.ac.at/schema#hasTitle'];
        $searchCfg->orderByLang = 'en';
        
        $pdoStmt = $this->repoliDB->getPdoStatementBySearchTerms([$searchTerm], $searchCfg);

        # define context
        $matchProp = $schema->searchMatch;
        $context = [
            'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle' => 'title',
            'search://order' => 'order',
            'search://orderValue1' => 'orderValue',
            'search://count' => 'count'
        ];

        echo "<pre>";
        var_dump("ALL COUNT");
        var_dump($searchCfg->count);
        echo "</pre>";

        $nodes=[];
        while ($triple = $pdoStmt->fetchObject()) {
            
            # $triple is an object with properties id, property, type, lang, value
            # skip RDF properties for which we don't know the mapping
            if (!isset($context[$triple->property])) {
                continue;
            }
            
            echo "<pre>";
            var_dump($triple);
            echo "</pre>";
            
            # map prop name according to the context
            $prop = $context[$triple->property];

            # if the triple points to another node in the graph, maintain the reference
            if ($triple->type === 'REL') {
                if (!isset($nodes[$triple->value])) {
                    $nodes[$triple->value] = (object) ['__id__' => $triple->value];
                }
            }

            # manage the data
            if (!isset($nodes[$triple->id])) {
                $nodes[$triple->id] = (object) ['__id__' => $triple->id];
            }
            if (!isset($nodes[$triple->id]->$prop)) {
                $nodes[$triple->id]->$prop = [];
            }
            switch ($triple->type) {
                case 'ID':
                    $nodes[$triple->id]->$prop[] = $triple->value;
                    break;
                case 'REL':
                    $nodes[$triple->id]->$prop[] = $nodes[$triple->value];
                    break;
                default:
                    $nodes[$triple->id]->$prop[(string) $triple->lang] = $triple->value;
            }
        }
        /*
         *
        $nodes = array_combine(
  array_map(fn($x) => $x->order, $nodes),
  $nodes
)
ksort($nodes);

*/
        # find and display nodes matching the search
        //$matches = array_filter($nodes, fn($x) => isset($x->__match__));
      
        /*
                echo "<pre>";
                var_dump($nodes);
                var_dump($searchCfg->count);
                echo "</pre>";
        */
        die();

        $requestUrl = $this->repo->getBaseUrl() . "search?"
                . $this->getQueryPropVal() . ""
                . "&lang[]=" . $this->siteLang;

        try {
            $graph = new \EasyRdf\Graph();
            $res = $graph->newAndLoad($requestUrl)->toRdfPhp();
            echo "<pre>";
            var_dump($res);
            echo "</pre>";

            die();
        } catch (\Exception | \EasyRdf\Exception $ex) {
            die($ex->getMessage());
            \Drupal::logger('arche_rest_gui')->notice($ex->getMessage());
            return array();
        }
        return array();
    }

    private function getQueryPropVal(): string
    {
        return http_build_query(
            array(
                    'property' => array(
                        $this->repo->getSchema()->__get('namespaces')->rdfs . "type",
                        $this->repo->getSchema()->__get('namespaces')->ontology . "hasIdentifier"
                    ),
                    'value' => array(
                        $this->repo->getSchema()->__get('namespaces')->ontology . 'Person',
                        'gnd'
                    ),
                    'operator' => array('=', '~')
                )
        );
    }
}
