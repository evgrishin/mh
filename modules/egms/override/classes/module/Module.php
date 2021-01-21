<?php



class Module extends ModuleCore

{



	protected function getCacheId($name = null)

	{

		$cache_array = array();

		$cache_array[] = $name !== null ? $name : $this->name;

		if (Configuration::get('PS_SSL_ENABLED'))

			$cache_array[] = (int)Tools::usingSecureMode();

		if (Shop::isFeatureActive())

			$cache_array[] = (int)$this->context->shop->id_shop_url;

		if (Group::isFeatureActive())

			$cache_array[] = (int)Group::getCurrent()->id;

		if (Language::isMultiLanguageActivated())

			$cache_array[] = (int)$this->context->language->id;

		if (Currency::isMultiCurrencyActivated())

			$cache_array[] = (int)$this->context->currency->id;

		$cache_array[] = (int)$this->context->country->id;

		return implode('|', $cache_array);

	}

}