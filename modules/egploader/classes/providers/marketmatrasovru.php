<?php

require_once(_PS_MODULE_DIR_.'egploader/classes/provider.php');

class marketmatrasovru  extends provider{

    public function isProduct()
    {
        foreach ($this->parser->find('div.product-header-container') as $n){
            if($n->itemtype == "http://schema.org/Product")
                return;
        }
        return $this->noprod = true;
    }

    public function getProductName(){
        foreach ($this->parser->find('h1') as $e) {
            $p = trim($e->innertext);
        }

        return ($this->noprod)?"NOPROD-".$p:$p;
    }

    public function getProductDescription(){

        return "";
    }

    public function getProductFeatures(){
        return "";
    }

    public function getPrice(){

        return 0;
    }

    public function getPriceDiscount(){

        return 0;
    }

    public function getImages(){
        return "";
    }

    public function getProductConsistens(){

        return "";
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