<?php

class HomepageAdvertise2 extends Module {
	
	private $_postErrors = array();
	
	function __construct() {
		$this->name = 'homepageadvertise2';
		$this->tab = 'front_office_features';
		$this->version = 1.7;
		$this -> author = 'IQIT-COMMERCE.COM';
		$this->bootstrap = true;
		
		parent::__construct();
		$this->displayName = $this->l('Hompage adverts2');
		$this->description = $this->l('Display image adverts on homepage');
	}
	
	public function install()
	{
		$this->_clearCache('homepageadvertise2.tpl');
		if (!parent::install() OR !$this->registerHook('home') OR !$this->registerHook('header') OR !$this->registerHook('displayAdditionalFooter'))
			return false;
		
		$images = $this->_parseImageDir();
		if (!$images)
			return false;

		$langs = '';
		$curr = '';
		for($i = 0; $i < count($images); $i++)
		{	
			$curr .= 'all;';
			$langs .= 'all;';
		}

		if (!Configuration::updateValue($this->name.'_links'	, '')		OR
			!Configuration::updateValue($this->name.'_langs'	, $langs)	OR
			!Configuration::updateValue($this->name.'_curr'	, $curr)	OR
			!Configuration::updateValue($this->name.'_number'	, 2)	OR
			!Configuration::updateValue($this->name.'_hook'	, 2)	OR
			!Configuration::updateValue($this->name.'_images'	, implode(";", $images)))
			return false;
		
		return true;
	}

	private function _postProcess()
	{
		Configuration::updateValue($this->name.'_images'	,  Tools::getValue('image_data'));
		Configuration::updateValue($this->name.'_links'		,  Tools::getValue('link_data'));
		Configuration::updateValue($this->name.'_langs'		,  Tools::getValue('lang_data'));
		Configuration::updateValue($this->name.'_hook'		,  Tools::getValue('hookname'));
		Configuration::updateValue($this->name.'_number'	,  Tools::getValue('number_per_line'));
		Configuration::updateValue($this->name.'_curr'		,  Tools::getValue('curr_data'));
		$html = '';
		$html .= $this->displayConfirmation($this->l('Settings updated'));
		
	}

	public function getContent() {

		$html = '';
		if (Tools::isSubmit('submit'))
		{			
			
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
			{
				foreach ($this->_postErrors as $err)
				{
					$html .= '<div class="alert error">'.$err.'</div>';
				}
			}
			$this->_clearCache('homepageadvertise2.tpl');
		}
		
		
		$dirImages = $this->_parseImageDir();
		$confImages = $this->_getImageArray();
		$nbDirImages = count($dirImages);
		$nbConfImages = count($confImages);
		

		
		$html .= '
		<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
		<script type="text/javascript" src="../modules/'.$this->name.'/ajaxupload.js"></script>
		<script type="text/javascript" src="../modules/'.$this->name.'/homepageadvertise2.js"></script>

		<table id="hidden-row" style="display:none">' . $this->_getTableRowHTML(0, 2, '') . '</table>
		
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<div class="panel">
		<div class="panel-heading">'.$this->l('Settings').'</div>



		<input type="hidden" id="hidden_image_data" name="image_data" value="' . Configuration::get($this->name.'_images') . '"/>
		<input type="hidden" id="hidden_link_data" name="link_data" value="'   . Configuration::get($this->name.'_links') . '"/>
		<input type="hidden" id="hidden_lang_data" name="lang_data" value="'   . Configuration::get($this->name.'_langs') . '"/>
		<input type="hidden" id="hidden_curr_data" name="curr_data" value="'   . Configuration::get($this->name.'_curr') . '"/>
		Hook<select name="hookname">
		<option ' . ((Configuration::get($this->name.'_hook') == 1) ? 'selected="selected" ' : '') . 'value="1">Home</option>
		<option ' . ((Configuration::get($this->name.'_hook') == 2) ? 'selected="selected" ' : '') . 'value="2">Footer(all pages)</option>

		</select>
		<br />	<br />
		Number of ad per line <select name="number_per_line">
		<option ' . ((Configuration::get($this->name.'_number') == 1) ? 'selected="selected" ' : '') . 'value="1">1</option>
		<option ' . ((Configuration::get($this->name.'_number') == 2) ? 'selected="selected" ' : '') . 'value="2">2</option>
		<option ' . ((Configuration::get($this->name.'_number') == 3) ? 'selected="selected" ' : '') . 'value="3">3</option>
		<option ' . ((Configuration::get($this->name.'_number') == 4) ? 'selected="selected" ' : '') . 'value="4">4</option>
		<option ' . ((Configuration::get($this->name.'_number') == 5) ? 'selected="selected" ' : '') . 'value="5">5</option>
		<option ' . ((Configuration::get($this->name.'_number') == 6) ? 'selected="selected" ' : '') . 'value="6">6</option>
		<option ' . ((Configuration::get($this->name.'_number') == 7) ? 'selected="selected" ' : '') . 'value="7">7</option>
		<option ' . ((Configuration::get($this->name.'_number') == 8) ? 'selected="selected" ' : '') . 'value="8">8</option>

		</select>
		<br />	<br />
		<table cellpadding="0" cellspacing="0" class="table space'.($nbDirImages >= 2 ? ' tableDnD' : '' ).'" id="table_images" style="margin-left: 30px; width: 825px;">
		<caption style="font-weight: bold; margin-bottom: 1em;">' . $this->l('Images') . '</caption>
		<tr class="nodrag nodrop">
		<th width="60" colspan="2">' . $this->l('Position') . '</th>

		<th style="padding-left: 10px; display: none">'. $this->l('Image') .' </th>
		<th width="270">'. $this->l('Link') .' </th>
		<th width="80">'. $this->l('Language') .' </th>
		<th width="80">'. $this->l('Currency') .' </th>
		<th width="40">'. $this->l('Enabled') .' </th>
		<th width="40">'. $this->l('Delete') .' </th>
		<th>'. $this->l('Image') .' </th>
		</tr>';

		if ($nbDirImages) {
			$i = 1;

			foreach ($confImages AS $confImage) {
				if (in_array($confImage['name'], $dirImages)) {
					$html .= $this->_getTableRowHTML($i, $nbDirImages, $confImage['name'], $confImage['link'], true);
					$i++;
				}
			}

			if ($nbDirImages > $nbConfImages) {
				foreach ($dirImages AS $dirImage) {
					if (!$this->_isImageInArray($dirImage, $confImages)) {
						$html .= $this->_getTableRowHTML($i, $nbDirImages, $dirImage);
						$i++;
					}
				}
			}
		}
		else {
			$html .= '<tr><td colspan="4">'.$this->l('No image in module directory').'</td></tr>';
		}

		$html .= '	</table>

		<br />

		<a href="#" id="uploadImage" style="margin-left:30px">
		<img src="../img/admin/add.gif" alt="upload image" />' . $this->l('Add an image') . '
		</a>
		<img id="loading_gif" src="'. _MODULE_DIR_ . $this->name . '/ajax-loader.gif" alt="uploading..." style="position:relative; top:2px; display:none;"/>

		<br /><br />
		<input type="submit" name="submit" value="'.$this->l('Update').'" class="btn btn-default pull-right" >
		<br /><br />

		</div>
		</form>';
		return $html;
	}


	
	public function hookHome($params) {
		if((Configuration::get($this->name.'_hook') == 1)){
		if (!$this->isCached('homepageadvertise2.tpl', $this->getCacheId()))
		{
			global $smarty;

			$smarty->assign(array('images'  => $this->_getImageArray(true, true),
				'number_per_line' 	=> Configuration::get($this->name.'_number')));

		}
		return $this->display(__FILE__, 'homepageadvertise2.tpl', $this->getCacheId());
		}

	}
	public function hookdisplayAdditionalFooter($params) {
		if((Configuration::get($this->name.'_hook') == 2)){
		if (!$this->isCached('homepageadvertise2_footer.tpl', $this->getCacheId()))
		{
			global $smarty;


			$smarty->assign(array('images'  => $this->_getImageArray(true, true),
				'number_per_line' 	=> Configuration::get($this->name.'_number')));

		}
		return $this->display(__FILE__, 'homepageadvertise2_footer.tpl', $this->getCacheId());
		}

	}
	
	private function _getImageArray($lang_filter = false, $curr_filter = false) {
		global $cookie;

		$images = explode(";", Configuration::get($this->name.'_images'));
		$links 	= explode(";", Configuration::get($this->name.'_links'));
		$langs 	= $lang_filter ? explode(";", Configuration::get($this->name.'_langs')) : false;
		$curr 	= $curr_filter ? explode(";", Configuration::get($this->name.'_curr')) : false;
		
		$tab_images = array();
		
		for($i = 0, $length = sizeof($images); $i < $length; $i++) {
			if ($images[$i] != "") {
				if ($lang_filter && isset($langs[$i]) && $langs[$i] != 'all' && $langs[$i] != $cookie->id_lang)
					continue;
				if (($curr_filter && isset($curr[$i]) && $curr[$i] != 'all' && $curr[$i] != $cookie->id_currency))	
					continue;
				
				$tab_images[] = array('name' 	=> $images[$i], 
					'link' 	=> isset($links[$i]) ? $links[$i] : '');
			}
		}
		
		return $tab_images;
	}
	
	private function _isImageInArray($name, $array) {
		if (!is_array($array))
			return false;
		
		foreach ($array as $image) {
			if (isset($image['name'])) {
				if ($image['name'] == $name)
					return true;
			}
		}
		
		return false;
	}
	
	private function _parseImageDir() {
		$dir = _PS_MODULE_DIR_ . $this->name . '/slides/';
		$imgs = array();
		$imgmarkup = '';
		
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (!is_dir($file) && preg_match("/^[^.].*?\.(jpe?g|gif|png)$/i", $file)) {
					array_push($imgs, $file);
				}
			}
			closedir($dh);
		} else {
			echo 'can\'t open slide directory';
			return false;
		}
		
		return $imgs;
	}
	
	private function _getTableRowHTML($i, $nbImages, $imagename, $imagelink = '', $checked = false) {
		return '<tr id="tr_image_'. $i . '"' . ($i % 2 ? ' class="alt_row"' : '').' style="height: 142px;">
		<td class="positions" width="30" style="padding-left: 20px;">' . $i . '</td>
		<td'.($nbImages >= 2 ? ' class="dragHandle"' : '') . ' id="td_image_'. $i . '" width="30">
		<a' .($i == 1 ? ' style="display: none;"' : '' ).' href="#" class="move-up"><img src="../img/admin/up.gif" alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a><br />
		<a '.($i == $nbImages ? ' style="display: none;"' : '' ).'href="#" class="move-down"><img src="../img/admin/down.gif" alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>
		</td>

		<td class="imagename" style="padding-left: 10px; display: none;">'. $imagename .'</td>
		<td class="imagelink">
		<input type="text" style="width: 250px" name="image_link_' . $i . '" value="' . $imagelink .'" />
		</td>
		<td class="imagelang">' . $this->_getLanguageSelectHTML($i) . '</td>
		<td class="imagecurr">' . $this->_getCurrencySelectHTML($i) . '</td>
		<td class="checkbox_image_enabled" style="padding-left: 25px;" width="40">
		<input type="checkbox" name="image_enable_' . $i . '"' . ($checked ? ' checked="checked"' : '') . ' /> 
		</td>
		<td class="delete_image" style="padding-left: 25px;" width="40">
		<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" style="cursor:pointer;" /> 
		</td>
		<td class="image_prev" style="padding-left: 10px;"><img src="'._MODULE_DIR_.$this->name.'/slides/'. $imagename .'" alt="'.$imagename.'"></td>
		</tr>';
	}
	
	private function _getLanguageSelectHTML($i) {
		$languages = Language::getLanguages();
		$langs 	   = explode(";", Configuration::get($this->name.'_langs'));

		$html =  '<select name="language_' . $i . '" style="width:55px">';
		$html .= '<option value="all">ALL</option>';

		foreach ($languages as $language)
		{		
			$html .= '<option value="' . $language['id_lang'] . '"' . (isset($langs[$i-1]) && $langs[$i-1] == $language['id_lang'] ? 'selected="selected"' : '') . '>' . strtoupper($language['iso_code']) . '</option>';
		}

		return $html .= '</select>';
	}
	
	private function _getCurrencySelectHTML($i) {
		$currencies = Currency::getCurrencies();
		$curr 	   = explode(";", Configuration::get($this->name.'_curr'));

		$html =  '<select name="currency_' . $i . '" style="width:55px">';
		$html .= '<option value="all">ALL</option>';

		foreach ($currencies as $currency)
		{		
			$html .= '<option value="' . $currency['id_currency'] . '"' . (isset($curr[$i-1]) && $curr[$i-1] == $currency['id_currency'] ? 'selected="selected"' : '') . '>' . strtoupper($currency['iso_code']) . '</option>';
		}

		return $html .= '</select>';
	}
	
	
	public function hookDisplayHeader()
	{	
		$this->context->controller->addCSS(($this->_path).'homepageadvertise2.css', 'all');
	}
	
}

?>