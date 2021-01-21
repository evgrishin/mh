<?php

/**

 */

require_once(_PS_MODULE_DIR_.'egploader/classes/loadmanager.php');
require_once(_PS_MODULE_DIR_.'egploader/classes/pproduct.php');
require_once(_PS_MODULE_DIR_.'egms/classes/filter.php');

class ploader extends ObjectModel
{
	/** @var string Name */
	public $id_load;
	public $url;
	public $page_type;
	public $id_pproduct;
	public $url_product_name;
	public $provider;
	public $id_category;
	public $id_manufacturer;
    public $load_datetime;
    public $active;

    public $url_arrays = array();

	/**
	 * @see ObjectModel::$definition
	 */
	//TODO: add check fot length of fields
	public static $definition = array(
		'table' => 'egploader',
		'primary' => 'id_load',
		'fields' => array(
			'id_load' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'url' => array('type' => self::TYPE_STRING, 'required' => true),
            'page_type' => array('type' => self::TYPE_STRING, 'required' => false),
			'id_pproduct' => array('type' => self::TYPE_INT, 'required' => false),
			'url_product_name' => array('type' => self::TYPE_STRING, 'required' => false),
			'provider' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_category' => array('type' => self::TYPE_INT, 'required' => false),
            'id_manufacturer' => array('type' => self::TYPE_INT, 'required' => false),
            'load_datetime' => array('type' => self::TYPE_DATE, 'required' => false),
            'active' => array('type' => self::TYPE_INT, 'required' => false),
		),
	);



	public function delete()
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'egploader
		WHERE id_load ='.(int)$this->id_load;
		
		if (!Db::getInstance()->executeS($sql))
			return(false);
		
		return (parent::delete());
	}
	
	public static function getPloader($id_load=null)
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'egploader';
		if ($id_load != null)
			$sql.= ' WHERE id_load='.(int)$id_load;
		$sql .= ' ORDER BY id_load';
			
		return (Db::getInstance()->executeS($sql));
	}

    public static function getPloadersByProduct($id_pproduct)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egploader
         WHERE id_pproduct ='.(int)$id_pproduct;
        $sql .= ' ORDER BY url_product_name';

        return (Db::getInstance()->executeS($sql));
    }

	public function fillPloader($id_load)
    {
        $res = ploader::getPloader($id_load);
        foreach ($res as $r)
        {
            $this->id_load = $r['id_load'];
            $this->url = $r['url'];
            $this->id_pproduct = $r['id_pproduct'];
            $this->url_product_name = $r['url_product_name'];
            $this->provider = $r['provider'];
            $this->id_category = $r['id_category'];
            $this->id_manufacturer = $r['id_manufacturer'];
            $this->load_datetime = $r['load_datetime'];
            $this->active = $r['active'];
        }
    }

    public static function getProvidersDir()
    {
        $res = array();

        $dir  = _PS_MODULE_DIR_.'egploader/classes/providers/';
        $files = scandir($dir,1 );
        foreach ($files as $file) {
            if (strpos($file, ".php")) {
                $file = str_replace(".php", "", $file);
                $res[$file] = $file;
            }
        }
        return $res;
    }

    public function getErrors()
    {
        $err = 0;
        if($this->id_manufacturer == 0)
            $err = 1;
        if($this->id_category == 0)
            $err = 1;

        return $err;
    }

    public function create_product($id_load = null)
    {
        if($this->id_pproduct == 0) {

            if ($id_load) {
                $this->fillPloader($id_load);

                if($this->getErrors())
                    die('SKIPED: no category or manufacturer for '.$this->id_load.'<br>');

                $id_pproduct = pproduct::addPProduct($this->url_product_name, $this->id_load, $this->id_category, $this->id_manufacturer);
                $this->updateLoadsField($id_load, "id_pproduct", $id_pproduct, 'int');
                $load_datetime = date("Y-m-d H:i:s");
                $this->updateLoadsField($id_load,'load_datetime' , $load_datetime );
                //$this->save();
                // return error to ajax
                die("Create PProduct: " . $id_pproduct . "; " . $this->load_datetime . " ; load: " . $this->id_load . "; " . $this->provider . "; <a href='" . $this->url . "'>" . $this->url . "</a>; " . $this->url_product_name . "; " . $this->page_type . "; - ok<br>");
            } else
                die("id_load not defined<br>");
        }else
            die("SKIPPED: Product defined<br>");
    }

    public function updateLoadsField($id_load, $field_name, $value, $type='string')
    {
        //update
        $sql = "update "._DB_PREFIX_."egploader
				set
				`".$field_name."` = ";
        if($type == 'int')
            $sql .="".addslashes($value)."";
        else
            $sql .="'".addslashes($value)."'";

        $sql .=" where id_load = ".(int)$id_load;


        if (!$links = Db::getInstance()->execute($sql))
            return false;
    }

    public function create_cache($id_load = null)
    {
        if($id_load) {
            $this->fillPloader($id_load);
            $loader = new LoadManager();
            $loader->setVariables($this->id_load, $this->provider, 0);

            $loader->getContent($this->url, "proxy");
            if ($loader->content) {
                $loader->extractData();
                $loader->saveImages();
                $loader->featuresSave();
                $loader->consistenceSave();
                $loader->reviewsSave();
                $loader->saveDataInDB();
                $this->url_product_name = $loader->extractDataResult['product_name'];
                $this->page_type = $loader->extractDataResult['page_type'];
            } else
                $this->page_type = 'FAIL';

            $this->load_datetime = date("Y-m-d H:i:s");

            $loader->updateLoadsField($this->id_load, "url_product_name", $this->url_product_name);
            $loader->updateLoadsField($this->id_load, "page_type", $this->page_type);
            $loader->updateLoadsField($this->id_load, "load_datetime", date("Y-m-d H:i:s"));

            // return error to ajax
            die($this->load_datetime . " ; load: " . $this->id_load . "; " . $this->provider . "; <a href='".$this->url."'>" .$this->url. "</a>; " . $this->url_product_name . "; " . $this->page_type . "; - ok<br>");
        }
        else
            die("id_load not defined");
    }

    public function update($null_values = false)
    {
        if (Tools::getValue('create_pproduct'))
            $this->id_pproduct = pproduct::addPProduct($this->url_product_name, $this->id_load);

        if (Tools::getValue('create_filter'))
        {
            $params = $this->getLoaderData($this->id_load);
            $this->id_pproduct = egmsfilter::addFilter($params);
            $this->page_type = 'FILTER';
        }
        return parent::update($null_values);

    }

    public function getLoaderData($id_load)
    {
        $sql = "SELECT * from " . _DB_PREFIX_ . "egploader_product_data where id_load = ".$id_load;
        $row = Db::getInstance()->executeS($sql);
        return $row[0];
    }

    public function getUrlsFromSitemap($url)
    {

        $loader = new LoadManager();
        $loader->setVariables($this->id_load, $this->provider, $this->id_pproduct);
        if(substr($url, 0, 4) == "http")
            $loader->getContent($url);
        else
            $loader->content = $url;
        return $loader->getDataFromSitemap();
    }

    public static function addLoadProduct($url, $provider){

        $sql = "INSERT INTO " . _DB_PREFIX_ . "egploader(`url`, `provider`,`url_product_name`, `page_type`) VALUES ('".$url."','".$provider."', '', 'NEW')";

        return Db::getInstance()->execute($sql);

    }

    public static function productExist($url){
        $sql = "SELECT * from " . _DB_PREFIX_ . "egploader where url = '".$url."'";
        $row = Db::getInstance()->executeS($sql);
        if (!$row)
            return false;
        else
            return true;
    }

    public function add($autodate = true, $null_values = false, $val)
    {
        $this->page_type = "NEW";
        if (Tools::getValue('sitemap_url')){
            foreach ($this->getUrlsFromSitemap(trim($this->url)) as $url_from_map) {
                $this->url = $url_from_map;
                if (!ploader::productExist($this->url))
                    $result = parent::add($autodate, $null_values);
            }
        }else {
            $this->url_arrays = explode(PHP_EOL, $this->url);
            foreach ($this->url_arrays as $url) {
                $this->url = $url;
                if (!ploader::productExist($this->url))
                    $result = parent::add($autodate, $null_values);
            }
        }

        return $result;
    }


}
