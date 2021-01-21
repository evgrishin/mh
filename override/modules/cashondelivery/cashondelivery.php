<?php


if (!defined('_PS_VERSION_'))
	exit;

class CashOnDeliveryOverride extends CashOnDelivery
{
	public function hookPayment($params)
	{//75
		if (!Shop::paymentModuleIsAvaliable($this->id))
			return true;
		return parent::hookPayment($params);
	}

}
