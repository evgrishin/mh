<?php
require_once 'TinkoffMerchantAPI.php';

class tinkoff extends PaymentModule
{
    private $_html = '';
    private $_postErrors = array();
    public $ti_merchant_id;
    public $ti_secret_key;
    public $ti_gateway;
    public $ti_testmode;

    public function __construct()
    {
        $this->name = 'tinkoff';
        $this->tab = 'Payment';
        $this->version = 1.0;
        $this->author = 'Tinkoff';

        $this->currencies = true;
        $this->currencies_mode = 'radio';

        $config = Configuration::getMultiple(array('TI_MERCHANT_ID', 'TI_SECRET_KEY', 'TI_GATEWAY', 'TI_TESTMODE'));
        if (isset($config['TI_MERCHANT_ID']))
            $this->ti_merchant_id = $config['TI_MERCHANT_ID'];
        if (isset($config['TI_SECRET_KEY']))
            $this->ti_secret_key = $config['TI_SECRET_KEY'];
        if (isset($config['TI_GATEWAY']))
            $this->ti_gateway = $config['TI_GATEWAY'];
        if (isset($config['TI_TESTMODE']))
            $this->ti_testmode = $config['TI_TESTMODE'];

        parent::__construct();

        /* The parent construct is required for translations */
        $this->page = basename(__FILE__, '.php');
        $this->displayName = 'Tinkoff';
        $this->description = $this->l('Accept payments with Tinkoff');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
    }

    public function install()
    {
        if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
            return false;

        Configuration::updateValue('TI_MERCHANT_ID', '');
        Configuration::updateValue('TI_SECRET_KEY', '');
        Configuration::updateValue('TI_GATEWAY', '');
        Configuration::updateValue('TI_TESTMODE', '1');

        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('TI_MERCHANT_ID');
        Configuration::deleteByName('TI_SECRET_KEY');
        Configuration::deleteByName('TI_GATEWAY');
        Configuration::deleteByName('TI_TESTMODE');

        parent::uninstall();
    }

    private function _postValidation()
    {
        if (isset($_POST['btnSubmit']))
        {
            if (empty($_POST['ti_merchant_id']))
                $this->_postErrors[] = $this->l('Merchant ID is required');
            elseif (empty($_POST['ti_secret_key']))
                $this->_postErrors[] = $this->l('Secret key is required');
        }
    }

    private function _postProcess()
    {
        if (isset($_POST['btnSubmit']))
        {
            if(!isset($_POST['ti_testmode']))
                $_POST['ti_testmode'] = 0;

            Configuration::updateValue('TI_MERCHANT_ID', $_POST['ti_merchant_id']);
            Configuration::updateValue('TI_SECRET_KEY', $_POST['ti_secret_key']);
            Configuration::updateValue('TI_GATEWAY', $_POST['ti_lifetime']);
            Configuration::updateValue('TI_TESTMODE', $_POST['ti_testmode']);
        }
        $this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('OK').'" /> '.$this->l('Settings updated').'</div>';
    }

    private function _displayRb()
    {
        $this->_html .= '<img src="../modules/tinkoff/tinkoff.png" style="float:left; margin-right:15px;"><b>'.$this->l('This module allows you to accept payments by Tinkoff.').'</b><br /><br />';
    }

    private function _displayForm()
    {
        $bTestMode = htmlentities(Tools::getValue('ti_testmode', $this->ti_testmode), ENT_COMPAT, 'UTF-8');
        $checked = '';
        if($bTestMode)
            $checked = 'checked="checked"';

        $this->_html .=
            '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset>
            <legend><img src="../img/admin/contact.gif" />'.$this->l('Contact details').'</legend>
                <table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
                    <tr><td colspan="2">'.$this->l('Please specify required data').'.<br /><br /></td></tr>
                    <tr><td width="140" style="height: 35px;">'.$this->l('Merchant ID').'</td><td><input type="text" name="ti_merchant_id" value="'.htmlentities(Tools::getValue('ti_merchant_id', $this->ti_merchant_id), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
                    <tr><td width="140" style="height: 35px;">'.$this->l('Secret key').'</td><td><input type="text" name="ti_secret_key" value="'.htmlentities(Tools::getValue('ti_secret_key', $this->ti_secret_key), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
                    <tr><td width="140" style="height: 35px;">'.$this->l('Payment gateway').'</td><td><input type="text" name="ti_lifetime" value="'.htmlentities(Tools::getValue('ti_lifetime', $this->ti_gateway), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" /></td></tr>
                    <tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
                </table>
            </fieldset>
        </form>';
    }

    public function getContent()
    {
        $this->_html = '<h2>'.$this->displayName.'</h2>';

        if (!empty($_POST))
        {
            $this->_postValidation();
            if (!sizeof($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors AS $err)
                    $this->_html .= '<div class="alert error">'. $err .'</div>';
        }
        else
            $this->_html .= '<br />';

        $this->_displayRb();
        $this->_displayForm();

        return $this->_html;
    }

    public function hookPayment($params)
    {
        global $smarty;

        $cookie = $this->context->cookie;
        $customer = new Customer((int)$cookie->id_customer);
        $nTotalPrice = $params['cart']->getOrderTotal(true, 3);

        $arrOrderItems = $params['cart']->getProducts();
        $strDescription = '';
        foreach($arrOrderItems as $arrItem){
            $strDescription .= $arrItem['name'];
            if(!empty($arrItem['attributes_small']))
                $strDescription .= $arrItem['attributes_small'];
            if($arrItem['cart_quantity'] > 1)
                $strDescription .= "*".$arrItem['cart_quantity'];
            $strDescription .= " ";
        }


        $arrFields = array(
            'OrderId'			=> $params['cart']->id,
            'Amount'			=> $nTotalPrice,
            'Description'		=> $strDescription,
            'DATA'              => 'Email='.$cookie->email,
        );

        $smarty->caching           = false;
        $smarty->force_compile     = true;
        $smarty->compile_check     = false;

        $smarty->assign('arrFields', $arrFields);
        return $this->display(__FILE__, 'tinkoff.tpl');
    }

    public function getL($key)
    {
        $translations = array(
            'success'=> 'Tinkoff transaction is carried out successfully.',
            'fail'=> 'Tinkoff transaction is refused.'
        );
        return $translations[$key];
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active)
            return ;

        return $this->display(__FILE__, 'confirmation.tpl');
    }

}

?>
