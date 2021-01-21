<?php

require_once(_PS_MODULE_DIR_.'egms/classes/egms_shop.php');
require_once(_PS_MODULE_DIR_.'egms/controllers/front/special.php');


class Product extends ProductCore

{
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
    {

        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);

       // $this->condition = "gift";
    }

	public static function checkAccessStatic($id_product, $id_customer)
	{
	//	$product = new Product($id_product);
		
	//	if (!egms_shop::getEgmsAccess($product->id_manufacturer))
        //return false;

		return parent::checkAccessStatic($id_product, $id_customer);
	}

    public static function getFeaturesStatic($id_product)
    {
        if (!Feature::isFeatureActive())
            return array();
        if (!array_key_exists($id_product, self::$_cacheFeatures))
            self::$_cacheFeatures[$id_product] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT fp.id_feature, fp.id_product, fp.id_feature_value, custom, ef.show_in_text
				FROM `'._DB_PREFIX_.'feature_product` fp
				INNER JOIN `'._DB_PREFIX_.'product` p on p.id_product = fp.id_product and p.id_category_default = ef.id_category_default
				LEFT JOIN `'._DB_PREFIX_.'feature_value` fv ON (fp.id_feature_value = fv.id_feature_value)
				LEFT JOIN `' . _DB_PREFIX_ . 'egms_features` ef on ef.id_feature = fp.id_feature
				WHERE `id_product` = '.(int)$id_product
            );
        return self::$_cacheFeatures[$id_product];
    }


    public static function getAttributesList($id_product, $id_attribute = null)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $cache_id = 'Product::getAttributesList_' . (int)$id_product . '-0-' . (int)$id_lang . '-' . (int)$id_shop;


        if (!Cache::isStored($cache_id)) {
            $s = '
			SELECT DISTINCT la.`id_attribute`, la.`url_name` as `name`, la.meta_title
			FROM `' . _DB_PREFIX_ . 'attribute` a
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
				ON (a.`id_attribute` = pac.`id_attribute`)
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
				ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
			' . Shop::addSqlAssociation('product_attribute', 'pa') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'layered_indexable_attribute_lang_value` la
				ON (la.`id_attribute` = a.`id_attribute` AND la.`id_lang` = ' . (int)$id_lang . ')
			WHERE la.`url_name` IS NOT NULL AND la.`url_name` != \'\'
			AND pa.`id_product` = ' . (int)$id_product;

            if (isset($id_product_attribute))
                $s .= ' AND pac.`id_product_attribute` = ' . (int)$id_product_attribute;
            $result = Db::getInstance()->executeS($s);

            Cache::store($cache_id, $result);
        }
        $result = Cache::retrieve($cache_id);
        return $result;
    }

    public static function getFrontFeaturesStatic($id_lang, $id_product)
    {
        if (!Feature::isFeatureActive())
            return array();
        if (!array_key_exists($id_product . '-' . $id_lang, self::$_frontFeaturesCache)) {
            $sql = '
				SELECT name, value, pf.id_feature , ef.id_category_default, ef.icon_class, ef.show_in_text as show_in_text, fvl.id_feature_value
				FROM ' . _DB_PREFIX_ . 'feature_product pf 
				LEFT join ' . _DB_PREFIX_ . 'product p on p.id_product = ' . $id_product . '  and p.id_product = pf.id_product
                LEFT JOIN ' . _DB_PREFIX_ . 'egms_features ef ON (pf.id_feature = ef.id_feature AND ef.id_category_default = p.id_category_default)				
				LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = ' . (int)$id_lang . ')
				LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int)$id_lang . ')
				LEFT JOIN ' . _DB_PREFIX_ . 'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = ' . (int)$id_lang . ')
				' . Shop::addSqlAssociation('feature', 'f') . '
				WHERE pf.id_product = ' . (int)$id_product;

            $sql .= ' ORDER BY f.position ASC';
            self::$_frontFeaturesCache[$id_product . '-' . $id_lang] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
        $ret = self::$_frontFeaturesCache[$id_product . '-' . $id_lang];
        return $ret;
    }

    public function getFrontFeatures($id_lang)
    {
        return Product::getFrontFeaturesStatic($id_lang, $this->id);
    }

    public function checkAccess($id_customer)
    {
       // if (!egms_shop::getEgmsAccess($this->id_manufacturer))
        //    $this->show_price = false;

        return Product::checkAccessStatic((int)$this->id, (int)$id_customer);
    }

    public static function getProductsProperties($id_lang, $query_result)
    {
        $results_array = array();

        if (is_array($query_result))
            foreach ($query_result as $row){
                if ($row2 = Product::getProductProperties($id_lang, $row)) {
                    $row2['condition'] = egmsspecialModuleFrontController::getSpecial($row['id_product'])['stiker'];
                   // $row2['features'][] = array();
                    $results_array[] = $row2;
                }
            }

        return $results_array;
    }

    public static function getIdTaxRulesGroupByIdProduct($id_product, Context $context = null)
    {
        return 1;
    }

    /** this function added for optimization reasons */
    public function getAccessories($id_lang, $active = true)
    {
        $sql = 'SELECT p.*, product_shop.*, 0 as out_of_stock, 0 as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`,
					pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
					MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` as manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(
						p.`date_add`,
						DATE_SUB(
							NOW(),
							INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
						)
					) > 0 AS new
				FROM `'._DB_PREFIX_.'accessory`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = `id_product_2`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (
					product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
            Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (p.`id_manufacturer`= m.`id_manufacturer`)
				WHERE `id_product_1` = '.(int)$this->id.
            ($active ? ' AND product_shop.`active` = 1 AND product_shop.`visibility` != \'none\'' : '').'
				GROUP BY product_shop.id_product';

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
            return false;

        foreach ($result as &$row)
            $row['id_product_attribute'] = Product::getDefaultAttribute((int)$row['id_product']);

        return $this->getProductsProperties($id_lang, $result);
    }
/*
    public static function getProductAttributeCombinations($id_product) {
        $combinations = array();
        $context = Context::getContext();
        $product = new Product ($id_product, $context->language->id);
        $attributes_groups = $product->getAttributesGroups($context->language->id);
        $att_grps = '';
        foreach ($attributes_groups as $k => $row)
        {
            $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
            $combinations[$row['id_product_attribute']]['attributes_group'][$row['id_attribute_group']] = $row['group_name'];

            $combinations[$row['id_product_attribute']]['attributes_groups'] = @implode(', ', $combinations[$row['id_product_attribute']]['attributes_group']);
            $att_grps = $row['public_group_name'];
            $combinations[$row['id_product_attribute']]['attributes_names'] = @implode(', ', $combinations[$row['id_product_attribute']]['attributes_values']);
            $combinations[$row['id_product_attribute']]['attributes'] = (int)$row['id_attribute'];
            $combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];

            foreach ($context->selected_filter['id_attribute_group'] as $g)
            $v = explode('_', $g);
            if ($v[0] == $row['id_attribute_group'] && $v[1] == $row['id_attribute'])
                $combinations[$row['id_product_attribute']]['selected'] = 'selected';
            else
                $combinations[$row['id_product_attribute']]['selected'] = '';

            // Call getPriceStatic in order to set $combination_specific_price
            if (!isset($combination_prices_set[(int)$row['id_product_attribute']]))
            {
                Product::getPriceStatic((int)$product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                $combination_prices_set[(int)$row['id_product_attribute']] = true;
                $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
            }
            $combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
            $combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
            $combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
            $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
            $combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
            $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
            if ($row['available_date'] != '0000-00-00')
            {
                $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                $combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
            }
            else
                $combinations[$row['id_product_attribute']]['available_date'] = '';
            foreach ($combinations as $id_product_attribute => $comb)
            {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute)
                    $attribute_list .= '\''.(int)$id_attribute.'\',';
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }
        }
        $t = 0;
        $comb = array(
            'attribute_groups' => $att_grps,
            'values' => $combinations
        );

        return $comb;
    }
    */

    public function getAttributesGroups($id_lang)
    {
        if (!Combination::isFeatureActive())
            return array();
        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
					a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
					0 as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
					product_attribute_shop.`default_on`, pa.`reference`, product_attribute_shop.`unit_price_impact`,
					product_attribute_shop.`minimal_quantity`, product_attribute_shop.`available_date`, ag.`group_type`
				FROM `'._DB_PREFIX_.'product_attribute` pa
				'.Shop::addSqlAssociation('product_attribute', 'pa').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
				'.Shop::addSqlAssociation('attribute', 'a').'
				WHERE pa.`id_product` = '.(int)$this->id.'
					AND al.`id_lang` = '.(int)$id_lang.'
					AND agl.`id_lang` = '.(int)$id_lang.'
				GROUP BY id_attribute_group, id_product_attribute
				ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';
        return Db::getInstance()->executeS($sql);
    }

}







