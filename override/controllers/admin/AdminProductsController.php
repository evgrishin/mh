<?php
/**
 * Created by PhpStorm.
 * User: Evgeny Grishin
 * Date: 02.10.2018
 * Time: 1:21
 */

class AdminProductsController extends AdminProductsControllerCore
{


    public function initFormFeatures($obj)
    {
        if (!$this->default_form_language)
            $this->getLanguages();

        $data = $this->createTemplate($this->tpl_form);
        $data->assign('default_form_language', $this->default_form_language);
        $data->assign('languages', $this->_languages);

        if (!Feature::isFeatureActive())
            $this->displayWarning($this->l('This feature has been disabled. ').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
        else
        {
            if ($obj->id)
            {
                if ($this->product_exists_in_shop)
                {
                    $features = Feature::getFeatures_cust($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP), $obj->id);

                    foreach ($features as $k => $tab_features)
                    {
                        $features[$k]['current_item'] = false;
                        $features[$k]['val'] = array();

                        $custom = true;
                        foreach ($obj->getFeatures() as $tab_products)
                            if ($tab_products['id_feature'] == $tab_features['id_feature'])
                                $features[$k]['current_item'] = $tab_products['id_feature_value'];

                        $features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int)$tab_features['id_feature']);
                        if (count($features[$k]['featureValues']))
                            foreach ($features[$k]['featureValues'] as $value)
                                if ($features[$k]['current_item'] == $value['id_feature_value'])
                                    $custom = false;

                        if ($custom)
                            $features[$k]['val'] = FeatureValue::getFeatureValueLang($features[$k]['current_item']);
                    }

                    $data->assign('available_features', $features);
                    $data->assign('product', $obj);
                    $data->assign('link', $this->context->link);
                    $data->assign('default_form_language', $this->default_form_language);
                }
                else
                    $this->displayWarning($this->l('You must save the product in this shop before adding features.'));
            }
            else
                $this->displayWarning($this->l('You must save this product before adding features.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }


}