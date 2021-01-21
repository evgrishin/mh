<?php

class ProductController extends ProductControllerCore
{
    public function init()
    {
        $egms_noid = Configuration::get('EGMS_NOID');
        if ($egms_noid) {
            if (Tools::getValue('product_rewrite')) {
                $rewrite_url = Tools::getValue('product_rewrite');

                $id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_product`
				FROM `' . _DB_PREFIX_ . 'product_lang`
				WHERE `link_rewrite` = \'' . $rewrite_url . '\'');

                if ($id_product > 0) {
                    $_GET['id_product'] = $id_product;
                    //$_GET['noredirect'] = 1;
                } else {
                    Tools::display404Error();
                    die;
                }
            }
        }
        parent::init();
    }

    public function getProduct()
    {
        $this->product->condition = egmsspecialModuleFrontController::getSpecial($this->product->id)['stiker'];
        return $this->product;
    }

}
