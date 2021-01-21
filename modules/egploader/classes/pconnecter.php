<?php

/**

 */

require_once(_PS_MODULE_DIR_.'egploader/classes/loadmanager.php');

class pconnecter extends ObjectModel
{
    /** @var string Name */
    public $id_connecter;
    public $provider;
    public $connection_type;
    public $url_sitemap;
    public $load_datetime;
    public $log;

    public $url_arrays = array();

    /**
     * @see ObjectModel::$definition
     */
    //TODO: add check fot length of fields
    public static $definition = array(
        'table' => 'egploader_connecter',
        'primary' => 'id_connecter',
        'fields' => array(
            'id_connecter' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'provider' => array('type' => self::TYPE_STRING, 'required' => true),
            'connection_type' => array('type' => self::TYPE_INT, 'required' => true),
            'url_sitemap' => array('type' => self::TYPE_STRING, 'required' => true),
            'load_datetime' => array('type' => self::TYPE_DATE, 'required' => false),
            'log' => array('type' => self::TYPE_STRING, 'required' => false),
        ),
    );



    public function delete()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egploader_connecter
		WHERE id_connecter ='.(int)$this->id_connecter;

        if (!Db::getInstance()->executeS($sql))
            return(false);

        return (parent::delete());
    }

    public static function getConnecter($id_connecter=null)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egploader_connecter';
        if ($id_connecter != null)
            $sql.= ' WHERE id_connecter='.(int)$id_connecter;
        $sql .= ' ORDER BY id_connecter';

        return (Db::getInstance()->executeS($sql));
    }

    public function fillPconnecter($id_connecter)
    {
        $res = pconnecter::getConnecter($id_connecter);
        foreach ($res as $r)
        {
            $this->id_connecter = $r['id_connecter'];
            $this->provider = $r['provider'];
            $this->connection_type = $r['connection_type'];
            $this->url_sitemap = $r['url_sitemap'];
            $this->load_datetime = $r['load_datetime'];
            $this->log = $r['log'];
        }

    }

    public function download($id_connecter = null, $actions = "execute")
    {
        $this->fillPconnecter($id_connecter);

        $type = ($this->connection_type == 1)?"sitemap":"category";
        $loader = new LoadManager();
        $loader->setVariables(0, $this->provider, 0, 0);
        $loader->getContent($this->url_sitemap);

        $products = $loader->getDataFromSitemap($type);
        $i_exist = 0;
        $i_addad = 0;
        foreach ($products as $product){

            $e = ploader::productExist($product);
            if($e)
                $i_exist ++;
            else
            {
                $i_addad++;
                ploader::addLoadProduct($product, $this->provider);
            }
        }
        $this->updateDatetime($id_connecter);

        $result = 'Connection: <font style="color:green;">'.$id_connecter.'</font>; Founeded: '.count($products).'; Addad: '.$i_addad.'; Exist: '.$i_exist.';<br>';
        return $result;
    }

    public function updateDatetime($id_connecter)
    {
        $sql = "UPDATE " . _DB_PREFIX_ . "egploader_connecter SET load_datetime = now() where id_connecter = ".$id_connecter;

        return Db::getInstance()->execute($sql);
    }



    public function update($null_values = false)
    {

        return parent::update($null_values);

    }


    public function add($autodate = true, $null_values = false, $val)
    {
        $url_sitemap_array = explode(PHP_EOL, $this->url_sitemap);

        foreach ($url_sitemap_array as $url)
        {
            //if(substr($url, 0, 4) == "http")

            $this->url_sitemap = $url;

            $result = parent::add($autodate, $null_values);
        }

        return $result;
    }


}