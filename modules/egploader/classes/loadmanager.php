<?php

require_once(_PS_MODULE_DIR_.'egploader/classes/simple_html_dom.php');

class LoadManager
{
    public static $column_mask;
    public $entities = array();
    public $available_fields = array();
    public $required_fields = array();

    public $cache_image_deleted = array();

    public static $default_values = array();



    public $provider;
    public $id_product;
    public $id_category;
    public $id_manufacturer;
    public $actions;
    public $content;
    public $line;
    public $load_result = array();
    public $image_counter;
    public $id_load;
    public $id_load_old;
    public $content_cache;
    public $product;
    public $combinations;
    public $extractDataResult;
    public $no_cache;

    public function __construct($no_cache=0)
    {
        $this->image_counter = 0;
        $this->no_cache = $no_cache;
    }

    public function setVariables($id_load, $provider, $id_product){
        $res = ploader::getPloader($id_load);
        foreach ($res as $r) {
            //$this->id_load = $r['id_load'];
            //$this->url = $r['url'];
            //$this->id_pproduct = $r['id_pproduct'];
            //$this->url_product_name = $r['url_product_name'];
            $this->provider = $r['provider'];
        }
       // $this->provider = $provider;
        $this->id_product = $id_product;
        $this->id_load = $id_load;
        $this->image_counter = 0;
    }

    public static function getProxy()
    {
            $sql = "SELECT id_proxy, proxy FROM " . _DB_PREFIX_ . "egploader_proxy where active=1";
            $proxys = Db::getInstance()->executeS($sql);

            $count = count($proxys);
            if ($count > 0)
                return $proxys[rand(0, $count-1)];
            else
                return false;
    }

    public function getUrl()
    {
        $sql = "select * from " . _DB_PREFIX_ . "egploader WHERE id_load=". $this->id_load;
        $r = Db::getInstance()->executeS($sql);

        return $r[0]['url'];
    }

    public function getContent($url, $proxy = null, $cookie_use = false){
        if($this->id_load != $this->id_load_old)
        {
            $content = false;
            $save_dir = _PS_MODULE_DIR_ . 'egploader/content/' .$this->provider;
            if(!is_dir($save_dir )) {
                mkdir($save_dir , 0777, true);
            }

            if ($url == "" && $this->no_cache==1) {
                $url = $this->getUrl();
            }

            if ($url == "") {
                $this->content = file_get_contents($save_dir.'/'.$this->provider."_".$this->id_load.'.txt', $content);
                $this->content_cache = true;
            }
            else
            {

                $ch = curl_init( $url );
                $proxy = $this->getProxy();
                if ($proxy){
                    curl_setopt($ch, CURLOPT_PROXY, $proxy['proxy']);
                }

                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $user_agent = "Mozilla/5.0 (X11; Linux i686; rv:24.0) Gecko/20140319 Firefox/24.0 Iceweasel/24.4.0";
                curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
               // curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // переходит по редиректам
                curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
                if ($cookie_use)
                {
                    curl_setopt($ch, CURLOPT_COOKIEFILE, _PS_MODULE_DIR_.'egploader/content/cookie.txt');
                }

                $content = curl_exec( $ch );
                if(!$content)
                {
                    $sql = "UPDATE " . _DB_PREFIX_ . "egploader_proxy SET failcon = failcon+1 WHERE id_proxy=".$proxy['id_proxy'];
                    Db::getInstance()->executeS($sql);
                }else
                {
                    $sql = "UPDATE " . _DB_PREFIX_ . "egploader_proxy SET okcon = okcon+1 WHERE id_proxy=".$proxy['id_proxy'];
                    Db::getInstance()->executeS($sql);
                }
                curl_close( $ch );

                $this->content = $content;//mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
                if ($this->id_load)
                    if($this->content)
                        file_put_contents($save_dir.'/'.$this->provider."_".$this->id_load.'.txt', $content);
            }
        }
    }

    public function saveImages()
    {
        foreach (explode(",",$this->extractDataResult['product_images']) as $url)
            if($url != "")
                $this->saveImage($url);
    }

    public function updateLoadsField($id_load, $field_name, $value)
    {
        //update
        $sql = "update "._DB_PREFIX_."egploader
				set
				`".$field_name."` = '".addslashes($value)."' 
				where id_load = ".(int)$id_load;

        if (!$links = Db::getInstance()->execute($sql))
            return false;
    }

    public function saveImage($url)
    {
        $save_dir = _PS_MODULE_DIR_.'egploader/content/'.$this->provider.'/images';
        if(!is_dir($save_dir )) {
            mkdir($save_dir , 0777, true);
        }
        $save_dir .='/'.$this->provider.'_'.$this->id_load.'_'.$this->image_counter.".".$this->getImageExtensin($url);
        file_put_contents($save_dir, file_get_contents($url));
        $this->image_counter++;
    }

    public function getImagesCache($images)
    {
        $load_dir = "";
        foreach (explode(",", $images) as $image)
        {
            if($this->image_counter>0)
                $load_dir .= ",";

            $load_dir .=  Tools::getShopProtocol().Tools::getHttpHost().'/modules/egploader/content/'.$this->provider.'/images/'.$this->provider.'_'.$this->id_load.'_'.$this->image_counter.".".$this->getImageExtensin($image);
            $this->image_counter++;
        }
        return $load_dir;
    }

    public function getImageExtensin($url)
    {
        $ext = end(explode(".", $url));
        return $ext;
    }

    public function getDataFromSitemap($type)
    {
        require_once(_PS_MODULE_DIR_ . 'egploader/classes/providers/' . $this->provider . '.php');

        $converter = new $this->provider($this->content, $this->id_load);

        return $converter->getSitemap($type);
    }

    public function extractData()
    {
        require_once(_PS_MODULE_DIR_.'egploader/classes/providers/'.$this->provider.'.php');

        $converter = new $this->provider($this->content, $this->id_load);

        $this->extractDataResult = $converter->getAllData();

        return $this->extractDataResult;
    }

    public function reviewsSave(){

    }

    public function consistenceSave()
    {

        foreach ($this->extractDataResult['consistens'] as $key => $consist)
        {

            // add to map
            $sql = "SELECT * FROM " . _DB_PREFIX_ . "egploader_product_consists_item WHERE provider='".$this->provider."' AND name='".$consist['name']."'";
            $r = Db::getInstance()->executeS($sql);
            if(!$r) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "egploader_product_consists_item(`provider`, `name`, `description`, `image_url`) 
                            VALUES ('".$this->provider."','".$consist['name']."','".$consist['description']."','".$consist['image_url']."')";
                $r = Db::getInstance()->executeS($sql);
            }

            $sql = "SELECT id_consists_item FROM " . _DB_PREFIX_ . "egploader_product_consists_item WHERE provider='".$this->provider."' AND name='".$consist['name']."'";
            $id_consists_item = Db::getInstance()->getValue($sql);

            if($id_consists_item>0){

                $sql = "SELECT * FROM " . _DB_PREFIX_ . "egploader_product_consists WHERE id_load=".$this->extractDataResult['id_load']." AND id_consists_item=".$id_consists_item." AND position=".$key;
                $r = Db::getInstance()->executeS($sql);

                if(!$r) {
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "egploader_product_consists(`id_load`, `id_consists_item`, `position`) VALUES (".$this->extractDataResult['id_load'].",".$id_consists_item.", ".$key.")";
                    $r = Db::getInstance()->execute($sql);
                }

            }
        }
    }

    public  static function getConsistions($id_product, $id_load){

        $sql="UPDATE ps_egploader_product_consists c
                INNER JOIN ps_egploader l ON c.id_load=l.id_load
                INNER JOIN ps_egploader_product lp ON l.id_pproduct=lp.id_pproduct
                SET c.id_product = 0
              WHERE lp.id_product=".$id_product;
        $r = Db::getInstance()->execute($sql);

        $sql="UPDATE ps_egploader_product_consists c
                SET c.id_product = ".$id_product."
              WHERE c.id_load=".$id_load;
        $r = Db::getInstance()->execute($sql);


    }

    public static function getProductFeatures($id_pproduct, $id_load = null)
    {
        // get features
       // $sql="";
       // $features = Db::getInstance()->executeS($sql);

        //foreach ($features as $feature)
       // {
       //     $sql="UPDATE";
       //     Db::getInstance()->executeS($sql);
       // }

        $sql = "SELECT fm.id_feature, fm.id_feature_value FROM " . _DB_PREFIX_ . "egploader_product p
                        INNER JOIN " . _DB_PREFIX_ . "egploader l ON p.id_pproduct = l.id_pproduct
                        INNER JOIN " . _DB_PREFIX_ . "egploader_product_feature f on f.id_load = l.id_load
                        INNER JOIN " . _DB_PREFIX_ . "egploader_product_feature_map fm ON f.id_load_feature_map = fm.id_load_feature_map
                    WHERE p.id_product = ".$id_pproduct."
                    AND fm.id_feature > 0
                    AND f.active = 1
                    AND fm.active = 1
                    AND fm.id_feature_value > 0 ";

        $r = Db::getInstance()->executeS($sql);
        return $r;
    }

    public function featuresSave()
    {
        foreach ($this->extractDataResult['features'] as $feature)
        {
            // delete old features by load
            //$sql = "DELETE FROM " . _DB_PREFIX_ . "egploader_product_feature WHERE feature_load_name=".$this->extractDataResult['id_load'];
            //$r = Db::getInstance()->executeS($sql);

            // add to map
            $sql = "SELECT * FROM " . _DB_PREFIX_ . "egploader_product_feature_map WHERE provider='".$this->provider."' AND feature_load_name='".$feature['name']."' AND feature_load_value='".$feature['value']."'";
            $r = Db::getInstance()->executeS($sql);
            if(!$r) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "egploader_product_feature_map(`provider`, `feature_load_name`, `feature_load_value`) VALUES ('".$this->provider."','".$feature['name']."','".$feature['value']."')";
                $r = Db::getInstance()->executeS($sql);
            }

            $sql = "SELECT id_load_feature_map FROM " . _DB_PREFIX_ . "egploader_product_feature_map WHERE provider='".$this->provider."' AND feature_load_name='".$feature['name']."' AND feature_load_value='".$feature['value']."'";
            $id_load_feature_map = Db::getInstance()->getValue($sql);

            if($id_load_feature_map>0){

                $sql = "SELECT * FROM " . _DB_PREFIX_ . "egploader_product_feature WHERE id_load=".$this->extractDataResult['id_load']." AND id_load_feature_map=".$id_load_feature_map."";
                $r = Db::getInstance()->executeS($sql);

                if(!$r) {
                    $sql = "INSERT INTO " . _DB_PREFIX_ . "egploader_product_feature(`id_load`, `id_load_feature_map`, `load_datetime`) VALUES (".$this->extractDataResult['id_load'].",".$id_load_feature_map.", NOW())";
                    $r = Db::getInstance()->execute($sql);
                }

            }

        }

    }

    public function saveDataInDB()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "egploader_product_data WHERE id_load=".(int)$this->extractDataResult['id_load'];
        $r = Db::getInstance()->executeS($sql);
        if($r)
        {
            //update
            $sql = "UPDATE " . _DB_PREFIX_ . "egploader_product_data
            SET
            page_type = '".$this->extractDataResult['page_type']."', 
            product_name = '".$this->extractDataResult['product_name']."',
            product_images = '".$this->extractDataResult['product_images']."',
            meta_title = '".$this->extractDataResult['meta_title']."',
            meta_description = '".$this->extractDataResult['meta_description']."',
            meta_keywords = '".$this->extractDataResult['meta_keywords']."',
            h1 = '".$this->extractDataResult['h1']."',
            description = '".$this->extractDataResult['description']."',
            price = ".$this->extractDataResult['price'].",
            price_discount = ".$this->extractDataResult['price_discount'].",
            price_array = '".$this->extractDataResult['price_array']."'
            WHERE id_load=".(int)$this->extractDataResult['id_load'];
        }
        else
        {
            // insert
            $sql = "INSERT INTO " . _DB_PREFIX_ . "egploader_product_data(`id_load`, `page_type`, `product_name`,`product_images`
                                ,`meta_title`,`meta_description`,`meta_keywords`,`h1`
                                ,`description`,`price`,`price_discount`,`price_array`) VALUES (".$this->extractDataResult['id_load'].", '".$this->extractDataResult['page_type']."','".$this->extractDataResult['product_name']."'
                                , '".$this->extractDataResult['product_images']."', '".$this->extractDataResult['meta_title']."', '".$this->extractDataResult['meta_description']."'
                                , '".$this->extractDataResult['meta_keywords']."', '".$this->extractDataResult['h1']."', '".$this->extractDataResult['description']."', ".$this->extractDataResult['price']."
                                , ".$this->extractDataResult['price_discount'].", '".$this->extractDataResult['price_array']."')";
        }
        $r = Db::getInstance()->execute($sql);
    }



}
