<?php

require_once(_PS_MODULE_DIR_.'egploader/classes/provider.php');

class askonaru  extends provider{

    public function isProduct()
    {
        return ;
    }

    public function getProductName(){

        return "";
    }

    public function getProductDescription(){

        return "";
    }

    public function getProductFeatures(){
        return "";
    }

    public function getPrice(){

        return "";
    }

    public function getPriceDiscount(){

        return "";
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