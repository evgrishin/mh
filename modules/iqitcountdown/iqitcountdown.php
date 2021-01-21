<?php



if (!defined('_PS_VERSION_'))
	exit;

class IqitCountDown extends Module
{
	protected $html = '';

	public function __construct()
	{
		$this->name = 'iqitcountdown';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'IQIT-COMMERCE.COM';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Special price countdown');
		$this->description = $this->l('Show time to end of special price');
	}



	public function install() {
		return parent::install() && $this->registerHook('displayCountDown')  && $this->registerHook('top') && $this->registerHook('displayCountDownProduct') && $this->registerHook('displayHeader');
	}

		

	public function hookDisplayCountDown($params) 

	{
		if(!isset($params['product']['specific_prices']['to']))
			return;

		$today = date("Y-m-d H:i:s");
		$date = $params['product']['specific_prices']['to'];

		if ($date > $today) {
				$this->smarty->assign(array(		
			'specific_prices'	=>	$params['product']['specific_prices']
			));

		return $this->display(__FILE__, 'iqitcountdown.tpl');
		}
		else return;



	

	}

		public function hookDisplayCountDownProduct($params) 

	{

		if(isset($params['product']->specificPrice)){
			$this->smarty->assign(array(		
				'specific_prices'	=>	$params['product']->specificPrice
				));
		}

		return $this->display(__FILE__, 'iqitcountdown_product.tpl');

	}

	


	

	public function hookDisplayHeader($params)

	{	

	if (Configuration::get('PS_CATALOG_MODE'))
	return;
	$this->context->controller->addCss($this->_path.'css/iqitcountdown.css', 'all');
	$this->context->controller->addJS($this->_path.'js/count.js');
	$this->context->controller->addJS($this->_path.'js/iqitcountdown.js');
	Media::addJsDef(array('countdownEnabled' => true));
	

	}

	public function hookTop($params)
	{
		return $this->display(__FILE__, 'iqitcountdown_top.tpl');
	}
	



}