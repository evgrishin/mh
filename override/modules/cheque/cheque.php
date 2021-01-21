<?php

if (!defined('_PS_VERSION_'))
	exit;

class ChequeOverride extends Cheque
{
	public function hookPayment($params)
	{//108
		if (!Shop::paymentModuleIsAvaliable($this->id))
			return true;
		return  parent::hookPayment($params);
	}
}
