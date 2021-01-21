<?php
/**
*  @author Evgeny Grishin <e.v.grishin@yandex.ru>
*  @copyright  2015 Evgeny grishin
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
class egploader extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    protected $tabs = array(
        array('name' => 'Product Loader', 'class_name' => 'AdminEGPLOADER'),
        array('name' => 'Site Connecters', 'class_name' => 'AdminEGPCONNECTER'),
        array('name' => 'Products', 'class_name' => 'AdminEGPPRODUCT')
    );

    private $html = '';

    /**
     * 1) translations
     * 2) tab for maintanence
     * Enter description here ...
     */
    public function __construct()
    {
        $this->name = 'egploader';
        $this->tab = 'front_office_features';
        $this->version = '0.1.1';
        $this->author = 'Evgeny Grishin';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->html = '';

        parent::__construct();

        $this->displayName = $this->l('Product loader');
        $this->description = $this->l('Addon Product Loader.');
        //$this->registerHook('displayAdminProductsExtra') ||
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');



    }

    public function getContent()
    {
        // variables //
        // use cache
        // images images path url
        // content path server
        // proxy url
        // add link to ajax controller
        // use database name

       // $this->installAdminTab();


        $this->html .= $this->renderForm();
        return $this->html;

    }

    public function hookDisplayBackOfficeHeader()
    {
       // $this->installAdminTab();
        $this->context->controller->addJS($this->_path.'/views/js/ploader.js');
        return '<script>
				var admin_ploader_ajax_url = \''.$this->context->link->getAdminLink("AdminEGPLOADER").'\';
				var admin_pconnecter_ajax_url = \''.$this->context->link->getAdminLink("AdminEGPCONNECTER").'\';
				var admin_pproduct_ajax_url = \''.$this->context->link->getAdminLink("AdminEGPPRODUCT").'\';

				$(document).ready(function() {
                        
                     $("#create_cache").click(function() {
                         var delay = $("#load_delay").val();
				         var items = [];
				         var currentIndex = 0;

                         $("input:checkbox:checked").map(function() {
                             items.push($(this).val());
                          }).get();
                         
                         setTimeout(function() {
                        // currentIndex++;
                         download("createcache", this.id, items[currentIndex], "AdminEGPLOADER", admin_ploader_ajax_url, currentIndex);
                         //test_alert("create_cache", items[currentIndex++]);
                         
                         if(currentIndex++<items.length-1) {
                             setTimeout(arguments.callee, delay);
                         }
                         }, delay);
                     });
                     
                    $("#create_product").click(function() {
                         var delay = $("#load_delay").val();
				         var items = [];
				         var currentIndex = 0;

                         $("input:checkbox:checked").map(function() {
                             items.push($(this).val());
                          }).get();
                         
                         setTimeout(function() {
                        // currentIndex++;
                         download("createproduct", this.id, items[currentIndex], "AdminEGPLOADER", admin_ploader_ajax_url, currentIndex);
                         //test_alert("create_cache", items[currentIndex++]);
                         
                         if(currentIndex++<items.length-1) {
                             setTimeout(arguments.callee, delay);
                         }
                         }, delay);
                     });      

                    $("#product_create").click(function() {
                         var delay = $("#load_delay").val();
				         var items = [];
				         var currentIndex = 0;

                         $("input:checkbox:checked").map(function() {
                             items.push($(this).val());
                          }).get();
                         
                         setTimeout(function() {
                        // currentIndex++;
                         download("createproduct", this.id, items[currentIndex], "AdminEGPPRODUCT", admin_pproduct_ajax_url, currentIndex);
                         //test_alert("create_cache", items[currentIndex++]);
                         
                         if(currentIndex++<items.length-1) {
                             setTimeout(arguments.callee, delay);
                         }
                         }, delay);
                     });       
                    
                    $("#features_load, #product_meta_load, #product_name_load, #description_load, #price_load, #images_load, #consistions_load, #rewiews_load, #update_all").click(function() {
                         var delay = $("#load_delay").val();
				         var items = [];
				         var currentIndex = 0;
				         var currId = this.id;

                         $("input:checkbox:checked").map(function() {
                             items.push($(this).val());
                          }).get();
                         
                         setTimeout(function() {
                        // currentIndex++;
                         download("updatedata", currId, items[currentIndex], "AdminEGPPRODUCT", admin_pproduct_ajax_url, currentIndex);
                         //test_alert("create_cache", items[currentIndex++]);
                         
                         if(currentIndex++<items.length-1) {
                             setTimeout(arguments.callee, delay);
                         }
                         }, delay);
                     });                        
                     
                     // downloads connecters
                     $("#download_connecters").click(function() {
                         var delay = 1000;
				         var items = [];
				         var currentIndex = 0;

                         $("input:checkbox:checked").map(function() {
                             items.push($(this).val());
                          }).get();
                         
                         if (items.length>0){
                             setTimeout(function() {
                            // currentIndex++;
                             download_connecter("download_connecters", this.id, items[currentIndex], "AdminEGPLOADER", admin_ploader_ajax_url, currentIndex);
                             
                             if(currentIndex++<items.length-1) {
                                 setTimeout(arguments.callee, delay);
                             }
                             }, delay);
                         }
                     });  
                     $("#clear_log").click(function() {
                        $("#message_display").empty();
                     });
                     
                     $("#create_from_cache").click(function() {
                          //download("create_from_cache");
                     });      
                     

                });
				
				function test_alert(action,id){
				    console.log(id);
				    return setInterval(console.log("delay 5sek"), 10000);
				}
				
			</script>';
    }

    private function renderForm()
    {
       // !$this->registerHook('displayBackOfficeHeader');
        return 'product loader configuration';
    }

    public function getConfigFieldsValues()
    {

    }





    
  	public function installAdminTab()
	{

        $retval = true;
        $id_parent = 0;
        foreach ($this->tabs as $key => $ctab)
        {
            $tab = new Tab();
            $tab->active = 1;
            $languages = Language::getLanguages(true);
            if (is_array($languages))
                foreach ($languages as $language)
                    $tab->name[$language['id_lang']] = $ctab['name'];
            $tab->class_name = $ctab['class_name'];
            $tab->module = $this->name;
            $tab->id_parent = $id_parent;
            $retval = (bool)$tab->add();
            if ($key==117)
                $id_parent = $tab->id;
        }
        return $retval;
	}    
	
	public static function uninstallAdminTab()
	{
        $retval = true;
        $tabs = new egploader();
        foreach ($tabs->tabs as $ctab)
        {
            $idTab = Tab::getIdFromClassName($ctab['class_name']);
            if ($idTab != 0)
            {
                $tab = new Tab($idTab);
                $tab->delete();
                $retval = true;
            }
        }
        return $retval;
	}	



    public function install($keep = true)
    {

        if (!parent::install() ||
            !$this->registerHook('displayBackOfficeHeader') ||

           // !$this->registerHook('displayAdminProductsExtra') ||
        !$this->installAdminTab()
        )
        return false;
      return true;
    }

    public function uninstall($keep = true)
    {
      if (!parent::uninstall() || ($keep && !$this->deleteTables()) ||
      		!$this->uninstallAdminTab())
        return false;
      return true;
    }

    
    public function reset()
    {
        if (!$this->uninstall(false))
            return false;
        if (!$this->install(false))
            return false;
        return true;
    }

    public function deleteTables()
    {

    }
}

