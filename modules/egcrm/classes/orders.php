<?php
/**

 */



class orders extends ObjectModel
{
    /** @var string Name */
    public $id_order;
    public $seqn;
    public $id_shop;
    public $man_order;
    public $id_manufacturer;
    public $id_order_stat;
    public $bayer_name;
    public $phone;
    public $phone2;
    public $email;
    public $city;
    public $address;
    public $comment;
    public $date_add;
    public $date_delivery;
    public $id_carrier;
    public $id_payment;
    public $delivery_amount;
    public $id_rejection;
    public $rejection_comment;

        /**
     * @see ObjectModel::$definition
     */
    //TODO: add check fot length of fields
    public static $definition = array(
        'table' => 'egms_orders',
        'primary' => 'id_order', //array('id_order', 'seqn'),
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'seqn' => array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT, 'required' => true),
            'man_order' => array('type' => self::TYPE_STRING),
            'id_manufacturer' => array('type' => self::TYPE_INT),
            'id_order_stat' => array('type' => self::TYPE_INT),
            'bayer_name' => array('type' => self::TYPE_STRING),
            'phone' => array('type' => self::TYPE_STRING),
            'phone2' => array('type' => self::TYPE_STRING),
            'email' => array('type' => self::TYPE_STRING),
            'city' => array('type' => self::TYPE_STRING),
            'address' => array('type' => self::TYPE_STRING),
            'comment' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_delivery' => array('type' => self::TYPE_DATE),
            'id_carrier' => array('type' => self::TYPE_INT),
            'id_payment' => array('type' => self::TYPE_INT),
            'delivery_amount' => array('type' => self::TYPE_INT),
            'id_rejection' => array('type' => self::TYPE_INT),
            'rejection_comment' => array('type' => self::TYPE_STRING),
        ),
    );
    /*
        public function add($autodate = true, $null_values = false)
        {

            return parent::add($autodate, $null_values);
        }
    */
    public function delete()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egms_orders
		WHERE id_city ='.(int)$this->id_order;

      //  if (!Db::getInstance()->executeS($sql))
       //     return(parent::delete());

        return false;
    }

    public static function getOrder($id_order=null)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egms_orders';
        if ($id_order != null)
            $sql.= ' WHERE id_order='.(int)$id_order;
        $sql .= ' ORDER BY seqn';

        return (Db::getInstance()->executeS($sql));
    }
/*
    public static function getCityByShop($id=null)
    {
        $host = Tools::getHttpHost();

        $context = Context::getContext();
        $url = $_SERVER["HTTP_REFERER"];
        $base = $context->shop->getBaseURL();

        $subdomains = Configuration::get('EGMS_SUBDOMAIN');

        $sql = 'SELECT cu.`id_shop_url` as id, su.`domain`,
				c.`cityname1` as `city_name`, s.`name` as shop_name,
				REPLACE(su.`domain`, "'.".".$host.'", "") as alias, 
				concat("'.$host.'/", REPLACE(su.`domain`, "'.".".$host.'", "")) as host_dir,';

        if ($subdomains)
            $sql.='replace("'.$url.'", "'.$base.'", concat("//", su.`domain`,"/")) as url';
        else
            $sql.='replace("'.$url.'", "'.$base.'", concat("//'.$host.'/", REPLACE(su.`domain`, "'.".".$host.'", ""),"/")) as url';

        $sql.=' FROM '._DB_PREFIX_.'egms_city c
				INNER JOIN '._DB_PREFIX_.'egms_city_url cu ON cu.id_city = c.id_egms_city
				INNER JOIN '._DB_PREFIX_.'shop_url su ON cu.id_shop_url = su.id_shop_url
				INNER JOIN `'._DB_PREFIX_.'shop` s ON s.`id_shop`= su.`id_shop`
				WHERE cu.active=1
				and	su.active=1 ';
        if ($id != null)
            $sql .= ' and cu.`id_shop_url` = '.$id;
        $sql .=' AND su.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')
				ORDER BY c.cityname1';

        return (Db::getInstance()->executeS($sql));
    }
*/
}