<?php
/**

 */


//require_once(_PS_MODULE_DIR_.'egms/classes/egms_shop.php');

class egmsfilter extends ObjectModel
{
    /** @var string Name */
    public $id;
    public $id_filter;
    public $id_shop;
    public $id_category;
    public $id_category_redirect;
    public $url;
    public $link_name;
    public $f_date;
    public $index;
    public $bread;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $title;
    public $description;


    /**
     * @see ObjectModel::$definition
     */
    //TODO: add check fot length of fields
    public static $definition = array(
        'table' => 'egms_filter',
        'primary' => 'id_filter',
        'fields' => array(
            'id_filter' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_shop' => array('type' => self::TYPE_INT, 'required' => false),
            'id_category' => array('type' => self::TYPE_INT, 'required' => true),
            'id_category_redirect' => array('type' => self::TYPE_INT, 'required' => false),
            'url' => array('type' => self::TYPE_STRING, 'required' => true),
            'link_name' => array('type' => self::TYPE_STRING, 'required' => false),
            'f_date' => array('type' => self::TYPE_DATE, 'required' => true),
            'index' => array('type' => self::TYPE_INT, 'required' => true),
            'bread' => array('type' => self::TYPE_STRING, 'required' => false),
            'meta_title' => array('type' => self::TYPE_STRING, 'required' => false),
            'meta_description' => array('type' => self::TYPE_STRING, 'required' => false),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'required' => false),
            'title' => array('type' => self::TYPE_STRING, 'required' => false),
            'description' => array('type' => self::TYPE_HTML, 'lang' => false),
        ),
    );
    /*
        public function add($autodate = true, $null_values = false)
        {

            return parent::add($autodate, $null_values);
        }
    */
    public function __construct($id = null, $params){

        parent::__construct($id);
        if (!$id){

            $this->id_shop = 1;
            $this->id_category = 0;
            $this->id_category_redirect = 0;
            $this->url = '/none';
            $this->link_name = $params['h1'];
            $this->f_date =date("Y-m-d H:i:s");;
            $this->index = 0;
            $this->meta_title = $params['meta_title'];
            $this->meta_description = $params['meta_description'];
            $this->meta_keywords = $params['meta_keywords'];
            $this->title = $params['h1'];
            $this->save();
        }
    }

    public static function getSubCategorys($id_filter, $link)
    {

        $sql = 'SELECT f.id_filter, id_category, url, f.link_name, "' . $link . '" as category_link
                FROM ' . _DB_PREFIX_ . 'egms_filter f
                INNER JOIN ' . _DB_PREFIX_ . 'egms_filter_category fc ON fc.id_filter = f.id_filter 
                WHERE fc.id_filter_parent =' . $id_filter;

        $r = Db::getInstance()->executeS($sql);
        return ($r);
    }

    public function delete()
    {
        return false;
    }

    public static function getFilter($id_filter=null)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egms_filter';
        if ($id_filter != null)
            $sql.= ' WHERE id_filter='.(int)$id_filter;
        $sql .= ' ORDER BY id_filter';

        return (Db::getInstance()->executeS($sql));
    }

    public static function getRedirectUrl($id_category)
    {
        $id_shop = Context::getContext()->shop->id;

        $cache_id = 'egmsfilter::getRedirectUrl_' . $id_category;

        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'egms_filter';
            $sql .= " WHERE id_category_redirect = " . $id_category;
            $sql .= " AND `index`= 1";
            $sql .= " AND id_shop=" . (int)$id_shop;

            $result = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $result);
        }
        $result = Cache::retrieve($cache_id);

        return $result[0];
    }

    public static function getFilterByUrl($url=null, $id_category)
    {
        if (trim($url) == '')
            return null;

        $id_shop = Context::getContext()->shop->id;

        $cache_id = 'egmsfilter::getFilterByUrl_'.(int)$url.'-'.$id_shop.'-'.$id_category;

        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'egms_filter';
            $sql .= " WHERE url='" . $url . "'";
            $sql .= " AND `index` = 1 ";
            $sql .= " AND id_category = " . $id_category;
            $sql .= " AND id_shop=" . (int)$id_shop;

            $result = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $result);
        }
        $result = Cache::retrieve($cache_id);


        if(!$result)
            $result = array(array('id_filter' => '',
                                    'id_shop' => '',
                                    'id_category' => '',
                                    'id_category_redirect' => '',
                                    'url' => '',
                'link_name' => '',
                                    'index' => '',
                                    'bread' => '',
                                    'meta_title' => '',
                                    'meta_description' => '',
                                    'meta_keywords' => '',
                                    'title' => '',
                                    'description' => '',));

        return ($result);
    }

    public static function getIndexFilters($id_category)
    {

        $sql = 'SELECT url FROM '._DB_PREFIX_.'egms_filter';
        $sql.= " WHERE id_category=".$id_category;
        $sql.= " AND id_shop=".(int)Context::getContext()->shop->id;
        $sql.= " AND `index`= 1";
        $result_temp = array();
        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $r)
            $result_temp[] = $r['url'];

        return ($result_temp);
    }

    public static function addFilter($params){

        $filter = new egmsfilter(null, $params);
       // $sql = "UPDATE " . _DB_PREFIX_ . "egploader SET id_pproduct = ".$pproduct->id." WHERE id_load = ".$id_load;
        //$r = Db::getInstance()->execute($sql);
        return $filter->id;

    }



    public static function getFilterData($url = null, $id_category)
    {

        $url = strtok($url, '?');
        $url = strtok($url, '#');

        if (trim($url) == '')
            $url = "/";


        $row = egmsfilter::getFilterByUrl($url, $id_category);

        $content = Meta::replaceForCEO($row);

        return $content[0];
    }

}