<?php

class CmsController extends CmsControllerCore
{
    public function init()
    {
        $egms_noid = Configuration::get('EGMS_NOID');
        if ($egms_noid) {

            if (Tools::getValue('cms_rewrite')) {
                $rewrite_url = Tools::getValue('cms_rewrite');

                $id_cms = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_cms`
				FROM `' . _DB_PREFIX_ . 'cms_lang`
				WHERE `link_rewrite` = \'' . $rewrite_url . '\'');

                if ($id_cms > 0) {
                    $_GET['id_cms'] = $id_cms;
                    //$_GET['noredirect'] = 1;
                } else {
                    Tools::display404Error();
                    die;
                }
            } else if (Tools::getValue('cms_category_rewrite')) {
                $rewrite_url = Tools::getValue('cms_category_rewrite');

                $id_cms_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_cms_category`
				FROM `' . _DB_PREFIX_ . 'cms_category_lang`
				WHERE `link_rewrite` = \'' . $rewrite_url . '\'');

                if ($id_cms_category > 0) {
                    $_GET['id_cms_category'] = $id_cms_category;
                    //$_GET['noredirect'] = 1;
                } else {
                    Tools::display404Error();
                    die;
                }
            }
        }
        parent::init();
    }
}
