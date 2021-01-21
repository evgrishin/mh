<?php

class ManufacturerController extends ManufacturerControllerCore
{
    public function init()
    {
        $egms_noid = Configuration::get('EGMS_NOID');
        if ($egms_noid) {
            if (Tools::getValue('manufacturer_rewrite')) {
                $name_manufacturer = str_replace('-', '%', Tools::getValue('manufacturer_rewrite'));
                $id_manufacturer = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_manufacturer`
				FROM `' . _DB_PREFIX_ . 'manufacturer`
				WHERE `name` LIKE \'' . $name_manufacturer . '\'');

                if ($id_manufacturer > 0) {
                    $_GET['id_manufacturer'] = $id_manufacturer;
                    $_GET['noredirect'] = 1;
                } else {
                    Tools::display404Error();
                    die;
                }
            }
        }
        parent::init();
    }
}
