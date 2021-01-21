<?php

/**

 */

require_once(_PS_MODULE_DIR_.'egploader/classes/productloader.php');

class pproduct extends ObjectModel
{
    /** @var string Name */
    public $id;
    public $id_pproduct;
    public $name;
    public $id_product;
    public $id_category;
    public $id_manufacturer;
    public $id_attr_group1;
    public $id_attr_group2;
    public $load_datetime;
    public $product_name;
    public $description;
    public $price;
    public $images;
    public $features;
    public $consistions;
    public $rewiews;
    public $url_arrays = array();

    /**
     * @see ObjectModel::$definition
     */
    //TODO: add check fot length of fields
    public static $definition = array(
        'table' => 'egploader_product',
        'primary' => 'id_pproduct',
        'fields' => array(
            'id_pproduct' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'name' => array('type' => self::TYPE_STRING, 'required' => false),
            'id_product' => array('type' => self::TYPE_INT, 'required' => false),
            'id_category' => array('type' => self::TYPE_INT, 'required' => true),
            'id_manufacturer' => array('type' => self::TYPE_INT, 'required' => true),
            'id_attr_group1' => array('type' => self::TYPE_INT, 'required' => false),
            'id_attr_group2' => array('type' => self::TYPE_INT, 'required' => false),
            'load_datetime' => array('type' => self::TYPE_DATE, 'required' => false),
            'product_name' => array('type' => self::TYPE_INT, 'required' => false),
            'description' => array('type' => self::TYPE_INT, 'required' => false),
            'price' => array('type' => self::TYPE_INT, 'required' => false),
            'images' => array('type' => self::TYPE_INT, 'required' => false),
            'features' => array('type' => self::TYPE_INT, 'required' => false),
            'consistions' => array('type' => self::TYPE_INT, 'required' => false),
            'rewiews' => array('type' => self::TYPE_INT, 'required' => false),
        ),
    );
    public function __construct($id = null, $name=null, $id_load = null, $id_category=0, $id_manufacturer=0){

        parent::__construct($id);
        if (!$id&&$name!=null){
            $this->name = $name;
            $this->id_category = $id_category;
            $this->id_manufacturer = $id_manufacturer;
            $this->product_name = $id_load;
            $this->description = $id_load;
            $this->price = $id_load;
            $this->images = $id_load;
            $this->features = $id_load;
            $this->consistions = $id_load;
            $this->rewiews = $id_load;
            $this->save();
        }
    }



    public function delete()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egploader_product
		WHERE id_pproduct ='.(int)$this->id_pproduct;

        if (!Db::getInstance()->executeS($sql))
            return(false);

        return (parent::delete());
    }

    public static function getPproduct($id_pproduct=null)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egploader_product';
        if ($id_pproduct != null)
            $sql.= ' WHERE id_pproduct='.(int)$id_pproduct;
        $sql .= ' ORDER BY id_pproduct';

        return (Db::getInstance()->executeS($sql));
    }

    public function updateDatetime($id_pproduct)
    {
        $sql = "UPDATE " . _DB_PREFIX_ . "egploader_product SET load_datetime = now() where id_pproduct = ".$id_pproduct;

        return Db::getInstance()->execute($sql);
    }

    public function update($null_values = false)
    {
        if (Tools::getValue('create_product')&&Tools::getValue('id_product')==0)
            $this->id_product = ProductLoader::addProduct($this);
        $params = array();
        $params['product_name_load'] = Tools::getValue('product_name_load');
        $params['description_load'] = Tools::getValue('description_load');
        $params['price_load'] = Tools::getValue('price_load');
        $params['images_load'] = Tools::getValue('images_load');
        $params['features_load'] = Tools::getValue('features_load');
        $params['consistions_load'] = Tools::getValue('consistions_load');
        $params['product_meta_load'] = Tools::getValue('product_meta_load');
        $params['rewiews_load'] = Tools::getValue('rewiews_load');
        $no_cache = 0;//
        if (Tools::getValue('create_product')&&Tools::getValue('id_product')>0)
        {
            ProductLoader::updateProduct($this, $params, $no_cache);

        }

        if (Tools::getValue('update_product'))
        {
            ProductLoader::updateProduct($this, $params, $no_cache);
        }
        $this->load_datetime = date("Y-m-d H:i:s");

        return parent::update($null_values);
    }


    public static function addPProduct($name, $id_load, $id_category, $id_manufacturer){

        $pproduct = new pproduct(null, $name, $id_load, $id_category, $id_manufacturer);
        return $pproduct->id;
    }

    public function create_product($no_cache = 0)
    {
        if ($this->id_product>0)
            die('SKIPPED '.$this->id.' product exist');

        $this->check_errors();

        $id_product = ProductLoader::addProduct($this);
        $this->id_product = $id_product;

        $params = array();
        $params['product_name_load'] = 1;//Tools::getValue('product_name_load');
        //$params['description_load'] = Tools::getValue('description_load');
        $params['price_load'] = 1;//Tools::getValue('price_load');
        $params['images_load'] = 1;//Tools::getValue('images_load');
        $params['features_load'] = 1;//Tools::getValue('features_load');
        $params['consistions_load'] = 1;//Tools::getValue('consistions_load');
        $params['product_meta_load'] = 1;//Tools::getValue('product_meta_load');
        $params['rewiews_load'] = 1;//Tools::getValue('rewiews_load');
        ProductLoader::updateProduct($this, $params, $no_cache);

        $this->updateLoadsField($this->id,'id_product', $id_product, 'int');
        die("product created - ".$id_product. " for pproduct" .$this->id_pproduct. "; - ok<br> id -");
    }

    public  function check_errors()
    {
        if ($this->id_manufacturer==0)
            die('ERRROR '.$this->id.' manufacturer not defined<br>');
        if ($this->id_category==0)
            die('ERRROR '.$this->id.' category not defined<br>');

    }

    public function update_field($field, $no_cache = 0)
    {
        $this->check_errors();

        $no_cache = Tools::getValue('no_cache');

        $params = array();

        $params['product_meta_load'] = Tools::getValue('product_meta_load');
        $params['product_name_load'] = Tools::getValue('product_name_load');
        $params['description_load'] = Tools::getValue('description_load');
        $params['price_load'] = Tools::getValue('price_load');
        $params['images_load'] = Tools::getValue('images_load');
        $params['features_load'] = Tools::getValue('features_load');
        $params['consistions_load'] = Tools::getValue('consistions_load');
        $params['rewiews_load'] = Tools::getValue('rewiews_load');

        ProductLoader::updateProduct($this, $params, $no_cache);
        $this->load_datetime = date("Y-m-d H:i:s");
        $this->updateLoadsField($this->id,'load_datetime', $this->load_datetime, 'datetime');
        die("id_pproduct - " .$this->id_pproduct. "; id_product - ".$this->id_product."; - ok<br>");
    }

    public function updateLoadsField($id_pproduct, $field_name, $value, $type='string')
    {
        //update
        $sql = "update "._DB_PREFIX_."egploader_product
				set
				`".$field_name."` = ";
        if($type == 'int')
            $sql .="".addslashes($value)."";
        else
            $sql .="'".addslashes($value)."'";

        $sql .=" where id_pproduct = ".(int)$id_pproduct;


        if (!$links = Db::getInstance()->execute($sql))
            return false;
    }


}
