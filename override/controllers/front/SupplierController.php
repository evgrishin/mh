<?php

class SupplierController extends SupplierControllerCore
{
    public function init()
    {
        $egms_noid = Configuration::get('EGMS_NOID');

        if ($egms_noid) {

            if (Tools::getValue('supplier_rewrite')) {
                $name_supplier = str_replace('-', '%', Tools::getValue('supplier_rewrite'));
                $id_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_supplier`
				FROM `' . _DB_PREFIX_ . 'supplier`
				WHERE `name` LIKE \'' . $name_supplier . '\'');

                if ($id_supplier > 0) {
                    $_GET['id_supplier'] = $id_supplier;
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
