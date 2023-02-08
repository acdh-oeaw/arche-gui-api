<?php

namespace Drupal\arche_gui_api\Helper\FileFormats;

/**
 * Description of MetadataGuiHTMLHelper
 *
 * @author norbertczirjak
 */
class FileFormatsHTMLHelper
{
    private static $fields = ["EXTENSION", "FORMAT NAME & VERSION", "PREFERENCE"];
    private static $categories = [
        "https://vocabs.acdh.oeaw.ac.at/archecategory/text" => "TEXT DOCUMENTS",
        "https://vocabs.acdh.oeaw.ac.at/archecategory/audioVisual" => "AUDIO / VIDEO",
        "https://vocabs.acdh.oeaw.ac.at/archecategory/image" => "IMAGE",
        "https://vocabs.acdh.oeaw.ac.at/archecategory/dataset" => "DATASET",
        "https://vocabs.acdh.oeaw.ac.at/archecategory/3dData" => "3D DATA"
        ];
    private $categoriesData = [];
    private $data;
    
    /**
     * Fetch the whole table
     * @param array $data
     * @return string
     */
    public function fetchHtmlContent(array $data): string
    {
        $this->data = $data;
        $html = $this->createHeader();
        
        $this->fetchCategoriesData();
        
        $html .= $this->createRows();
        $html .= '</tbody></table><br>';
        
        return $html;
    }
    
    /**
     * Loop through the data array to reorder the data based on the categories
     * @return void
     */
    private function fetchCategoriesData(): void
    {
        foreach ($this->data as $k => $v) {
            if (key_exists($v->getCategory(), $this::$categories)) {
                $this->categoriesData[$this::$categories[$v->getCategory()]][] = $v;
            }
        }
    }
    
    /**
     * fetch the table rows
     * @param array $data
     * @return string
     */
    private function createRows(): string
    {
        $html = "";
      
        foreach ($this->categoriesData as $key => $val) {
            $html .= '<tr class="table-row-acdhBlue"><td colspan="3" style="">'.$key.'</td></tr>';
           
            foreach ($val as $obj) {
                $html .="<tr>";
                $html .= '<td style="text-align:right">'.implode(", ", $obj->getExtensions()).'</td>';
                $html .= '<td>'.$obj->getName().'</td>';
                $html .= '<td>'.$obj->getConformance().'</td>';
                $html .="</tr>";
            }
        }
        return $html;
    }
    
    /**
     * Create the Header for the HTML table
     * @return string
     */
    private function createHeader(): string
    {
        $str = '<table class="format-table" style="width:99%;">';
        $str .= '<thead><tr class="table-firstrow">';
        foreach ($this::$fields as $field) {
            $str .= '<th style="text-align: center;">'.$field.'</th>';
        }
        $str .= '</tr>';
        $str .= '</thead>';
        $str .= '<tbody style=" word-break: break-word;">';
        return $str;
    }
}
