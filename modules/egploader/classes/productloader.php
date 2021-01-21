<?php

require_once(_PS_MODULE_DIR_.'egploader/classes/simple_html_dom.php');
require_once(_PS_MODULE_DIR_.'egploader/classes/loadmanager.php');

class ProductLoader extends AdminControllerCore//ModuleAdminController
{
    public static $column_mask;
    public $entities = array();
    public $available_fields = array();
    public $required_fields = array();

    public $cache_image_deleted = array();

    public static $default_values = array();

    public static $validators = array(
        'active' => array('ProductLoader', 'getBoolean'),
        'tax_rate' => array('ProductLoader', 'getPrice'),
        /** Tax excluded */
        'price_tex' => array('ProductLoader', 'getPrice'),
        /** Tax included */
        'price_tin' => array('ProductLoader', 'getPrice'),
        'reduction_price' => array('ProductLoader', 'getPrice'),
        'reduction_percent' => array('ProductLoader', 'getPrice'),
        'wholesale_price' => array('ProductLoader', 'getPrice'),
        'ecotax' => array('ProductLoader', 'getPrice'),
        'name' => array('ProductLoader', 'createMultiLangField'),
        'description' => array('ProductLoader', 'createMultiLangField'),
        'description_short' => array('ProductLoader', 'createMultiLangField'),
        'meta_title' => array('ProductLoader', 'createMultiLangField'),
        'meta_keywords' => array('ProductLoader', 'createMultiLangField'),
        'meta_description' => array('ProductLoader', 'createMultiLangField'),
        'link_rewrite' => array('ProductLoader', 'createMultiLangField'),
        'available_now' => array('ProductLoader', 'createMultiLangField'),
        'available_later' => array('ProductLoader', 'createMultiLangField'),
        'category' => array('ProductLoader', 'split'),
        'online_only' => array('ProductLoader', 'getBoolean')
    );

    public $separator;
    public $multiple_value_separator;

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
    public $content_cache;
    public $product;
    public $combinations;
    public $extractDataResult;

    public function __construct()
    {
        //$this->bootstrap = true;

        self::$column_mask = array(
            id => 0,
            name => 1,
            description_short => 2,
            description => 3,
            manufacturer => 4,
            category => 5,
            price_tin => 6,
            reduction_percent => 7,
            image => 8,
            delete_existing_images =>9,
            features => 10,
            consistinons => 11
        );

        $this->entities = array(
            $this->l('Categories'),
            $this->l('Products'),
            $this->l('Combinations'),
            $this->l('Customers'),
            $this->l('Addresses'),
            $this->l('Manufacturers'),
            $this->l('Suppliers'),
            $this->l('Alias'),
        );
        self::$validators['image'] = array(
            'ProductLoader',
            'split'
        );
        $this->available_fields = array(
            'no' => array('label' => $this->l('Ignore this column')),
            'id' => array('label' => $this->l('ID')),
            'active' => array('label' => $this->l('Active (0/1)')),
            'name' => array('label' => $this->l('Name')),
            'category' => array('label' => $this->l('Categories (x,y,z...)')),
            'price_tex' => array('label' => $this->l('Price tax excluded')),
            'price_tin' => array('label' => $this->l('Price tax included')),
            'id_tax_rules_group' => array('label' => $this->l('Tax rules ID')),
            'wholesale_price' => array('label' => $this->l('Wholesale price')),
            'on_sale' => array('label' => $this->l('On sale (0/1)')),
            'reduction_price' => array('label' => $this->l('Discount amount')),
            'reduction_percent' => array('label' => $this->l('Discount percent')),
            'reduction_from' => array('label' => $this->l('Discount from (yyyy-mm-dd)')),
            'reduction_to' => array('label' => $this->l('Discount to (yyyy-mm-dd)')),
            'reference' => array('label' => $this->l('Reference #')),
            'supplier_reference' => array('label' => $this->l('Supplier reference #')),
            'supplier' => array('label' => $this->l('Supplier')),
            'manufacturer' => array('label' => $this->l('Manufacturer')),
            'ean13' => array('label' => $this->l('EAN13')),
            'upc' => array('label' => $this->l('UPC')),
            'ecotax' => array('label' => $this->l('Ecotax')),
            'width' => array('label' => $this->l('Width')),
            'height' => array('label' => $this->l('Height')),
            'depth' => array('label' => $this->l('Depth')),
            'weight' => array('label' => $this->l('Weight')),
            'quantity' => array('label' => $this->l('Quantity')),
            'minimal_quantity' => array('label' => $this->l('Minimal quantity')),
            'visibility' => array('label' => $this->l('Visibility')),
            'additional_shipping_cost' => array('label' => $this->l('Additional shipping cost')),
            'unity' => array('label' => $this->l('Unit for the unit price')),
            'unit_price' => array('label' => $this->l('Unit price')),
            'description_short' => array('label' => $this->l('Short description')),
            'description' => array('label' => $this->l('Description')),
            'tags' => array('label' => $this->l('Tags (x,y,z...)')),
            'meta_title' => array('label' => $this->l('Meta title')),
            'meta_keywords' => array('label' => $this->l('Meta keywords')),
            'meta_description' => array('label' => $this->l('Meta description')),
            'link_rewrite' => array('label' => $this->l('URL rewritten')),
            'available_now' => array('label' => $this->l('Text when in stock')),
            'available_later' => array('label' => $this->l('Text when backorder allowed')),
            'available_for_order' => array('label' => $this->l('Available for order (0 = No, 1 = Yes)')),
            'available_date' => array('label' => $this->l('Product availability date')),
            'date_add' => array('label' => $this->l('Product creation date')),
            'show_price' => array('label' => $this->l('Show price (0 = No, 1 = Yes)')),
            'image' => array('label' => $this->l('Image URLs (x,y,z...)')),
            'delete_existing_images' => array(
                'label' => $this->l('Delete existing images (0 = No, 1 = Yes)')
            ),
            'features' => array('label' => $this->l('Feature (Name:Value:Position:Customized)')),
            'online_only' => array('label' => $this->l('Available online only (0 = No, 1 = Yes)')),
            'condition' => array('label' => $this->l('Condition')),
            'customizable' => array('label' => $this->l('Customizable (0 = No, 1 = Yes)')),
            'uploadable_files' => array('label' => $this->l('Uploadable files (0 = No, 1 = Yes)')),
            'text_fields' => array('label' => $this->l('Text fields (0 = No, 1 = Yes)')),
            'out_of_stock' => array('label' => $this->l('Action when out of stock')),
            'shop' => array(
                'label' => $this->l('ID / Name of shop'),
                'help' => $this->l('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.'),
            ),
            'advanced_stock_management' => array(
                'label' => $this->l('Advanced Stock Management'),
                'help' => $this->l('Enable Advanced Stock Management on product (0 = No, 1 = Yes).')
            ),
            'depends_on_stock' => array(
                'label' => $this->l('Depends on stock'),
                'help' => $this->l('0 = Use quantity set in product, 1 = Use quantity from warehouse.')
            ),
            'warehouse' => array(
                'label' => $this->l('Warehouse'),
                'help' => $this->l('ID of the warehouse to set as storage.')
            ),
        );

        self::$default_values = array(
            'id_category' => array((int)Configuration::get('PS_HOME_CATEGORY')),
            'id_category_default' => null,
            'active' => '1',
            'width' => 0.000000,
            'height' => 0.000000,
            'depth' => 0.000000,
            'weight' => 0.000000,
            'visibility' => 'both',
            'additional_shipping_cost' => 0.00,
            'unit_price' => 0,
            'quantity' => 0,
            'minimal_quantity' => 1,
            'price' => 0,
            'id_tax_rules_group' => 0,
            'description_short' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
            'link_rewrite' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
            'online_only' => 0,
            'condition' => 'new',
            'available_date' => date('Y-m-d'),
            'date_add' => date('Y-m-d H:i:s'),
            'customizable' => 0,
            'uploadable_files' => 0,
            'text_fields' => 0,
            'advanced_stock_management' => 0,
            'depends_on_stock' => 0,
        );
        $this->image_counter = 0;
        $this->multiple_value_separator = ($separator = Tools::substr(strval(trim(Tools::getValue('multiple_value_separator'))), 0, 1)) ? $separator :  '~~';
        parent::__construct();
    }

    public static function addProduct($pproduct)
    {
            $pl = new ProductLoader();
            $pl->line[1] = $pproduct->name;
            $pl->line[2] = '<p></p>';
            $pl->line[4] = $pproduct->id_manufacturer;
            $pl->line[5] = $pproduct->id_category;

            $pl->productImport();
            return $pl->product->id;
    }

    public static function updateProduct($pproduct, $params, $no_cache = 0)
    {

        $pl = new ProductLoader();
        $pl->id_product = $pproduct->id_product;

        $lm = new LoadManager($no_cache);
        $errors = array();
        if ($params['product_name_load'] && $pproduct->product_name > 0) {
            $lm->setVariables($pproduct->product_name);
            $lm->getContent();
            if ($lm->content) {
                $lm->extractData();
                $pl->line[1] = $lm->extractDataResult['product_name'];
            }else {
                $errors[] = 'fail content for '.$pproduct->product_name.' - Product name';
            }
        }

        if ($params['description_load'] && $pproduct->description > 0){
            $lm->setVariables($pproduct->description);
            $lm->getContent();
            if ($lm->content) {
                $lm->extractData();
                $description = ProductLoader::getDescription($pproduct);//$lm->extractDataResult['description'];
                $pl->line[2] = $description['description_short'];
                $pl->line[3] = $description['description'];
            }else {
                $errors[] = 'fail content for '.$pproduct->description.' - Description';
            }
        }

        $pl->line[4] = $pproduct->id_manufacturer; // manuf
        $pl->line[5] = $pproduct->id_category; // catego

        if ($params['price_load'] && $pproduct->price > 0)
        {
            $lm->setVariables($pproduct->price);
            $lm->getContent();
            if ($lm->content) {
                $lm->extractData();
                $pl->line[6] = $lm->extractDataResult['price'];
                if($lm->extractDataResult['price_discount']>0)
                    $pl->line[7] = $lm->extractDataResult['price_discount'];
            }else {
                $errors[] = 'fail content for '.$pproduct->price.' - Price';
            }
        }

        if ($params['images_load'] && $pproduct->images > 0)
        {
            $lm->setVariables($pproduct->images);
            $lm->getContent();
            if ($lm->content) {
                $lm->extractData();
                $pl->line[8] = $lm->extractDataResult['product_images'];
                $pl->line[9] = 1;
            }else {
                $errors[] = 'fail content for '.$pproduct->images.' - Images';
            }
        }

        if ($params['features_load'] && $pproduct->features > 0)
        {
                $pl->line[10] = 1;
        }

        if ($params['consistions_load'] && $pproduct->consistions > 0)
        {
            $pl->line[11] = $pproduct->consistions;
        }

        /*
                if ($params['rewiews_load'] && $pproduct->rewiews > 0)
                {
                    $lm->setVariables($pproduct->rewiews);
                    $lm->getContent();
                    if ($lm->content) {
                        //       rewiews
                    }else {
                        $errors[] = 'fail content for '.$pproduct->rewiews.' - Rewiews';
                    }
                }
        */
        if ($params['product_meta_load'])
        {
            ProductLoader::updateProductMeta($pl->id_product);
        }


        $pl->productImport();
        if ($params['price_load'] && $pproduct->price > 0) {
            $pl->generatePriceAttribute($pproduct->id_attr_group1, $pproduct->id_attr_group2);
        }
    }

    public static function updateProductMeta($id_product){
        $meta_title = Configuration::get('EGPLOADER_PRODUCT_META_TITLE');
        $meta_desc = Configuration::get('EGPLOADER_PRODUCT_META_DESC');
        $meta_keys = Configuration::get('EGPLOADER_PRODUCT_META_KEAYS');


        $sql = "UPDATE "._DB_PREFIX_."product_lang 
        set 
        meta_title = replace('".$meta_title."','%product%', name),
        meta_description =  replace('".$meta_desc."','%product%', name),
        meta_keywords =  replace('".$meta_keys."','%product%', name)
        WHERE id_product = ".$id_product."
        AND id_shop = 1";

        $links = Db::getInstance()->execute($sql);
    }

    public static function getDescription($product)
    {
        $sql="SELECT * FROM "._DB_PREFIX_."egploader_product_data pd WHERE pd.id_load=".$product->id_load;

        $links = Db::getInstance()->executeS($sql);

        return $links;
    }

    public static function getAttributeId($name, $attrg, $ctyp=0)
    {
        $sql = 'select a.id_attribute from '._DB_PREFIX_.'attribute a
				 join '._DB_PREFIX_.'attribute_lang al on a.id_attribute = al.id_attribute
				where a.id_attribute_group = '.$attrg.'';
        if ($ctyp == 0)
            $sql .= ' and al.name like \''.$name.'%\';';
        else
            $sql .= ' and al.name = \''.$name.'\';';
        $links = Db::getInstance()->executeS($sql);
        if ($links[0]['id_attribute']> 0 )
            return $links[0]['id_attribute'];

        return false;
    }

    public function generatePriceAttribute($id_attr_group1, $id_attr_group2){

        $attrs = array();//  addeteonal attributes
        $attrr_group_add = $id_attr_group1;

        $prices = $this->line[6]['price'];
        $sizes = $this->line[6]['sizes'];

        $basePrice = $prices[0];
        foreach ($prices as $p)
        {
            $prices_dif[] = $p - $basePrice;
        }


        $tab[0] = array();
        $combinations = 0;

        foreach($sizes as $key => $size)
        {
            $size = str_replace('x', '×', $size);
            $id_attr = ProductLoader::getAttributeId($size, $attrr_group_add);
            if ($id_attr)
            {
                $tab[0][]= $id_attr;
            }else{
                unset($prices[$key]);
                unset($prices_dif[$key]);
                unset($sizes[$key]);
                //unset($attrs[$key]);
                // delete prices and prices_dif
            }
        }

        foreach ($attrs as $attr) {
            if (trim($attr) != '') {

                $id_attr = ProductLoader::getAttributeId($attr, $attrr_group_add, 1);
                if ($id_attr) {
                    $tab[1][] = $id_attr;
                }
            }
        }


        ProductLoader::deleteCombinatinImages($this->product);
        SpecificPriceRule::deleteConditions();
        $this->updateProductBasePrice($this->product->id, $basePrice);
        $this->product->deleteProductAttributes();

        if($attrr_group_add>0){
            ProductLoader::setAttributesImpacts($this->product->id, $tab, $prices_dif);

            if ($tab[1]) {
                foreach ($tab[1] as $t) {
                    foreach ($prices_dif as $a)
                        $prices_dif_full[] = $a;
                }
            }else
            {
                $prices_dif_full = $prices_dif;
            }

            $this->combinations = array_values(ProductLoader::createCombinations($tab));
            $values = array_values(array_map(array($this, 'addAttribute'), $this->combinations, $prices_dif_full));
            //$combinations = $this->getCombination($tab);
            //$values = $this->getMap($product->id, $combinations, $prices_dif);

            SpecificPriceRule::disableAnyApplication();

            $this->product->deleteProductAttributes();
            $res = $this->product->generateMultipleCombinations($values, $this->combinations);

            SpecificPriceRule::enableAnyApplication();

            SpecificPriceRule::applyAllRules(array((int)$this->product->id));

            ProductLoader::addCombinationImages($this->product, $attrs);
        }
    }

    protected function addAttribute($attributes, $price = 0, $weight = 0)
    {
        $a = $attributes;
        if ($this->product->id)
        {
            return array(
                'id_product' => (int)$this->product->id,
                'price' => (float)$price,
                'weight' => 0,
                'ecotax' => 0,
                'quantity' => 0,
                'reference' => 0,
                'default_on' => 0,
                'available_date' => '0000-00-00'
            );
        }
        return array();
    }

    public function updateProductBasePrice($id_product, $price)
    {
        $id_shop = $this->context->shop->id;

        $sql = "update "._DB_PREFIX_."product_shop
				 		set
				 		price = ".$price."
				 		where id_product=".(int) $id_product."
				 		and id_shop IN (".implode(', ', Shop::getContextListShopID()).")";
        if (!$links = Db::getInstance()->execute($sql))
            return false;

        $sql = "update "._DB_PREFIX_."product
				 		set
				 		price = ".$price."
				 		where id_product=".(int) $id_product;
        if (!$links = Db::getInstance()->execute($sql))
            return false;
    }

    protected static function setAttributesImpacts($id_product, $tab, $prices)
    {
        $attributes = array();
        foreach ($tab as $group)
            foreach ($group as $key => $attribute) {
                $price = $prices[$key];
                $weight = 0;
                $attributes[] = '(' . (int)$id_product . ', ' . (int)$attribute . ', ' . (float)$price . ', ' . (float)$weight . ')';
            }

        return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
		VALUES '.implode(',', $attributes).'
		ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');
    }

    protected static function addCombinationImages($product, $attrs)
    {
        $id_lang = Context::getContext()->language->id;
        $combinations = $product->getAttributeCombinations($id_lang);

        foreach($attrs as $attr) {
            foreach ($combinations as $combination) {
                if (($combination['attribute_name']) == trim($attr)) {
                    $id_product_attribute = $combination['id_product_attribute'];

                    $images = $product->getImages($id_lang);

                    foreach ($images as $image) {
                        $parts = explode(':', $image['legend']);
                        if (trim($parts[1]) == trim($attr)) {
                            $sql = "insert into " . _DB_PREFIX_ . "product_attribute_image(id_product_attribute, id_image)
                              values(" . $id_product_attribute . ", " . $image['id_image'] . ")";
                            Db::getInstance()->executeS($sql);
                        }
                    }
                }
            }
        }

    }

    protected static function deleteCombinatinImages($product)
    {
        $sql = "delete pai
                      from "._DB_PREFIX_."product_attribute_image pai
                        inner join "._DB_PREFIX_."product_attribute pa on pai.id_product_attribute = pa.id_product_attribute
                        where pa.id_product = ".(int)$product->id;
        $r = Db::getInstance()->executeS($sql);

    }

    protected static function createCombinations($list)
    {
        if (count($list) <= 1)
            return count($list) ? array_map(create_function('$v', 'return (array($v));'), $list[0]) : $list;
        $res = array();
        $first = array_pop($list);
        foreach ($first as $attribute)
        {
            $tab = ProductLoader::createCombinations($list);
            foreach ($tab as $to_add)
                $res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
        }
        return $res;
    }

    public function utf8EncodeArray($array)
    {
        return (is_array($array) ? array_map('utf8_encode', $array) : utf8_encode($array));
    }


    public static function getMaskedRow($row)
    {
        $res = array();
        if (is_array(self::$column_mask))
            foreach (self::$column_mask as $type => $nb)
                $res[$type] = isset($row[$nb]) ? $row[$nb] : null;

        return $res;
    }

    protected static function rewindBomAware($handle)
    {
        // A rewind wrapper that skips BOM signature wrongly
        if (!is_resource($handle))
            return false;
        rewind($handle);
        if (($bom = fread($handle, 3)) != "\xEF\xBB\xBF")
            rewind($handle);
    }

    protected static function getBoolean($field)
    {
        return (boolean)$field;
    }

    protected static function getPrice($field)
    {
        $field = ((float)str_replace(',', '.', $field));
        $field = ((float)str_replace('%', '', $field));
        return $field;
    }

    protected static function split($field)
    {
        if (empty($field))
            return array();

        $separator = Tools::getValue('multiple_value_separator');
        if (is_null($separator) || trim($separator) == '')
            $separator = ',';

        do
            $uniqid_path = _PS_UPLOAD_DIR_.uniqid();
        while (file_exists($uniqid_path));
        file_put_contents($uniqid_path, $field);
        $tab = '';
        if (!empty($uniqid_path))
        {
            $fd = fopen($uniqid_path, 'r');
            $tab = fgetcsv($fd, MAX_LINE_SIZE, $separator);
            fclose($fd);
            if (file_exists($uniqid_path))
                @unlink($uniqid_path);
        }

        if (empty($tab) || (!is_array($tab)))
            return array();
        return $tab;
    }

    protected static function copyImg($id_entity, $id_image = null, $url, $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity)
        {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int)$id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_.(int)$id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_.(int)$id_entity;
                break;
        }

        $url = str_replace(' ', '%20', trim($url));
        $url = urldecode($url);
        $parced_url = parse_url($url);

        if (isset($parced_url['path']))
        {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part)
                $part = urlencode ($part);
            unset($part);
            $parced_url['path'] = '/'.implode('/', $parts);
        }

        if (isset($parced_url['query']))
        {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url'))
            require_once(_PS_TOOL_DIR_ . 'http_build_url/http_build_url.php');

        $url = http_build_url('', $parced_url);

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($url))
            return false;

        // 'file_exists' doesn't work on distant file, and getimagesize makes the import slower.
        // Just hide the warning, the processing will be the same.
        if (Tools::copy($url, $tmpfile))
        {
            ImageManager::resize($tmpfile, $path.'.jpg');
            $images_types = ImageType::getImagesTypes($entity);

            if ($regenerate)
                foreach ($images_types as $image_type)
                {
                    ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']);
                    if (in_array($image_type['id_image_type'], $watermark_types))
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                }
        }
        else
        {
            unlink($tmpfile);
            return false;
        }
        unlink($tmpfile);
        return true;
    }

    protected function addProductWarning($product_name, $product_id = null, $message = '')
    {
        $this->warnings[] = $product_name.(isset($product_id) ? ' (ID '.$product_id.')' : '').' '
            .Tools::displayError($message);
    }

    public function refCalculate($id_manufacturer, $id_product)
    {

         $ref = "".((strlen($id_manufacturer)==1)?'0'.$id_manufacturer:$id_manufacturer);
         for($i=0;$i<4-strlen($id_product);$i++)
            $ref .= '0';
         return $ref.$id_product;
    }

    public static function productToCategorieAssigne($id_product, $id_category)
    {

        $sql = "INSERT INTO "._DB_PREFIX_."category_product
                SELECT ".$id_category." as c, ".$id_product." as p, tmp.max + 1 FROM (
					SELECT MAX(cp.`position`) AS max
					FROM `'._DB_PREFIX_.'category_product` cp
					WHERE cp.`id_category`=".(int)$id_category.") AS tmp";
        
        $r = Db::getInstance()->execute($sql);
        return $r;
    }

    public function productImport()
    {
        $line = $this->line;

        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso('ru');
        if (!Validate::isUnsignedId($id_lang))
            $id_lang = $default_language_id;
        ProductLoader::setLocale();

        $shop_ids = Shop::getCompleteListOfShopsID();

        if (Tools::getValue('convert'))
            $line = $this->utf8EncodeArray($line);
        $info = ProductLoader::getMaskedRow($line);

        if($this->id_product)
            $product = new Product($this->id_product);
        else
            $product = new Product();

        if (isset($product->id) && $product->id && Product::existsInDatabase((int)$product->id, 'product'))
        {
            $product->loadStockData();
            $category_data = Product::getProductCategories((int)$product->id);

            if (is_array($category_data))
                foreach ($category_data as $tmp)
                    if (!isset($product->category) || !$product->category || is_array($product->category))
                        $product->category[] = $tmp;
        }

        ProductLoader::setEntityDefaultValues($product);
        ProductLoader::arrayWalk($info, array('ProductLoader', 'fillInfo'), $product);



        if (!Shop::isFeatureActive())
            $product->shop = 1;
        elseif (!isset($product->shop) || empty($product->shop))
            $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());

        if (!Shop::isFeatureActive())
            $product->id_shop_default = 1;
        else
            $product->id_shop_default = (int)Context::getContext()->shop->id;

        // link product to shops
        $product->id_shop_list = array();
        foreach (explode($this->multiple_value_separator, $product->shop) as $shop)
            if (!empty($shop) && !is_numeric($shop))
                $product->id_shop_list[] = Shop::getIdByName($shop);
            elseif (!empty($shop))
                $product->id_shop_list[] = $shop;

        if ((int)$product->id_tax_rules_group != 0)
        {
            if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group)))
            {
                $address = $this->context->shop->getAddress();
                $tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
                $product_tax_calculator = $tax_manager->getTaxCalculator();
                $product->tax_rate = $product_tax_calculator->getTotalRate();
            }
            else
                $this->addProductWarning(
                    'id_tax_rules_group',
                    $product->id_tax_rules_group,
                    Tools::displayError('Invalid tax rule group ID. You first need to create a group with this ID.')
                );
        }

        if (isset($product->manufacturer) && is_numeric($product->manufacturer) && Manufacturer::manufacturerExists((int)$product->manufacturer))
            $product->id_manufacturer = (int)$product->manufacturer;
        elseif (isset($product->manufacturer) && is_string($product->manufacturer) && !empty($product->manufacturer))
        {
            if ($manufacturer = Manufacturer::getIdByName($product->manufacturer))
                $product->id_manufacturer = (int)$manufacturer;
            else
            {
                $manufacturer = new Manufacturer();
                $manufacturer->name = $product->manufacturer;
                if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $manufacturer->add())
                    $product->id_manufacturer = (int)$manufacturer->id;
                else
                {
                    $this->errors[] = sprintf(
                        Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                        $manufacturer->name,
                        (isset($manufacturer->id) && !empty($manufacturer->id))? $manufacturer->id : 'null'
                    );
                    $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                        Db::getInstance()->getMsgError();
                }
            }
        }

        if (isset($product->supplier) && is_numeric($product->supplier) && Supplier::supplierExists((int)$product->supplier))
            $product->id_supplier = (int)$product->supplier;
        elseif (isset($product->supplier) && is_string($product->supplier) && !empty($product->supplier))
        {
            if ($supplier = Supplier::getIdByName($product->supplier))
                $product->id_supplier = (int)$supplier;
            else
            {
                $supplier = new Supplier();
                $supplier->name = $product->supplier;
                $supplier->active = true;

                if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $supplier->add())
                {
                    $product->id_supplier = (int)$supplier->id;
                    $supplier->associateTo($product->id_shop_list);
                }
                else
                {
                    $this->errors[] = sprintf(
                        Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                        $supplier->name,
                        (isset($supplier->id) && !empty($supplier->id))? $supplier->id : 'null'
                    );
                    $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                        Db::getInstance()->getMsgError();
                }
            }
        }

        if (isset($product->price_tex) && !isset($product->price_tin))
            $product->price = $product->price_tex;
        elseif (isset($product->price_tin) && !isset($product->price_tex))
        {
            $product->price = $product->price_tin;
            // If a tax is already included in price, withdraw it from price
            if ($product->tax_rate)
                $product->price = (float)number_format($product->price / (1 + $product->tax_rate / 100), 6, '.', '');
        }
        elseif (isset($product->price_tin) && isset($product->price_tex))
            $product->price = $product->price_tex;

        if (!Configuration::get('PS_USE_ECOTAX'))
            $product->ecotax = 0;

        if (isset($product->category) && is_array($product->category) && count($product->category))
        {
            $product->id_category = array(); // Reset default values array
            foreach ($product->category as $value)
            {
                if (is_numeric($value))
                {
                    if (Category::categoryExists((int)$value))
                        $product->id_category[] = (int)$value;
                    else
                    {
                        $category_to_create = new Category();
                        $category_to_create->id = (int)$value;
                        $category_to_create->name = ProductLoader::createMultiLangField($value);
                        $category_to_create->active = 1;
                        $category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
                        $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
                        $category_to_create->link_rewrite = ProductLoader::createMultiLangField($category_link_rewrite);
                        if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                            ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $category_to_create->add())
                            $product->id_category[] = (int)$category_to_create->id;
                        else
                        {
                            $this->errors[] = sprintf(
                                Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                                $category_to_create->name[$default_language_id],
                                (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
                            );
                            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                Db::getInstance()->getMsgError();
                        }
                    }
                }
                elseif (is_string($value) && !empty($value))
                {
                    $category = Category::searchByPath($default_language_id, trim($value), $this, 'productImportCreateCat');
                    if ($category['id_category'])
                        $product->id_category[] = (int)$category['id_category'];
                    else
                        $this->errors[] = sprintf(Tools::displayError('%1$s cannot be saved'), trim($value));
                }
            }
            $product->id_category = array_values(array_unique($product->id_category));
        }
        ProductLoader::productToCategorieAssigne($product->id, $product->id_category_default);

       // if (!isset($product->id_category_default) || !$product->id_category_default)
            $product->id_category_default = isset($product->id_category[0]) ? (int)$product->id_category[0] : (int)Configuration::get('PS_HOME_CATEGORY');

        $link_rewrite = (is_array($product->link_rewrite) && isset($product->link_rewrite[$id_lang])) ? trim($product->link_rewrite[$id_lang]) : '';
        $valid_link = Validate::isLinkRewrite($link_rewrite);

        if ((isset($product->link_rewrite[$id_lang]) && empty($product->link_rewrite[$id_lang])) || !$valid_link)
        {
            $link_rewrite = Tools::link_rewrite($product->name[$id_lang]);
            if ($link_rewrite == '')
                $link_rewrite = 'friendly-url-autogeneration-failed';
        }

        if (!$valid_link)
            $this->warnings[] = sprintf(
                Tools::displayError('Rewrite link for %1$s (ID: %2$s) was re-written as %3$s.'),
                $product->name[$id_lang],
                (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                $link_rewrite
            );

        if (!(Tools::getValue('match_ref') || Tools::getValue('forceIDs')) || !(is_array($product->link_rewrite) && count($product->link_rewrite) && !empty($product->link_rewrite[$id_lang])))
            $product->link_rewrite = ProductLoader::createMultiLangField($link_rewrite);

        // replace the value of separator by coma
        if ($this->multiple_value_separator != ',')
            if (is_array($product->meta_keywords))
                foreach ($product->meta_keywords as &$meta_keyword)
                    if (!empty($meta_keyword))
                        $meta_keyword = str_replace($this->multiple_value_separator, ',', $meta_keyword);

        // Convert comma into dot for all floating values
        foreach (Product::$definition['fields'] as $key => $array)
            if ($array['type'] == Product::TYPE_FLOAT)
                $product->{$key} = str_replace(',', '.', $product->{$key});

        // Indexation is already 0 if it's a new product, but not if it's an update
        $product->indexed = 0;

        $product->reference = $this->refCalculate($product->id_manufacturer, $product->id);

        $res = false;
        $field_error = $product->validateFields(UNFRIENDLY_ERROR, true);
        $lang_field_error = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
        if ($field_error === true && $lang_field_error === true)
        {
            // check quantity
            if ($product->quantity == null)
                $product->quantity = 0;

            // If match ref is specified && ref product && ref product already in base, trying to update
            if (Tools::getValue('match_ref') && $product->reference && $product->existsRefInDatabase($product->reference))
            {
                $datas = Db::getInstance()->getRow('
						SELECT product_shop.`date_add`, p.`id_product`
						FROM `'._DB_PREFIX_.'product` p
						'.Shop::addSqlAssociation('product', 'p').'
						WHERE p.`reference` = "'.pSQL($product->reference).'"
					');
                $product->id = (int)$datas['id_product'];
                $product->date_add = pSQL($datas['date_add']);
                $res = $product->update();
            } // Else If id product && id product already in base, trying to update
            elseif ($product->id && Product::existsInDatabase((int)$product->id, 'product'))
            {
                $datas = Db::getInstance()->getRow('
						SELECT product_shop.`date_add`
						FROM `'._DB_PREFIX_.'product` p
						'.Shop::addSqlAssociation('product', 'p').'
						WHERE p.`id_product` = '.(int)$product->id);
                $product->date_add = pSQL($datas['date_add']);
                $res = $product->update();
            }
            // If no id_product or update failed
            $product->force_id = (bool)Tools::getValue('forceIDs');

            if (!$res)
            {
                if (isset($product->date_add) && $product->date_add != '')
                    $res = $product->add(false);
                else
                    $res = $product->add();
            }

            if ($product->getType() == Product::PTYPE_VIRTUAL)
                StockAvailable::setProductOutOfStock((int)$product->id, 1);
            else
                StockAvailable::setProductOutOfStock((int)$product->id, (int)$product->out_of_stock);

        }

        $shops = array();
        $product_shop = explode($this->multiple_value_separator, $product->shop);
        foreach ($product_shop as $shop)
        {
            if (empty($shop))
                continue;
            $shop = trim($shop);
            if (!empty($shop) && !is_numeric($shop))
                $shop = Shop::getIdByName($shop);

            if (in_array($shop, $shop_ids))
                $shops[] = $shop;
            else
                $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Shop is not valid'));
        }

        if (empty($shops))
            $shops = Shop::getContextListShopID();
        // If both failed, mysql error

        if (!$res)
        {
            $this->errors[] = sprintf(
                Tools::displayError('%1$s (ID: %2$s) cannot be saved'),
                (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
            );
            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                Db::getInstance()->getMsgError();

        }
        else
        {
            // Product supplier
            if (isset($product->id) && $product->id && isset($product->id_supplier) && property_exists($product, 'supplier_reference'))
            {
                $id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$product->id, 0, (int)$product->id_supplier);
                if ($id_product_supplier)
                    $product_supplier = new ProductSupplier($id_product_supplier);
                else
                    $product_supplier = new ProductSupplier();

                $product_supplier->id_product = (int)$product->id;
                $product_supplier->id_product_attribute = 0;
                $product_supplier->id_supplier = (int)$product->id_supplier;
                $product_supplier->product_supplier_price_te = $product->wholesale_price;
                $product_supplier->product_supplier_reference = $product->supplier_reference;
                $product_supplier->save();
            }

            // SpecificPrice (only the basic reduction feature is supported by the import)
            if (!Shop::isFeatureActive())
                $info['shop'] = 1;
            elseif (!isset($info['shop']) || empty($info['shop']))
                $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());

            // Get shops for each attributes
            $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

            $id_shop_list = array();
            foreach ($info['shop'] as $shop)
                if (!empty($shop) && !is_numeric($shop))
                    $id_shop_list[] = (int)Shop::getIdByName($shop);
                elseif (!empty($shop))
                    $id_shop_list[] = $shop;

            if ((isset($info['reduction_price']) && $info['reduction_price'] > 0) || (isset($info['reduction_percent']) && $info['reduction_percent'] > 0)) {
                foreach ($id_shop_list as $id_shop) {
                    $specific_price = SpecificPrice::getSpecificPrice($product->id, $id_shop, 0, 0, 0, 1, 0, 0, 0, 0);

                    if (is_array($specific_price) && isset($specific_price['id_specific_price']))
                        $specific_price = new SpecificPrice((int)$specific_price['id_specific_price']);
                    else
                        $specific_price = new SpecificPrice();
                    $specific_price->id_product = (int)$product->id;
                    $specific_price->id_specific_price_rule = 0;
                    $specific_price->id_shop = $id_shop;
                    $specific_price->id_currency = 0;
                    $specific_price->id_country = 0;
                    $specific_price->id_group = 0;
                    $specific_price->price = -1;
                    $specific_price->id_customer = 0;
                    $specific_price->from_quantity = 1;
                    $specific_price->reduction = (isset($info['reduction_price']) && $info['reduction_price']) ? $info['reduction_price'] : $info['reduction_percent'] / 100;
                    $specific_price->reduction_type = (isset($info['reduction_price']) && $info['reduction_price']) ? 'amount' : 'percentage';
                    $specific_price->from = (isset($info['reduction_from']) && Validate::isDate($info['reduction_from'])) ? $info['reduction_from'] : '0000-00-00 00:00:00';
                    $specific_price->to = (isset($info['reduction_to']) && Validate::isDate($info['reduction_to'])) ? $info['reduction_to'] : '0000-00-00 00:00:00';
                    if (!$specific_price->save())
                        $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Discount is invalid'));
                }
            }else
                SpecificPrice::deleteByProductId($product->id);

            if (isset($product->tags) && !empty($product->tags))
            {
                if (isset($product->id) && $product->id)
                {
                    $tags = Tag::getProductTags($product->id);
                    if (is_array($tags) && count($tags))
                    {
                        if (!empty($product->tags))
                            $product->tags = explode($this->multiple_value_separator, $product->tags);
                        if (is_array($product->tags) && count($product->tags))
                        {
                            foreach ($product->tags as $key => $tag)
                                if (!empty($tag))
                                    $product->tags[$key] = trim($tag);
                            $tags[$id_lang] = $product->tags;
                            $product->tags = $tags;
                        }
                    }
                }
                // Delete tags for this id product, for no duplicating error
                Tag::deleteTagsForProduct($product->id);
                if (!is_array($product->tags) && !empty($product->tags))
                {
                    $product->tags = ProductLoader::createMultiLangField($product->tags);
                    foreach ($product->tags as $key => $tags)
                    {
                        $is_tag_added = Tag::addTags($key, $product->id, $tags, $this->multiple_value_separator);
                        if (!$is_tag_added)
                        {
                            $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->l('Tags list is invalid'));
                            break;
                        }
                    }
                }
                else
                {
                    foreach ($product->tags as $key => $tags)
                    {
                        $str = '';
                        foreach ($tags as $one_tag)
                            $str .= $one_tag.$this->multiple_value_separator;
                        $str = rtrim($str, $this->multiple_value_separator);

                        $is_tag_added = Tag::addTags($key, $product->id, $str, $this->multiple_value_separator);
                        if (!$is_tag_added)
                        {
                            $this->addProductWarning(Tools::safeOutput($info['name']), (int)$product->id, 'Invalid tag(s) ('.$str.')');
                            break;
                        }
                    }
                }
            }

            //delete existing images if "delete_existing_images" is set to 1
            if (isset($product->delete_existing_images))
                if ((bool)$product->delete_existing_images)
                    $product->deleteImages();

            if (isset($product->image) && is_array($product->image) && count($product->image))
            {
                $product_has_images = (bool)Image::getImages($this->context->language->id, (int)$product->id);
                foreach ($product->image as $key => $url)
                {
                    $url = trim($url);
                    $error = false;
                    if (!empty($url))
                    {
                        $url = str_replace(' ', '%20', $url);

                        $image = new Image();
                        $image->id_product = (int)$product->id;
                        $image->position = Image::getHighestPosition($product->id) + 1;
                        $image->cover = (!$key && !$product_has_images) ? true : false;
                        // file_exists doesn't work with HTTP protocol
                        if (($field_error = $image->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                            ($lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $image->add())
                        {
                            // associate image to selected shops
                            $image->associateTo($shops);
                            if (!ProductLoader::copyImg($product->id, $image->id, $url, 'products', !Tools::getValue('regenerate')))
                            {
                                $image->delete();
                                $this->warnings[] = sprintf(Tools::displayError('Error copying image: %s'), $url);
                            }
                        }
                        else
                            $error = true;
                    }
                    else
                        $error = true;

                    if ($error)
                        $this->warnings[] = sprintf(Tools::displayError('Product #%1$d: the picture (%2$s) cannot be saved.'), $image->id_product, $url);
                }
            }

            if (isset($product->id_category) && is_array($product->id_category))
                $product->updateCategories(array_map('intval', $product->id_category));

            $product->checkDefaultAttributes();
            if (!$product->cache_default_attribute)
                Product::updateDefaultAttribute($product->id);

            // Features import
            if($this->line[10]==1){
                $product->deleteProductFeatures();

                foreach($features = LoadManager::getProductFeatures($product->id) as $feaure)
                {
                    Product::addFeatureProductImport($product->id, $feaure['id_feature'], $feaure['id_feature_value']);
                }

                // clean feature positions to avoid conflict
                Feature::cleanPositions();
            }

            if($this->line[11]==1) {
                LoadManager::getConsistions($product->id, $this->line[11]);
            }

            // set advanced stock managment
            if (isset($product->advanced_stock_management))
            {
                if ($product->advanced_stock_management != 1 && $product->advanced_stock_management != 0)
                    $this->warnings[] = sprintf(Tools::displayError('Advanced stock management has incorrect value. Not set for product %1$s '), $product->name[$default_language_id]);
                elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management == 1)
                    $this->warnings[] = sprintf(Tools::displayError('Advanced stock management is not enabled, cannot enable on product %1$s '), $product->name[$default_language_id]);
                else
                    $product->setAdvancedStockManagement($product->advanced_stock_management);
                // automaticly disable depends on stock, if a_s_m set to disabled
                if (StockAvailable::dependsOnStock($product->id) == 1 && $product->advanced_stock_management == 0)
                    StockAvailable::setProductDependsOnStock($product->id, 0);
            }

            // Check if warehouse exists
            if (isset($product->warehouse) && $product->warehouse)
            {
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
                    $this->warnings[] = sprintf(Tools::displayError('Advanced stock management is not enabled, warehouse not set on product %1$s '), $product->name[$default_language_id]);
                else
                {
                    if (Warehouse::exists($product->warehouse))
                    {
                        // Get already associated warehouses
                        $associated_warehouses_collection = WarehouseProductLocation::getCollection($product->id);
                        // Delete any entry in warehouse for this product
                        foreach ($associated_warehouses_collection as $awc)
                            $awc->delete();
                        $warehouse_location_entity = new WarehouseProductLocation();
                        $warehouse_location_entity->id_product = $product->id;
                        $warehouse_location_entity->id_product_attribute = 0;
                        $warehouse_location_entity->id_warehouse = $product->warehouse;
                        if (WarehouseProductLocation::getProductLocation($product->id, 0, $product->warehouse) !== false)
                            $warehouse_location_entity->update();
                        else
                            $warehouse_location_entity->save();
                        StockAvailable::synchronize($product->id);
                    }
                    else
                        $this->warnings[] = sprintf(Tools::displayError('Warehouse did not exist, cannot set on product %1$s.'), $product->name[$default_language_id]);
                }
            }

            // stock available
            if (isset($product->depends_on_stock))
            {
                if ($product->depends_on_stock != 0 && $product->depends_on_stock != 1)
                    $this->warnings[] = sprintf(Tools::displayError('Incorrect value for "depends on stock" for product %1$s '), $product->name[$default_language_id]);
                elseif ((!$product->advanced_stock_management || $product->advanced_stock_management == 0) && $product->depends_on_stock == 1)
                    $this->warnings[] = sprintf(Tools::displayError('Advanced stock management not enabled, cannot set "depends on stock" for product %1$s '), $product->name[$default_language_id]);
                else
                    StockAvailable::setProductDependsOnStock($product->id, $product->depends_on_stock);

                // This code allows us to set qty and disable depends on stock
                if (isset($product->quantity) && (int)$product->quantity)
                {
                    // if depends on stock and quantity, add quantity to stock
                    if ($product->depends_on_stock == 1)
                    {
                        $stock_manager = StockManagerFactory::getManager();
                        $price = str_replace(',', '.', $product->wholesale_price);
                        if ($price == 0)
                            $price = 0.000001;
                        $price = round(floatval($price), 6);
                        $warehouse = new Warehouse($product->warehouse);
                        if ($stock_manager->addProduct((int)$product->id, 0, $warehouse, (int)$product->quantity, 1, $price, true))
                            StockAvailable::synchronize((int)$product->id);
                    }
                    else
                    {
                        if (Shop::isFeatureActive())
                            foreach ($shops as $shop)
                                StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                        else
                            StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$this->context->shop->id);
                    }
                }
            }
            else // if not depends_on_stock set, use normal qty
            {
                if (Shop::isFeatureActive())
                    foreach ($shops as $shop)
                        StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                else
                    StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$this->context->shop->id);
            }
        }
        $this->load_result['id_product']= $product->id;
        $this->load_result['url_product_name'] = Product::getProductName($product->id);

        $this->product = $product;

    }

    protected static function setDefaultValues(&$info)
    {
        foreach (self::$default_values as $k => $v)
            if (!isset($info[$k]) || $info[$k] == '')
                $info[$k] = $v;
    }

    protected static function setEntityDefaultValues(&$entity)
    {
        $members = get_object_vars($entity);
        foreach (self::$default_values as $k => $v)
            if ((array_key_exists($k, $members) && $entity->$k === null) || !array_key_exists($k, $members))
                $entity->$k = $v;
    }

    public static function arrayWalk(&$array, $funcname, &$user_data = false)
    {
        if (!is_callable($funcname)) return false;

        foreach ($array as $k => $row)
            if (!call_user_func_array($funcname, array($row, $k, $user_data)))
                return false;
        return true;
    }

    protected static function fillInfo($infos, $key, &$entity)
    {
        $infos = trim($infos);
        if (isset(self::$validators[$key][1]) && self::$validators[$key][1] == 'createMultiLangField' && Tools::getValue('iso_lang'))
        {
            $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
            $tmp = call_user_func(self::$validators[$key], $infos);
            foreach ($tmp as $id_lang_tmp => $value)
                if (empty($entity->{$key}[$id_lang_tmp]) || $id_lang_tmp == $id_lang)
                    $entity->{$key}[$id_lang_tmp] = $value;
        }
        else
            if (!empty($infos) || $infos == '0') // ($infos == '0') => if you want to disable a product by using "0" in active because empty('0') return true
                $entity->{$key} = isset(self::$validators[$key]) ? call_user_func(self::$validators[$key], $infos) : $infos;

        return true;
    }

    protected static function createMultiLangField($field)
    {
        $languages = Language::getLanguages(false);
        $res = array();
        foreach ($languages as $lang)
            $res[$lang['id_lang']] = $field;
        return $res;
    }

    public static function setLocale()
    {
        $iso_lang  = trim(Tools::getValue('iso_lang'));
        setlocale(LC_COLLATE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
        setlocale(LC_CTYPE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
    }

}
