<?php

class CategoryController extends CategoryControllerCore
{
    public function init()
    {
        $egms_noid = Configuration::get('EGMS_NOID');
        if ($egms_noid) {
            if (Tools::getValue('category_rewrite')) {
                $category_rewrite = Tools::getValue('category_rewrite');

                $id_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_category`
				FROM `' . _DB_PREFIX_ . 'category_lang`
				WHERE `link_rewrite` = \'' . $category_rewrite . '\'');

                if ($id_category > 0) {
                    $_GET['id_category'] = $id_category;
                    //$_GET['noredirect'] = 1;
                } else {
                    Tools::display404Error();
                    die;
                }
            }
        }
        parent::init();
    }


    public function initContent()
    {
        parent::initContent();
/*
        $this->setTemplate(_PS_THEME_DIR_.'category.tpl');

        if (!$this->customer_access)
            return;

        if (isset($this->context->cookie->id_compare))
            $this->context->smarty->assign('compareProducts', CompareProduct::getCompareProducts((int)$this->context->cookie->id_compare));

        $this->productSort(); // Product sort must be called before assignProductList()

        $this->assignScenes();
        $this->assignSubcategories();
        $this->assignProductList();

        $products = (isset($this->cat_products) && $this->cat_products) ? $this->cat_products : null;
        foreach($products as &$pro)
        {
            $pro['combinations'] = Product::getProductAttributeCombinations($pro['id_product']);
        }

        $this->context->smarty->assign(array(
            'category' => $this->category,
            'description_short' => Tools::truncateString($this->category->description, 350),
            'products' => $products,
            'combinations' => $combinations,
            'id_category' => (int)$this->category->id,
            'id_category_parent' => (int)$this->category->id_parent,
            'return_category_name' => Tools::safeOutput($this->category->name),
            'path' => Tools::getPath($this->category->id),
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'categorySize' => Image::getSize(ImageType::getFormatedName('category')),
            'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
            'thumbSceneSize' => Image::getSize(ImageType::getFormatedName('m_scene')),
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'allow_oosp' => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
            'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'suppliers' => Supplier::getSuppliers(),
            'body_classes' => array($this->php_self.'-'.$this->category->id, $this->php_self.'-'.$this->category->link_rewrite)
        ));
        */
    }

}
