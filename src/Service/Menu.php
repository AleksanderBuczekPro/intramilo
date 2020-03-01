<?php

namespace App\Service;

use voku\helper\HtmlDomParser;
use Doctrine\ORM\EntityManagerInterface;

class Menu{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;

    }
    

    function generate($html) {

        $html = HtmlDomParser::str_get_html($html);

        $headings = array();

        foreach ($html->find('h1') as $e) {

            // Extraction du h1
            $heading = html_entity_decode($e->plaintext);
            $headings[] = $heading;


        }

        return $headings;

    }

    function addLinks($html){

        $result = preg_replace('/(<h1\b[^><]*)>/i', '$1 style="xxxx:yyyy;">', $html);

        return $result;
    }


}