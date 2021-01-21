<?php
/**
 *  2017 ModuleFactory.co
 *
 *  @author    ModuleFactory.co <info@modulefactory.co>
 *  @copyright 2017 ModuleFactory.co
 *  @license   ModuleFactory.co Commercial License
 */

class Link extends LinkCore


{


    public function getImageLink($name, $ids, $type = null)
    {
        $not_default = false;

        // Check if module is installed, enabled, customer is logged in and watermark logged option is on
        if (Configuration::get('WATERMARK_LOGGED') && (Module::isInstalled('watermark') && Module::isEnabled('watermark')) && isset(Context::getContext()->customer->id))
            $type .= '-'.Configuration::get('WATERMARK_HASH');

        // legacy mode or default image
        $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
        if ((Configuration::get('PS_LEGACY_IMAGES')
                && (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
            || ($not_default = strpos($ids, 'default') !== false))
        {
            if ($this->allow == 1 && !$not_default)
                $uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            else
                $uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg';
        }
        else
        {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
            if ($this->allow == 1) {
                $uri_path = __PS_BASE_URI__ .'themes/media/'. $id_image . ($type ? '-' . $type : '') . $theme . '/' . $name . '.jpg';
                $t = $uri_path;
            }
            else {
                $uri_path = _THEME_PROD_DIR_ . Image::getImgFolderStatic($id_image) . $id_image . ($type ? '-' . $type : '') . $theme . '.jpg';
                $t = $uri_path;
            }
        }
        $p = $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
        return  $p;
    }


    /*
	* module: fsadvancedurl
	* date: 2019-03-12 14:52:38
	* version: 2.1.2
	*/
	protected function getLangLink($id_lang = null, Context $context = null, $id_shop = null)
    {
        if (Configuration::get('FSAU_REMOVE_DEFAULT_LANG', null, null, $id_shop) &&
            Language::isMultiLanguageActivated()) {
            if (!$id_lang) {
                if (is_null($context)) {
                    $context = Context::getContext();
                }

                $id_lang = $context->language->id;
            }

            if ($id_lang == Configuration::get('PS_LANG_DEFAULT', null, null, $id_shop)) {
                return '';
            }
        }

        return parent::getLangLink($id_lang, $context, $id_shop);
    }

    /*
	* module: fsadvancedurl
	* date: 2019-03-12 14:52:38
	* version: 2.1.2
	*/
	public function getCategoryLink(
        $category,
        $alias = null,
        $id_lang = null,
        $selected_filters = null,
        $id_shop = null,
        $relative_protocol = false
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, null, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        if (!is_object($category)) {
            if (is_array($category) && isset($category['id_category'])) {
                $category = new Category($category['id_category'], $id_lang);
            } elseif ((int)$category) {
                $category = new Category((int)$category, $id_lang);
            } else {
                return '';
            }
        }

        $params = array();
        $params['id'] = $category->id;
        $params['rewrite'] = (!$alias) ? $category->getFieldByLang('link_rewrite') : $alias;
        $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
        $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));

        $dispatcher = Dispatcher::getInstance();
        if ($dispatcher->hasKeyword('category_rule', $id_lang, 'categories', $id_shop)) {
            $cats = array();
            foreach ($category->getParentsCategories($id_lang) as $cat) {
                if (!in_array($cat['id_category'], Link::$category_disable_rewrite)) {
                    $cats[] = $cat['link_rewrite'];
                }
            }
            $cats = array_reverse($cats);
            array_pop($cats);
            $params['categories'] = implode('/', $cats);
        }

        $selected_filters = is_null($selected_filters) ? '' : $selected_filters;
        if (empty($selected_filters)) {
            $rule = 'category_rule';
        } else {
            $rule = 'layered_rule';
            $params['selected_filters'] = $selected_filters;
        }

        return $url.Dispatcher::getInstance()->createUrl($rule, $id_lang, $params, $this->allow, '', $id_shop);
    }

    /*
	* module: fsadvancedurl
	* date: 2019-03-12 14:52:38
	* version: 2.1.2
	*/
	public function getCMSCategoryLink(
        $cms_category,
        $alias = null,
        $id_lang = null,
        $id_shop = null,
        $relative_protocol = false
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, null, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cms_category)) {
            $cms_category = new CMSCategory((int)$cms_category, $id_lang);
        }

                $params = array();
        $params['id'] = $cms_category->id;

        $params['rewrite'] = $cms_category->link_rewrite;
        if (is_array($params['rewrite']) && isset($params['rewrite'][(int)$id_lang])) {
            $params['rewrite'] = $params['rewrite'][(int)$id_lang];
        }
        if ($alias) {
            $params['rewrite'] = $alias;
        }

        $params['meta_keywords'] = $cms_category->meta_keywords;
        if (is_array($params['meta_keywords']) && isset($params['meta_keywords'][(int)$id_lang])) {
            $params['meta_keywords'] = Tools::str2url($params['meta_keywords'][(int)$id_lang]);
        }

        $params['meta_title'] = $cms_category->meta_title;
        if (is_array($params['meta_title']) && isset($params['meta_title'][(int)$id_lang])) {
            $params['meta_title'] = Tools::str2url($params['meta_title'][(int)$id_lang]);
        }

        if ($dispatcher->hasKeyword('cms_category_rule', $id_lang, 'categories', $id_shop)) {
            $cats = array();
            if (Module::isEnabled('fsadvancedurl')) {
                $fsau = Module::getInstanceByName('fsadvancedurl');
                $categories = $fsau->getCMSCategoryParentCategories($cms_category->id, $id_lang);
                if ($categories) {
                    foreach ($categories as $cat) {
                        $cats[] = $cat['link_rewrite'];
                    }
                    $cats = array_reverse($cats);
                    array_pop($cats);
                }
            }
            $params['categories'] = implode('/', $cats);
        }

        return $url.$dispatcher->createUrl('cms_category_rule', $id_lang, $params, $this->allow, '', $id_shop);
    }

    /*
	* module: fsadvancedurl
	* date: 2019-03-12 14:52:38
	* version: 2.1.2
	*/
	public function getCMSLink(
        $cms,
        $alias = null,
        $ssl = null,
        $id_lang = null,
        $id_shop = null,
        $relative_protocol = false
    ) {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cms)) {
            $cms = new CMS((int)$cms, $id_lang);
        }

                $params = array();
        $params['id'] = $cms->id;

        $params['rewrite'] = $cms->link_rewrite;
        if (is_array($params['rewrite']) && isset($params['rewrite'][(int)$id_lang])) {
            $params['rewrite'] = $params['rewrite'][(int)$id_lang];
        }
        if ($alias) {
            $params['rewrite'] = $alias;
        }

        $params['meta_keywords'] = $cms->meta_keywords;
        if (is_array($params['meta_keywords']) && isset($params['meta_keywords'][(int)$id_lang])) {
            $params['meta_keywords'] = Tools::str2url($params['meta_keywords'][(int)$id_lang]);
        }

        $params['meta_title'] = $cms->meta_title;
        if (is_array($params['meta_title']) && isset($params['meta_title'][(int)$id_lang])) {
            $params['meta_title'] = Tools::str2url($params['meta_title'][(int)$id_lang]);
        }

        if ($dispatcher->hasKeyword('cms_rule', $id_lang, 'categories', $id_shop)) {
            $cats = array();
            $cms_category = new CMSCategory($cms->id_cms_category, $id_lang);
            if (Validate::isLoadedObject($cms_category)) {
                if (Module::isEnabled('fsadvancedurl')) {
                    $fsau = Module::getInstanceByName('fsadvancedurl');
                    $categories = $fsau->getCMSCategoryParentCategories($cms_category->id, $id_lang);
                    if ($categories) {
                        foreach ($categories as $cat) {
                            $cats[] = $cat['link_rewrite'];
                        }
                        $cats = array_reverse($cats);
                    }
                }
            }
            $params['categories'] = implode('/', $cats);
        }

        return $url.$dispatcher->createUrl('cms_rule', $id_lang, $params, $this->allow, '', $id_shop);
    }
}
