<?php

namespace Drupal\arche_gui_api\Helper\Metadata;

/**
 * Description of MetadataGuiHTMLHelper
 *
 * @author norbertczirjak
 */
class MetadataGuiHTMLHelper {
 
    /**
     * Fetch the whole table
     * @param array $data
     * @return string
     */
    public function fetchHtmlContent(array $data): string {
        $html = $this->createHeader();
        $html .= $this->createRows($data['properties']['basic']) . '<tr class="table-row-acdhBlue"><td colspan="6" style="text-align">ACTORS INVOLVED</td></tr>';
        $html .= $this->createRows($data['properties']['actors_involved']) . '<tr class="table-row-acdhBlue"><td colspan="6" style="text-align">COVERAGE</td></tr>';
        $html .= $this->createRows($data['properties']['coverage']) . '<tr class="table-row-acdhBlue"><td colspan="6" style="text-align">RIGHTS & ACCESS</td></tr>';
        $html .= $this->createRows($data['properties']['right_access']) . '<tr class="table-row-acdhBlue"><td colspan="6" style="text-align">DATES</td></tr>';
        $html .= $this->createRows($data['properties']['dates']) . '<tr class="table-row-acdhBlue"><td colspan="6" style="text-align">RELATIONS TO OTHER PROJECTS, COLLECTIONS OR RESOURCES</td></tr>';
        $html .= $this->createRows($data['properties']['relations_other_projects']) . '<tr class="table-row-acdhBlue"><td colspan="6" style="text-align">CURATION, AUTOMATIC</td></tr>';
        $html .= $this->createRows($data['properties']['curation']) . '</tbody></table><br>';
       
        return $html;
    }
    
    /**
     * fetch the table rows
     * @param array $data
     * @return string
     */
    private function createRows(array $data): string {
        ksort($data);
        $rows ="";
        foreach($data as $key => $val) {
            foreach($val as $k => $v) {
                //project
                $project = isset($val[$k]['project']) ? $val[$k]['project'] : '-';     
                //topcollection
                //$topCollection = $val[$k]['topCollection'] ? $val[$k]['topCollection'] : '-';
                //collection
                $collection = isset($val[$k]['collection']) ? $val[$k]['collection'] : '-';        
                //resource
                $resource = isset($val[$k]['resource']) ? $val[$k]['resource'] : '-';
                
                if( $project === "-" && $collection === "-" && $resource === "-" ) {
                    //has no class
                } else {
                    //property
                    $rows .= '<tr><td>'.$k.'</td>';
                    //machine name
                    $rows .= '<td>'.$val[$k]['basic_info']['machine_name'].'</td>';
                    $rows .= '<td style="text-align: center;">'.$project.'</td>';
                    //$rows .= '<td style="text-align: center;">'.$topCollection.'</td>';
                    $rows .= '<td style="text-align: center;">'.$collection.'</td>';
                    $rows .= '<td style="text-align: center;">'.$resource.'</td>';
                }
                
            }
        }
        
        return $rows;
    }
    
    
    /**
     * Create the Header for the HTML table
     * @return string
     */
    private function createHeader(): string {
        $str = '<table class="metadata-table" style="max-width:99%;">';
        $str .= '<thead><tr class="table-firstrow">';
        $str .= '<th style="text-align: left;">PROPERTY</th> '
                . '<th style="text-align: left;">MACHINE NAME</th> '
                . '<th style="text-align: center;">PROJECT</th> '
               // . '<th style="text-align: center;">TOP<br>COLLECTION</th>  '
                . '<th style="text-align: center;">COLLECTION</th> '
                . '<th style="text-align: center;">RESOURCE</th>'
                . '</tr>';
        $str .= '</thead>';
        $str .= '<tbody style=" word-break: break-word;">';
        return $str;
    }
}