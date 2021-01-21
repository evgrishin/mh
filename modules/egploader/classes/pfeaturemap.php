<?php

/**

 */

//require_once(_PS_MODULE_DIR_.'egploader/classes/productloader.php');

class pfeaturemap extends ObjectModel
{
    /** @var string Name */
    public $id;
    public $id_load_feature_map;
    public $provider;
    public $feature_load_name;
    public $feature_load_value;
    public $id_feature;
    public $id_feature_value;
    public $active;


    /**
     * @see ObjectModel::$definition
     */
    //TODO: add check fot length of fields
    public static $definition = array(
        'table' => 'egploader_product_feature_map',
        'primary' => 'id_load_feature_map',
        'fields' => array(
            'id_load_feature_map' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'provider' => array('type' => self::TYPE_STRING, 'required' => true),
            'feature_load_name' => array('type' => self::TYPE_STRING, 'required' => true),
            'feature_load_value' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_feature' => array('type' => self::TYPE_INT, 'required' => true),
            'id_feature_value' => array('type' => self::TYPE_INT, 'required' => true),
            'active' => array('type' => self::TYPE_INT, 'required' => true),
        ),
    );


    public function delete()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egploader_product_feature_map
		WHERE id_load_feature_map ='.(int)$this->id_load_feature_map;

        if (!Db::getInstance()->executeS($sql))
            return(false);

        return (parent::delete());
    }

    public static function getFeatureMap($id_load_feature_map=null)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'egploader_product_feature_map';
        if ($id_load_feature_map != null)
            $sql.= ' WHERE id_load_feature_map='.(int)$id_load_feature_map;
        $sql .= ' ORDER BY id_load_feature_map';

        return (Db::getInstance()->executeS($sql));
    }


    public function update($null_values = false)
    {

        $id_feature_value = $this->id_feature_value;

        $sql = "SELECT fv.id_feature FROM " . _DB_PREFIX_ . "feature_value  fv
                    INNER JOIN " . _DB_PREFIX_ . "feature_lang fl ON fl.id_feature = fv.id_feature
                    INNER JOIN " . _DB_PREFIX_ . "feature_value_lang fvl ON fvl.id_feature_value = fv.id_feature_value
                where custom = 0
                AND fvl.id_feature_value=".$id_feature_value;

        $features = Db::getInstance()->getValue($sql);


        $this->id_feature = $features;
        $new_value=Tools::getValue('new_value');
        if($new_value&&$this->id_feature_value>0)
        {
            $id_feature_value = FeatureValue::addFeatureValueImport($this->id_feature, $new_value, null, 1, false);
            $this->id_feature_value = $id_feature_value;
        }

        if(Tools::getValue('dublicate'))
        {
            $sql = "INSERT INTO " . _DB_PREFIX_ . "egploader_product_feature_map (`provider`,`feature_load_name`,`feature_load_value`)
                    VALUES ('".$this->provider."','".$this->feature_load_name."','".$this->feature_load_value."')";

           $r = Db::getInstance()->execute($sql);
        }

        return parent::update($null_values);
    }


}
