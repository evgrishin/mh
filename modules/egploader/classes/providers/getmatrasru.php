<?php

require_once(_PS_MODULE_DIR_.'egploader/classes/provider.php');

class getmatrasru extends provider{


    public function isProduct()
    {
        foreach ($this->parser->find('div.m-lot__inner') as $n){
            if($n->itemtype == "http://schema.org/Product")
                return;
        }
        return $this->noprod = true;
    }

    public function getProductName(){

        foreach ($this->parser->find('h1') as $e) {
            $p = trim($e->innertext);
        }

        return $p;
    }

    public function getProductDescription(){
        $description = "";
        foreach ($this->parser->find('div.m-lot-description__content p') as $p){
            $description .= $p->outertext;
        }
        return $description;
    }

    public function getProductFeatures(){
        $features = "";
        $i=0;
        foreach ($this->parser->find('div.m-options__row') as $tr){
            if($i>0)
                $features .="~~";
            foreach($tr->find('div') as $td) {
                if($td->class=="m-options__row-name")
                    $features .= trim($td->plaintext);
                if($td->class=="m-options__row-value")
                    $features .= trim($td->plaintext);
            }
            $i++;
        }

        return $features;
    }

    public function getProductConsistens(){
        return "";
    }

    public function getPrice(){
        return "";
    }

    public function getPriceDiscount(){
        return "";
    }

    public function getImages(){
        $images = "";
        $i=0;
        foreach ($this->parser->find('div.m-lot__top-slider div.m-lot__top-slider-item img') as $img){
            if($i>0)
                $images .=",";
            $images .= $img->src;
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