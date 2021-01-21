<?php

abstract class provider
{

    public $content;
    public $parser;
    public $id_load;
    public $id_product;
    public $noprod;
    public $extractDataResult;
    public $discount;

    public function __construct($content, $id_load)
    {
        $this->id_load = $id_load;
        $this->noprod = false;
        $this->content = $content;//mb_convert_encoding($content, 'utf-8', mb_detect_encoding($content));;
        $this->parser = new simple_html_dom();
        $this->parser->load($this->content);//$this->parser->load($this->content);
        $this->discount = 0;
        $this->isProduct();

    }

    public  function getAllData(){
        $this->extractDataResult['id_load'] = $this->id_load;

        $this->extractDataResult['product_name'] = $this->getProductName();
        $this->extractDataResult['page_type'] = ($this->isProduct())?'NOPROD':'PRODUCT';
        $this->extractDataResult['product_images'] = $this->getImages();

        $this->extractDataResult['meta_title'] = $this->getMetaTitle();
        $this->extractDataResult['meta_description'] = $this->getMetaDescription();
        $this->extractDataResult['meta_keywords'] = $this->getMetaKeywords();

        $this->extractDataResult['h1'] = $this->getH1();
        $this->extractDataResult['description'] = $this->getProductDescription();

        $this->extractDataResult['price_discount'] = $this->getPriceDiscount();
        $this->extractDataResult['price'] = $this->getPrice();

        $this->extractDataResult['features'] = $this->getProductFeatures();
        $this->extractDataResult['consistens'] = $this->getProductConsistens();
        $this->extractDataResult['reviews'] = $this->getReviews();

        return $this->extractDataResult;
    }

    abstract public function isProduct();

    abstract public function getProductName();

    abstract public function getProductDescription();

    abstract public function getProductFeatures();

    abstract public function getProductConsistens();

    abstract public function getPrice();

    abstract public function getPriceDiscount();

    abstract public function getImages();

    abstract public function getReviews();

    abstract public function getSitemap($type = "sitemap");

    abstract public function getMetaTitle();

    abstract public function getMetaDescription();

    abstract public function getMetaKeywords();

    abstract public function getH1();
}