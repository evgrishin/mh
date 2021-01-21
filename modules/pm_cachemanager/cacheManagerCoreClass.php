<?php
/**
 * @author    Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module 2014
 *
 *************************************
 **   Core Functions For PM Addons   *
 **   http://www.presta-module.com   *
 *************************************
 **/
class cacheManagerCoreClass extends Module {
	// Begin AttributesDeclaration
	protected $_html;
	protected $_html_at_end;
	protected $_base_config_url;
	protected $_default_language;
	protected $_fields_options;
	protected $_iso_lang;
	protected $_languages;
	protected $_context;
	protected $_css_files;
	protected $_js_files;
	protected $_smarty;
	protected $_cookie;
	protected $_employee;
	protected $_coreClassName;
	protected $_registerOnHooks;
	public static $_module_prefix = 'CM';
	protected $_debug_mode = true;
	protected $_copyright_link = false;
	protected $_support_link = false;
	protected $_getting_started = false;
	protected $_css_js_lib_loaded = array ();
	function __construct() {
		$this->_coreClassName = strtolower(get_class());
		parent::__construct();
		if($this->_debug_mode) {
			if(file_exists(_PS_ROOT_DIR_ . '/override/classes/fb.php'))
				include_once (_PS_ROOT_DIR_ . '/override/classes/fb.php');
		}
		$this->_initClassVar();
	}
	public function install(){
		if (parent::install() == false OR $this->_registerHooks() == false OR (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !$this->registerHook('displayBackOfficeHeader')))
		  return false;
		return true;
    }
	public function checkIfModuleIsUpdate($updateDb = false, $displayConfirm = true) {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->registerHook('displayBackOfficeHeader');
		return true;
	}
	protected function _registerHooks() {
		if(!isset($this->_registerOnHooks) || !self::_isFilledArray($this->_registerOnHooks)) return true;
		foreach($this->_registerOnHooks as $hook) {
			$hook_exists = self::getIdHookByName($hook);
			if($hook_exists)
				if(!$this->registerHook($hook)) { return false;}
		}
		return true;
	}
	public static function getIdHookByName($hook_name)
	{
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$retro_hook_name = Hook::getRetroHookName($hook_name);
		$row = Db::getInstance()->getRow('SELECT `id_hook`
			FROM `'._DB_PREFIX_.'hook`
			WHERE `name` = \''.pSQL($hook_name).'\'
			'.(isset($retro_hook_name) ? ' OR `name` = \''.pSQL($retro_hook_name).'\'' :''));
		return ($row && isset($row['id_hook']) && $row['id_hook'] ? $row['id_hook'] : false);
	}
	public static function Db_ExecuteS($q) {
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($q);
		else return Db::getInstance()->ExecuteS($q);
	}
	public static function Db_Execute($q) {
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($q);
		else return Db::getInstance()->Execute($q);
	}
	protected function getContent() {
	}
	protected function _pmClear(){
		$this->_html .= '<div class="clear"></div>';
	}
	protected function _showWarning($text) {
		$this->_html .= '<div class="ui-widget">
        <div style="margin-top: 20px;margin-bottom: 20px;  padding: 0 .7em;" class="ui-state-error ui-corner-all">
          <p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>
          ' . $text . '
        </div>
      </div>';
	}
	protected function _showRating($show = false) {
		$dismiss = (int)(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Configuration::getGlobalValue('PM_'.self::$_module_prefix.'_DISMISS_RATING') : Configuration::get('PM_'.self::$_module_prefix.'_DISMISS_RATING'));
		if ($show && $dismiss != 1 && self::_getNbDaysModuleUsage() >= 3) {
			$this->_html .= '
			<div id="addons-rating-container" class="ui-widget note">
				<div style="margin-top: 20px; margin-bottom: 20px; padding: 0 .7em; text-align: center;" class="ui-state-highlight ui-corner-all">
					<p class="invite">'
						. $this->l('You are satisfied with our module and want to encourage us to add new features ?', $this->_coreClassName)
						. '<br/>'
						. '<a href="http://addons.prestashop.com/ratings.php" target="_blank"><strong>'
						. $this->l('Please rate it on Prestashop Addons, and give us 5 stars !', $this->_coreClassName)
						. '</strong></a>
					</p>
					<p class="dismiss">'
						. '[<a href="javascript:void(0);">'
						. $this->l('No thanks, I don\'t want to help you. Close this dialog.', $this->_coreClassName)
						. '</a>]
					 </p>
				</div>
			</div>';
		}
	}
	protected function _showInfo($text) {
		$this->_html .= '<div class="ui-widget">
        <div style="margin-top: 20px;margin-bottom: 20px;  padding: 0 .7em;" class="ui-state-highlight ui-corner-all">
          <p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>
          ' . $text . '
        </div>
      </div>';
	}
	protected function _displayTitle($title) {
		$this->_html .= '<h2>' . $title . '</h2>';
	}
	protected function _displaySubTitle($title) {
		$this->_html .= '<h3 class="pmSubTitle">' . $title . '</h3>';
	}
	public function _displayErrorsJs($include_script_tag = false) {
		if($include_script_tag) $this->_html .= '<script type="text/javascript">';
		if (sizeof($this->errors)) {
			foreach ( $this->errors as $key => $error )
				$this->_html .= 'parent.parent.show_error("' . $error . '");';
		}
		if($include_script_tag) $this->_html .= '</script>';
	}
	
	private function _getPMdata() {
		$param = array();
		$param[] = 'ver-'._PS_VERSION_;
		$param[] = 'current-'.$this->name;
		
		$result = self::Db_ExecuteS('SELECT DISTINCT name FROM '._DB_PREFIX_.'module WHERE name LIKE "pm_%"');
		if ($result && self::_isFilledArray($result)) {
			foreach ($result as $module) {
				$instance = Module::getInstanceByName($module['name']);
				if ($instance && isset($instance->version)) $param[] = $module['name'].'-'.$instance->version;
			}
		}
		return urlencode(base64_encode(implode('|', $param)));
	}
	protected function _displayCS() {
		$this->_html .= '<div id="pm_panel_cs_modules_bottom" class="pm_panel_cs_modules_bottom"><br />';
		$this->_displayTitle($this->l('Check all our modules', $this->_coreClassName));
		$this->_html .= '<iframe src="//www.presta-module.com/cross-selling-addons-modules-footer?pm='.$this->_getPMdata().'" scrolling="no"></iframe></div>';
	}
	protected function _displaySupport() {
		$this->_html .= '<div id="pm_footer_container" class="ui-corner-all ui-tabs ui-tabs-panel">';
		$this->_displayCS();
		$this->_html .= '<div id="pm_support_informations" class="pm_panel_bottom"><br />';
		if (method_exists($this, '_displayTitle'))
			$this->_displayTitle($this->l('Information & Support', (isset($this->_coreClassName) ? $this->_coreClassName : false)));
		else
			$this->_html .= '<h2>' . $this->l('Information & Support', (isset($this->_coreClassName) ? $this->_coreClassName : false)) . '</h2>';
		$this->_html .= '<ul class="pm_links_block">';
		$this->_html .= '<li class="pm_module_version"><strong>' . $this->l('Module Version: ', (isset($this->_coreClassName) ? $this->_coreClassName : false)) . '</strong> ' . $this->version . '</li>';
		if (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started))
			$this->_html .= '<li class="pm_get_started_link"><a href="javascript:;" class="pm_link">'. $this->l('Getting started', (isset($this->_coreClassName) ? $this->_coreClassName : false)) .'</a></li>';
		if (self::_isFilledArray($this->_support_link))
			foreach($this->_support_link as $infos)
				$this->_html .= '<li class="pm_useful_link"><a href="'.$infos['link'].'" target="_blank" class="pm_link">'.$infos['label'].'</a></li>';
		$this->_html .= '</ul>';
		if (isset($this->_copyright_link) && $this->_copyright_link) {
			$this->_html .= '<div class="pm_copy_block">';
			if (isset($this->_copyright_link['link']) && !empty($this->_copyright_link['link'])) $this->_html .= '<a href="'.$this->_copyright_link['link'].'"'.((isset($this->_copyright_link['target']) AND $this->_copyright_link['target']) ? ' target="'.$this->_copyright_link['target'].'"':'').''.((isset($this->_copyright_link['style']) AND $this->_copyright_link['style']) ? ' style="'.$this->_copyright_link['style'].'"':'').'>';
			$this->_html .= '<img src="'.str_replace('_PATH_',$this->_path,$this->_copyright_link['img']).'" />';
			if (isset($this->_copyright_link['link']) && !empty($this->_copyright_link['link'])) $this->_html .= '</a>';
			$this->_html .= '</div>';
		}
		$this->_html .= '</div>';
		$this->_html .= '</div>';
		if (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started)) {
			$this->_html .= "<script type=\"text/javascript\">
			$('.pm_get_started_link a').click(function() { $.fancybox([";
			$get_started_image_list = array();
			foreach ($this->_getting_started as $get_started_image)
				$get_started_image_list[] = "{ 'href': '".$get_started_image['href']."', 'title': '".htmlentities($get_started_image['title'], ENT_QUOTES, 'UTF-8')."' }";
			$this->_html .= implode(',', $get_started_image_list);
			$this->_html .= "
					], {
					'padding'			: 0,
					'transitionIn'		: 'none',
					'transitionOut'		: 'none',
					'type'				: 'image',
					'changeFade'		: 0
				}); });
			</script>";
		}
		if (method_exists($this, '_includeHTMLAtEnd')) $this->_includeHTMLAtEnd();
	}
	protected function _preProcess() {
		if (isset($_GET['dismissRating'])) {
			$this->_cleanOutput();
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				Configuration::updateGlobalValue('PM_'.self::$_module_prefix.'_DISMISS_RATING', 1);
			else
				Configuration::updateValue('PM_'.self::$_module_prefix.'_DISMISS_RATING', 1);
			die;
		}
		else if(isset($_GET ['pm_load_function'])) {
			if(method_exists($this, $_GET ['pm_load_function'])) {
				$this->_cleanOutput();
				if(Tools::getValue('class')) {
					if(class_exists ( Tools::getValue('class') )) {
						$class = Tools::getValue('class');
						$obj = new $class();
						if(Tools::getValue($obj->identifier)) {
							$obj = new $class(Tools::getValue($obj->identifier));
						}
						$params = array('obj'=>$obj,'class'=>$class, 'method'=>$_GET ['pm_load_function'],'reload_after'=>Tools::getValue('pm_reload_after'),'js_callback'=>Tools::getValue('pm_js_callback'));
						$this->_preLoadFunctionProcess($params);
						$this->$_GET ['pm_load_function']($params);
					}else {
						$this->_cleanOutput();
						$this->_showWarning($this->l('Class', $this->_coreClassName).' '.Tools::getValue('class').' '.$this->l('does not exists', $this->_coreClassName));
						$this->_echoOutput(true);
					}
				}
				else {
					$params = array('method' => $_GET ['pm_load_function'],'reload_after'=>Tools::getValue('pm_reload_after'),'js_callback'=>Tools::getValue('pm_js_callback'));
					$this->_preLoadFunctionProcess($params);
					$this->$_GET ['pm_load_function']($params);
				}
				$this->_echoOutput(true);
			}else {
				$this->_cleanOutput();
				$this->_showWarning($this->l('Please send class name into "class" var', $this->_coreClassName));
				$this->_echoOutput(true);
			}
		}
		elseif(isset($_GET ['pm_delete_obj'])) {
			if(Tools::getValue('class')) {
				if(class_exists ( Tools::getValue('class') )) {
					$class = Tools::getValue('class');
					$obj = new $class();
					$obj = new $class(Tools::getValue($obj->identifier));
					$this->_preDeleteProcess(array('obj'=>$obj,'class'=>$class));
					if($obj->delete()) {
						$this->_cleanOutput();
						$this->_postDeleteProcess(array('class'=>$class));
						$this->_echoOutput(true);
					}
					else {
						$this->_cleanOutput();
						$this->_showWarning($this->l('Error while deleting object', $this->_coreClassName));
						$this->_echoOutput(true);
					}
				}else {
					$this->_cleanOutput();
					$this->_showWarning($this->l('Class', $this->_coreClassName).' '.Tools::getValue('class').' '.$this->l('does not exists', $this->_coreClassName));
					$this->_echoOutput(true);
				}
			}
			else {
				$this->_cleanOutput();
				$this->_showWarning($this->l('Please send class name into "class" var', $this->_coreClassName));
				$this->_echoOutput(true);
			}
		}
		elseif(isset($_POST ['pm_save_order'])) {
			if(!Tools::getValue('order')) {
				$this->_cleanOutput();
				$this->_showWarning($this->l('Not receive IDS', $this->_coreClassName));
				$this->_echoOutput(true);
			}
			elseif(!Tools::getValue('destination_table')) {
				$this->_cleanOutput();
				$this->_showWarning($this->l('Please send destination table', $this->_coreClassName));
				$this->_echoOutput(true);
			}
			elseif(!Tools::getValue('field_to_update')) {
				$this->_cleanOutput();
				$this->_showWarning($this->l('Please send name of position field', $this->_coreClassName));
				$this->_echoOutput(true);
			}
			elseif(!Tools::getValue('identifier')) {
				$this->_cleanOutput();
				$this->_showWarning($this->l('Please send identifier', $this->_coreClassName));
				$this->_echoOutput(true);
			}
			else {
				$order = Tools::getValue('order');
				$identifier = Tools::getValue('identifier');
				$field_to_update = Tools::getValue('field_to_update');
				$destination_table = Tools::getValue('destination_table');
				foreach ($order as $position => $id) {
					$id = preg_replace("/^\w+_/", "", $id);
					$data = array($field_to_update=>$position);
					Db::getInstance()->AutoExecute(_DB_PREFIX_ . $destination_table, $data, 'UPDATE', $identifier.' = ' . (int) $id);
			}
				$this->_cleanOutput();
				$this->_echoOutput(true);
			}
		}
		elseif (isset($_GET ['getPanel']) && $_GET ['getPanel']) {
			self::_cleanBuffer();
			switch ($_GET ['getPanel']) {
				case 'displayFormFontManager' :
					$this->_cleanOutput();
					$this->_displayFormFontManager();
					$this->_echoOutput(true);
					break;
				case 'getChildrenCategories':
					if (Tools::getValue('id_category_parent')){
						$children_categories = self::getChildrenWithNbSelectedSubCat(Tools::getValue('id_category_parent'), Tools::getValue('selectedCat'), $this->_default_language);
						die(self::jsonEncode($children_categories));
					}
					break;
			}
		}
	}
	protected function _preCopyFromPost() {
	}
	protected function _postCopyFromPost($params) {
	}
	protected function _preDeleteProcess($params) {
	}
	protected function _preLoadFunctionProcess(&$params) {
	}
	protected function _postDeleteProcess($params) {
		if(isset($params['include_script_tag']) && $params['include_script_tag']) $this->_html .= '<script type="text/javascript">';
		if(isset($_GET['pm_reload_after']) && $_GET['pm_reload_after'])
			$this->_reloadPanels($_GET['pm_reload_after']);
		if(isset($_GET['pm_js_callback']) && $_GET['pm_js_callback'])
			$this->_getJsCallback($_GET['pm_js_callback']);
		$this->_html .= 'parent.parent.show_info("'.$this->l('Successfully deleted', $this->_coreClassName).'");';
		if(isset($params['include_script_tag']) && $params['include_script_tag']) $this->_html .= '</script>';
	}
	protected function _getJsCallback($js_callback) {
		$js_callbacks = explode('|',$js_callback);
		foreach($js_callbacks as $js_callback) {
			$this->_html .= 'parent.parent.'.$js_callback.'();';
		}
	}
	protected function _reloadPanels($reload_after) {
		$reload_after = explode('|',$reload_after);
		foreach($reload_after as $panel) {
			$this->_html .= 'parent.parent.reloadPanel("'.$panel.'");';
		}
	}
	protected function _postSaveProcess($params) {
		if(isset($params['include_script_tag']) && $params['include_script_tag']) $this->_html .= '<script type="text/javascript">';
		if(isset($params['reload_after']) && $params['reload_after'])
			$this->_reloadPanels($params['reload_after']);
		if(isset($params['js_callback']) && $params['js_callback'])
			$this->_getJsCallback($params['js_callback']);
		$this->_html .= 'parent.parent.show_info("'.$this->l('Successfully saved', $this->_coreClassName).'");</script>';
		if(isset($params['include_script_tag']) && $params['include_script_tag']) $this->_html .= '</script>';
	}
	protected function _postProcess() {
		if(Tools::getValue('pm_save_obj')) {
			if(class_exists ( Tools::getValue('pm_save_obj') )) {
				$class = Tools::getValue('pm_save_obj');
				$obj = new $class();
				if(Tools::getValue($obj->identifier)) {
					$obj = new $class(Tools::getValue($obj->identifier));
				}
				$this->errors = $obj->validateControler();
				if(!self::_isFilledArray($this->errors)) {
					$this->copyFromPost($obj);
					if($obj->save()) {
						$this->_cleanOutput();
						$this->_postSaveProcess(array('class'=>$class,'obj'=>$obj,'include_script_tag'=>true,'reload_after'=>Tools::getValue('pm_reload_after'),'js_callback'=>Tools::getValue('pm_js_callback')));
						$this->_echoOutput(true);
					}else {
						$this->_cleanOutput();
						$this->_showWarning($this->l('Error while saving object', $this->_coreClassName));
						$this->_echoOutput(true);
					}
				}else { $this->_cleanOutput();$this->_displayErrorsJs(true); $this->_echoOutput(true);}
			}else {
				$this->_cleanOutput();
				$this->_showWarning($this->l('Class', $this->_coreClassName).' '.Tools::getValue('class').' '.$this->l('does not exists', $this->_coreClassName));
				$this->_echoOutput(true);
			}
		} elseif (Tools::getValue('uploadTempFile')) {
			$this->_postProcessUploadTempFile();
		} elseif (isset($_POST ['submit_font'])){
			$this->_cleanOutput();
			$font_name = '';
			if(isset($_POST ['font_family_name']) && $_POST['font_family_name']){
				$font_name = $_POST ['font_family_name'];
				if(isset($_POST ['font_type']) && $_POST ['font_type'])
				$is_google_font = ($_POST['font_type'] == 1 ? true : false);
				$addfont = $this->_postProcessAddFont($font_name,($_POST['font_type'] == 1 ? true : false));
				if($addfont)
					$this->_html .= '<script type="text/javascript">
										parent.parent.show_info("' . $this->l('Font Added.', $this->_coreClassName) . '");
										parent.parent.closeDialogIframe();
									 </script>';
				$this->_echoOutput(true);
			}
			$this->_html .= '<script type="text/javascript">
										parent.parent.show_error("' . $this->l('An  error occured.', $this->_coreClassName) . '");
										parent.parent.closeDialogIframe();
									 </script>';
		} else if (Tools::getValue('getItem')) {
			$this->_cleanOutput();
			$item = Tools::getValue('itemType');
			$query = Tools::getValue('q', false);
			if (! $query || strlen($query) < 1) {
				self::_cleanBuffer();
				die();
			}
			$limit = Tools::getValue('limit', 100);
			$start = Tools::getValue('start', 0);
			switch ($item) {
				case 'product' :
					$items = $this->getProductsOnLive($query, $limit, $start);
					$item_id_column = 'id_product';
					$item_name_column = 'name';
					break;
				case 'supplier' :
					$items = $this->getSuppliersOnLive($query, $limit, $start);
					$item_id_column = 'id_supplier';
					$item_name_column = 'name';
					break;
				case 'manufacturer' :
					$items = $this->getManufacturersOnLive($query, $limit, $start);
					$item_id_column = 'id_manufacturer';
					$item_name_column = 'name';
					break;
				case 'cms' :
					$items = $this->getCMSPagesOnLive($query, $limit, $start);
					$item_id_column = 'id_cms';
					$item_name_column = 'meta_title';
					break;
			}
			if ($items)
				foreach ( $items as $row )
					$this->_html .= $row [$item_id_column] . '=' . $row [$item_name_column] . "\n";
			$this->_echoOutput(true);
			die();
		}
	}
	protected function _initClassVar() {
		global $cookie, $smarty, $currentIndex, $employee;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$this->_context = Context::getContext();
			$this->_cookie = $this->_context->cookie;
			$this->_smarty = $this->_context->smarty;
		} else {
			$this->_cookie = $cookie;
			$this->_smarty = $smarty;
		}
		$this->_employee = $employee;
		$this->_base_config_url = ((version_compare(_PS_VERSION_, '1.5.0.0', '<')) ? $currentIndex : $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller: '')) . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
		$this->_default_language = (int) Configuration::get('PS_LANG_DEFAULT');
		$this->_iso_lang = Language::getIsoById($this->_cookie->id_lang);
		$this->_languages = Language::getLanguages(false);
	}
	protected function _startForm($configOptions) {
		$defaultOptions = array(
			'action' => false,
			'target' => 'dialogIframePostForm',
			'iframetarget' => true
		);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		if ($configOptions['iframetarget']) $this->_headerIframe();
		$this->_html .= '<form action="' . ($configOptions['action'] ? $configOptions['action'] : $this->_base_config_url) . '" method="post" class="width3" id="' . $configOptions['id'] . '" target="' . $configOptions['target'] . '">';
		if(isset($configOptions['obj']) && $configOptions['obj'] && isset($configOptions['obj']->id) && $configOptions['obj']->id) {
			$this->_html .= '<input type="hidden" name="'.$configOptions['obj']->identifier.'" value="'.$configOptions['obj']->id.'" />';
		}
		if(isset($configOptions['obj']) && $configOptions['obj'])
			$this->_html .= '<input type="hidden" name="pm_save_obj" value="'.get_class($configOptions['obj']).'" />';
		if(isset($configOptions['params']['reload_after']) && $configOptions['params']['reload_after'])
			$this->_html .= '<input type="hidden" name="pm_reload_after" value="'.$configOptions['params']['reload_after'].'" />';
		if(isset($configOptions['params']['js_callback']) && $configOptions['params']['js_callback'])
			$this->_html .= '<input type="hidden" name="pm_js_callback" value="'.$configOptions['params']['js_callback'].'" />';
	}
	protected function _endForm($configOptions) {
		$defaultOptions = array(
			'id' => NULL,
			'iframetarget' => true,
			'jquerytoolsvalidatorfunction' => false
		);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		$this->_html .= '</form>';
		if ($configOptions['id'] != NULL && in_array('jquerytools', $this->_css_js_to_load)) {
			$this->_html .= '
			<script type="text/javascript">
				$jqPm("#'.$configOptions['id'].'").validator({
					lang: "'.$this->_iso_lang.'",
					messageClass: "formValidationError",
					errorClass: "elementErrorAssignedClass",
					position: "center bottom"
				})'.(($configOptions['jquerytoolsvalidatorfunction'] != false) ? '.submit('.$configOptions['jquerytoolsvalidatorfunction'].')' : '').';
			</script>';
		}
		if ($configOptions['iframetarget']) $this->_footerIframe();
	}
	public function _retrieveFormValue($type, $fieldName, $fieldDbName = false, $obj, $defaultValue = '', $compareValue = false, $key = false) {
		if (! $fieldDbName) $fieldDbName = $fieldName;
		switch ($type) {
			case 'text' :
				if ($key)
					return htmlentities(stripslashes(Tools::getValue($fieldName, ($obj && isset($obj->{$fieldDbName} [$key]) ? $obj->{$fieldDbName} [$key] : $defaultValue))), ENT_COMPAT, 'UTF-8');
				else
					return htmlentities(stripslashes(Tools::getValue($fieldName, ( $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue))), ENT_COMPAT, 'UTF-8');
				break;
			case 'textpx' :
				if ($key)
					return intval(preg_replace('#px#', '', Tools::getValue($fieldName, ( $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} [$key] : $defaultValue))));
				else
					return intval(preg_replace('#px#', '', Tools::getValue($fieldName, ( $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue))));
				break;
			case 'select' :
				return ((Tools::getValue($fieldName, ( $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue)) == $compareValue) ? ' selected="selected"' : '');
				break;
			case 'radio' :
			case 'checkbox' :
				if($fieldDbName=='groupslive' && isset($obj->groups) && sizeof($obj->groups) )
					return ( ( in_array($compareValue,pm_advancedbackgroundchanger::OneKeyArray($obj->groups,'id_group')) ) ? ' checked="checked"' : '');
				if($fieldDbName=='usergrouplive' && isset($obj->usergroup) && sizeof($obj->usergroup) )
					return ( ( in_array($compareValue,pm_advancedbackgroundchanger::OneKeyArray($obj->usergroup,'id_group')) ) ? ' checked="checked"' : '');
				if( isset($obj->$fieldName) && is_array($obj->$fieldName) && sizeof($obj->$fieldName) && isset($obj->{$fieldDbName})  )
					return ( ( in_array($compareValue,$obj->$fieldName) ) ? ' checked="checked"' : '');
				return ((Tools::getValue($fieldName, ($obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue)) == $compareValue) ? ' checked="checked"' : '');
				break;
		}
	}
	protected function _startFieldset($title, $icone = false, $hide = true, $onclick = false) {
		$this->_html .= '<fieldset>';
		if ($title || $hide) $this->_html .= '<legend class="ui-state-default" style="cursor:pointer;" onclick="$jqPm(this).next(\'div\').slideToggle(\'fast\'); '.
		($onclick?$onclick:'').'">' . ($icone ? '<img src="' . $icone . '" alt="' . $title . '" title="' . $title . '" /> ' : '') . '' . $title . ' <small ' . (! $hide ? 'style="display:none;"' : '') . '>' . $this->l('Click here to edit', $this->_coreClassName) . '</small></legend>';
		$this->_html .= '<div' . ($hide ? ' class="hideAfterLoad"' : '') . '>';
	}
	protected function _endFieldset() {
		$this->_html .= '</div>';
		$this->_html .= '</fieldset>';
	}
	protected function _displayInputActive($configOptions) {
		$defaultOptions = array(
			'defaultvalue' => false,
			'tips' => false,
			'onclick' => false
		);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		$this->_html .= '<label>' . $configOptions['label'] . '</label>
	    <div class="margin-form"><label class="t" for="' . $configOptions['key_active'] . '_on" style="float:left;"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes', $this->_coreClassName) . '" title="' . $this->l('Yes', $this->_coreClassName) . '" /></label>
	      <input type="radio" name="' . $configOptions['key_active'] . '" id="' . $configOptions['key_active'] . '_on" ' . ($configOptions['onclick'] ? 'onclick="' . $configOptions['onclick'] . '"' : '') . ' value="1" ' . $this->_retrieveFormValue('radio', $configOptions['key_active'], $configOptions['key_db'], $configOptions['obj'], $configOptions['defaultvalue'], 1) . '  style="float:left;" />
	      <label class="t" for="' . $configOptions['key_active'] . '_on" style="float:left;"> ' . $this->l('Yes', $this->_coreClassName) . '</label>
	      <label class="t" for="' . $configOptions['key_active'] . '_off" style="float:left;"><img src="../img/admin/disabled.gif" alt="' . $this->l('No', $this->_coreClassName) . '" title="' . $this->l('No', $this->_coreClassName) . '" style="margin-left: 10px;" /></label>
	      <input type="radio" name="' . $configOptions['key_active'] . '" id="' . $configOptions['key_active'] . '_off" ' . ($configOptions['onclick'] ? 'onclick="' . $configOptions['onclick'] . '"' : '') . ' value="0" ' . $this->_retrieveFormValue('radio', $configOptions['key_active'], $configOptions['key_db'], $configOptions['obj'], $configOptions['defaultvalue'], 0) . '  style="float:left;"/>
	      <label class="t" for="' . $configOptions['key_active'] . '_off" style="float:left;"> ' . $this->l('No', $this->_coreClassName) . '</label>';
		if (isset($configOptions['tips']) && $configOptions['tips']) {
			$this->_html .= '<img title="' . $configOptions['tips'] . '" id="' . $configOptions['key_active'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
			$this->_html .= '<script type="text/javascript">initTips("#' . $configOptions['key'] . '")</script>';
		}
		$this->_pmClear();
		$this->_html .= '</div>';
	}
	private function _parseOptions($defaultOptions = array(), $options = array()) {
		if (self::_isFilledArray($options)) $options = array_change_key_case($options, CASE_LOWER);
		if (isset($options['tips']) && !empty($options['tips'])) $options['tips'] = htmlentities($options['tips'], ENT_QUOTES, 'UTF-8');
		if (self::_isFilledArray($defaultOptions)) {
			$defaultOptions = array_change_key_case($defaultOptions, CASE_LOWER);
			foreach ($defaultOptions as $option_name=>$option_value)
				if (!isset($options[$option_name])) $options[$option_name] = $defaultOptions[$option_name];
		}
		return $options;
	}
	protected function _displayInputText($configOptions) {
		$defaultOptions = array(
			'type' => 'text',
			'size' => '150px',
			'defaultvalue' => false,
			'min' => false,
			'max' => false,
			'maxlength' => false,
			'onkeyup' => false,
			'onchange' => false,
			'required' => false,
			'tips' => false
		);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		$this->_html .= '<label>' . $configOptions['label'] . '</label>
		    <div class="margin-form">
		      <input style="width:' . $configOptions['size'] . '" type="'. $configOptions['type'] .'" name="' . $configOptions['key'] . '" id="' . $configOptions['key'] . '" value="' . $this->_retrieveFormValue('text', $configOptions['key'], false, $configOptions['obj'], $configOptions['defaultvalue']) . '" class="ui-corner-all ui-input-pm" '.(($configOptions['required'] == true) ? 'required="required" ' : '') . ($configOptions['onkeyup'] ? ' onkeyup="' . $configOptions['onkeyup'] . '"' : '') . ($configOptions['onchange'] ? ' onchange="' . $configOptions['onchange'] . '"' : '') . (($configOptions['min'] != false) ? 'min="'.(int)$configOptions['min'].'" ' : '').(($configOptions['max'] != false) ? 'max="'.(int)$configOptions['max'].'" ' : '').(($configOptions['maxlength'] != false) ? 'maxlength="'.(int)$configOptions['maxlength'].'" ' : '').'/>';
		if (isset($configOptions['tips']) && $configOptions['tips']) {
			$this->_html .= '<img title="' . $configOptions['tips'] . '" id="' . $configOptions['key'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
			$this->_html .= '<script type="text/javascript">initTips("#' . $configOptions['key'] . '")</script>';
		}
		$this->_pmClear();
		$this->_html .= '</div>';
	}
	protected function _loadCssJsLibrary($library, $rememberLoadedLibrary = true) {
		if (in_array($library, $this->_css_js_lib_loaded))
			return;
		switch ($library) {
			case 'admincore' :
				$this->_html .= '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'css/adminCore.css" />
	        					 <script type="text/javascript" src="' . $this->_path . 'js/adminCore.js"></script>';
				$this->_html .= '<script type="text/javascript">
					var _modulePath = "' . $this->_path . '";
					var _base_config_url = "' . $this->_base_config_url . '";
					var id_language = Number(' . $this->_default_language . ');
				</script>';
				break;
			case 'adminmodule' :
				if (file_exists(dirname(__FILE__) . '/css/admin.css'))
					$this->_html .= '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'css/admin.css" />';
				if (file_exists(dirname(__FILE__) . '/js/admin.js'))
					$this->_html .= '<script type="text/javascript" src="' . $this->_path . 'js/admin.js"></script>';
				break;
			case 'jquery' :
					if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
						$this->context->controller->addJqueryUI(array('ui.draggable', 'ui.droppable', 'ui.sortable', 'ui.widget', 'ui.dialog', 'ui.tabs'), 'base');
					} else {
						if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
							$this->_html .= '<script type="text/javascript" src="'.$this->_path . 'js/jquery.min.js"></script>';
						$this->_html .= ' <link type="text/css" rel="stylesheet" href="' . $this->_path . 'js/jqueryui/1.8.9/themes/custom-theme/jquery-ui-1.8.16.custom.css" />
							<script type="text/javascript" src="'.$this->_path . 'js/jquery-ui-1.8.11.min.js"></script>';
					}
					$this->_html .= '<script type="text/javascript">';
					if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
						$this->_html .= 'var $jqPm = jQuery.noConflict(true);';
					else
						$this->_html .= 'var $jqPm = jQuery;';
					$this->_html .= '</script>';
				break;
			case 'jquerytiptip':
				$this->_html .= '<script type="text/javascript" src="'.$this->_path . 'js/jquery.tipTip.js"></script>';
				break;
			case 'jgrowl' :
				$this->_html .= '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'js/jGrowl/jquery.jgrowl.css" />
		    					 <script type="text/javascript" src="' . $this->_path . 'js/jGrowl/jquery.jgrowl_minimized.js"></script>';
				break;
			case 'datatables' :
				$this->_html .= '<script type="text/javascript" src="' . $this->_path . 'js/datatables/jquery.dataTables.min.js"></script>
		  						<link rel="stylesheet" href="' . $this->_path . 'js/datatables/demo_table_jui.css" type="text/css" />';
				break;
		}
		if ($rememberLoadedLibrary)
			$this->_css_js_lib_loaded [] = $library;
	}
	protected function _loadCssJsLibraries($rememberLoadedLibrary = true) {
		if (self::_isFilledArray($this->_css_js_to_load)) {
			foreach ($this->_css_js_to_load as $library) {
				$this->_loadCssJsLibrary($library, $rememberLoadedLibrary);
			}
		}
	}
	private function _includeHTMLAtEnd() {
		$this->_html .= '<script type="text/javascript">$jqPm(\'.hideAfterLoad\').hide();</script>';
		$this->_html .= $this->_html_at_end;
	}
	protected function _addButton($configOptions) {
		$defaultOptions = array(
			'text' => '',
			'href' => '',
			'title' => '',
			'onclick' => false,
			'icon_class' => false,
			'class' => false,
			'rel' => false
		);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		$curId = 'button_' . uniqid();
		$this->_html .= '<a href="' . htmlentities($configOptions['href'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($configOptions['title'], ENT_COMPAT, 'UTF-8') . '" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' . ($configOptions['class'] ? ' ' . htmlentities($configOptions['class'], ENT_COMPAT, 'UTF-8') . '' : '') . '" id="' . $curId . '" ' . ($configOptions['text'] ? 'style="padding-right:5px;"' : '') . ' ' . ($configOptions['rel'] ? 'rel="' . $configOptions['rel'] . '"' : '') . '>
		' . ($configOptions['icon_class'] ? '<span class="' . htmlentities($configOptions['icon_class'], ENT_COMPAT, 'UTF-8') . '" style="float: left; margin-right: .3em;"></span>' : '') . '
		' . $configOptions['text'] . '
		</a>';
		if ($configOptions['onclick']) $this->_html .= '<script type="text/javascript">$jqPm("#' . $curId . '").unbind("click").bind("click", function() { ' . $configOptions['onclick'] . ' });</script>';
	}
	protected function _displaySubmit($value, $name) {
		$this->_pmClear();
		$this->_html .= '<center><input type="submit" value="' . $value . '" name="' . $name . '" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" /></center><br />';
	}
	protected function _headerIframe() {
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
			$assets = array();
			$backupHtml = $this->_html;
			$this->_loadCssJsLibraries(false);
			foreach ($this->context->controller->css_files as $cssUri=>$media)
				if (!preg_match('/gamification/i', $cssUri))
					$assets[] = '<link href="'.$cssUri.'" rel="stylesheet" type="text/css" media="'.$media.'" />';
			foreach ($this->context->controller->js_files as $jsUri)
				if (!preg_match('#gamification|notifications\.js|help\.js#i', $jsUri))
					$assets[] = '<script type="text/javascript" src="'.$jsUri.'"></script>';
			$assets[] = '<script type="text/javascript">$jqPm = jQuery;</script>';
			$this->_html = $backupHtml;
		}
		$this->_html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->_iso_lang . '" lang="' . $this->_iso_lang . '">
	      <head>
	        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	        <title>PrestaShop</title>
	        '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && version_compare(_PS_VERSION_, '1.6', '<') ? '<script type="text/javascript" src="'.$this->_path . 'js/jquery.min.js"></script>' : '').'
	        '.(version_compare(_PS_VERSION_, '1.6.0.0', '>=') ? implode('', $assets) : '').'
	      </head>
	      <body style="background:#fff;" class="pm_bo_ps_'.substr(str_replace('.', '', _PS_VERSION_), 0, 2).'">';
		$this->_loadCssJsLibraries();
	}
	protected function _footerIframe() {
		$this->_html .= '<iframe name="dialogIframePostForm" id="dialogIframePostForm" frameborder="0" marginheight="0" marginwidth="0" width="' . ($this->_debug_mode ? '500' : '0') . '" height="' . ($this->_debug_mode ? '500' : '0') . '"></iframe>';
		$this->_includeHTMLAtEnd();
		$this->_html .= '</body></html>';
	}
	protected function _initDataTable($id_table, $returnHTML = false, $returnAsScript = false) {
		$return = '';
		if (! $returnAsScript)
			$return .= '<script type="text/javascript">
		 		var oTable' . $id_table . ' = undefined;
		 		$jqPm(document).ready(function(){';
		$return .= 'oTable' . $id_table . ' = $jqPm(\'#' . $id_table . '\').dataTable( {
				        "sDom": \'R<"H"lfr>t<"F"ip<\',
				        "bJQueryUI": true,
				        "bStateSave": true,
				       	"bPaginate": false,
				        "bDestory": true,
				        "oLanguage": {
				          "sLengthMenu": "' . $this->l('Display', $this->_coreClassName) . ' _MENU_ ' . $this->l('records per page', $this->_coreClassName) . '",
				          "sZeroRecords": "' . $this->l('Nothing found - sorry', $this->_coreClassName) . '",
				          "sInfo": "' . $this->l('Showing', $this->_coreClassName) . ' _START_ ' . $this->l('to', $this->_coreClassName) . ' _END_ ' . $this->l('of', $this->_coreClassName) . ' _TOTAL_ ' . $this->l('records', $this->_coreClassName) . '",
				          "sInfoEmpty": "' . $this->l('Showing', $this->_coreClassName) . ' 0 ' . $this->l('to', $this->_coreClassName) . ' 0 ' . $this->l('of', $this->_coreClassName) . ' 0 ' . $this->l('records', $this->_coreClassName) . '",
				          "sInfoFiltered": "(' . $this->l('filtered from', $this->_coreClassName) . ' _MAX_ ' . $this->l('total records', $this->_coreClassName) . ')",
				          "sPageNext": "' . $this->l('Next', $this->_coreClassName) . '",
				          "sPagePrevious": "' . $this->l('Previous', $this->_coreClassName) . '",
				          "sPageLast": "' . $this->l('Last', $this->_coreClassName) . '",
				          "sPageFirst": "' . $this->l('First', $this->_coreClassName) . '",
				          "sSearch": "' . $this->l('Search', $this->_coreClassName) . '",
						  oPaginate: {
							  "sFirst":"' . $this->l('First', $this->_coreClassName) . '",
							  "sPrevious":"' . $this->l('Previous', $this->_coreClassName) . '",
							  "sNext":"' . $this->l('Next', $this->_coreClassName) . '",
							  "sLast":"' . $this->l('Last', $this->_coreClassName) . '"
						  }
				        }
				      } );';
		if (! $returnAsScript)
			$return .= ' });</script>';
		if ($returnHTML)
			return $return;
		$this->_html .= $return;
	}
	public static function _isFilledArray($array) {
		return ($array && is_array($array) && sizeof($array));
	}
	protected function _cleanOutput() {
		$this->_html = '';
		self::_cleanBuffer();
	}
	public static function _cleanBuffer() {
		if (ob_get_length() > 0) ob_clean();
	}
	protected function _echoOutput($die = false) {
		echo $this->_html;
		if ($die) die();
	}
	public function pmLog($var) {
		$result = date('[d/m/Y H:i:s]').'<br />';
		$result .= $var.'<br />';
		ob_start();
		var_dump($var);
		$result .= ob_get_clean();
	}
	public function _displayTabsPanel($params) {
		$this->_html .= '<div id="'.$params['id_panel'].'">';
        $this->_html .= '<ul style="height: 30px;">';
        foreach($params['tabs'] as $id_tab => $tab) {
        	$label = '';
        	if(isset($tab['img']) && $tab['img'])
        		$label .= '<img src="'.$tab['img'].'" alt="'.$tab['label'].'" title="'.$tab['label'].'" /> ';
        	$label .= $tab['label'];
        	if(isset($tab['url']) && $tab['url'])
        		$href = $tab['url'];
        	elseif(isset($tab['funcs']) && $tab['funcs'])
        		$href = '#tab-'.$params['id_panel'].'-'.$id_tab;
        	else continue;
        	$this->_html .= '<li><a href="'.$href.'"><span>'.$label.'</span></a></li>';
        }
        $this->_html .= '</ul>';
        foreach($params['tabs'] as $id_tab => $tab) {
        	if(isset($tab['funcs']) && $tab['funcs']) {
        		$this->_html .= '<div id="tab-'.$params['id_panel'].'-'.$id_tab.'">';
        		if(self::_isFilledArray($tab['funcs'])) {
        			foreach($tab['funcs'] as $func) {
        				call_user_func(array($this, $func));
        			}
        		}
        		elseif(!is_array($tab['funcs'])) {
        			call_user_func(array($this, $tab['funcs']));
        		}
				$this->_html .= '</div>';
        	}
        }
    	$this->_html .= '</div>';
    	$this->_html .= '<script type="text/javascript">
			$jqPm(document).ready(function() {
				$jqPm("#'.$params['id_panel'].'").tabs();
			});
        </script>';
	}
	public static function getHttpHost($http = false, $entities = false) {
		$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
		if ($entities) $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
		if ($http) $host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
		return $host;
	}
	protected function _onBackOffice() {
		if (isset($this->_cookie->id_employee) && Validate::isUnsignedId($this->_cookie->id_employee)) return true;
		return false;
	}
	protected static function _getNbDaysModuleUsage() {
		$sql = 'SELECT DATEDIFF(NOW(),date_add)
				FROM '._DB_PREFIX_.'configuration
				WHERE name = \''.pSQL('PM_'.self::$_module_prefix.'_LAST_VERSION').'\'
				ORDER BY date_add ASC';
		return (int)Db::getInstance()->getValue($sql);
	}
	public function hookDisplayBackOfficeHeader($params) {
		if (isset(Context::getContext()->controller) && isset(Context::getContext()->controller->controller_name) && strtolower(Context::getContext()->controller->controller_name) == 'adminmodules') {
			$this->_MHM_update();
		}
	}
	private function _MHM_needUpdate() {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && defined('Module::CACHE_FILE_MUST_HAVE_MODULES_LIST')) {
			if (file_exists(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST) && is_readable(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST) && is_writable(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST)) {
				$content = file_get_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST);
				if (!preg_match('#PM_MODS#', $content)) return true;
			}
		}
		return false;
	}
	private function _MHM_update() {
		if ($this->_MHM_needUpdate()) {
			$content = file_get_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST);
			if (strlen($content) == 0) $content = '<?xml version="1.0" encoding="UTF-8"?><modules></modules>';
			$new_content = Tools::file_get_contents('http://www.presta-module.com/cross-selling-addons-modules-footer?xml=1&iso='.$this->_iso_lang.'&pm='.$this->_getPMdata());
			if ($new_content !== false) {
				$content = str_replace('<modules>', '<modules><!-- PM_MODS -->'.$new_content.'<!-- /PM_MODS -->', $content);
				@file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, $content);
			} else {
				$content = str_replace('<modules>', '<modules><!-- PM_MODS --><!-- /PM_MODS -->', $content);
				@file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, $content);
			}
		}
	}
}
?>
