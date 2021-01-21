<?php
require_once(_PS_MODULE_DIR_.'egploader/classes/provider.php');

class raitonru extends provider{

    public function isProduct()
    {
        return ;
    }

    public function getProductName(){
        foreach ($this->parser->find('h1') as $e) {
            $p = trim($e->innertext);
        }
        return $p;
    }

    public function getProductDescription(){
        $description = "";
        foreach ($this->parser->find('div[itemprop=description] p') as $p){
                $description .= $p->outertext;
        }

        return $description;
    }

    public function getProductFeatures(){
        $features = "";
        $i=0;
        foreach ($this->parser->find('table.main-characteristics tr') as $tr){
            if($i>0)
                $features .="~~";
            $i=1;
            foreach($tr->find('td') as $td) {
                if($i == 1)
                    $features .= trim($td->plaintext);
                else
                    $features .= ":".trim($td->plaintext);
                $i++;
            }
            ;
        }

        return $features;
    }

    public function getProductConsistens(){
        return "";
    }

    public function getPrice(){
        return "999";
    }

    public function getPriceDiscount(){
        return "10";
    }

    public function getImages(){
        $images = "";
        $i=0;
        foreach ($this->parser->find('div.active div.gallery-slider a.image') as $a){
            if($i>0)
                $images .="~~";
            $images .= "https://raiton.ru".$a->href;
            $i++;
        }

        return $images;
    }

    public function getReviews(){
        return "";
    }

    public function getSitemap($type = "sitemap")
    {
        $result = array();
        foreach ($this->parser->find('loc') as $loc){
            $result[] = trim($loc->plaintext);
        }
        return $result;
    }
    public function getMetaTitle(){
        return "not implemented!!";
    }

    public function getMetaDescription(){
        return "not implemented!!";
    }

    public function getMetaKeywords(){
        return "not implemented!!";
    }

    public function getH1(){
        return "not implemented!!";
    }
}