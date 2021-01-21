<?php


class FrontController extends FrontControllerCore
{
    public $cache2level = '';

    /*
   public function run()
   {
       parent::run();

       $this->init();
       if ($this->checkAccess()) {

           if ($this->product->id == 27) {
               //file_put_contents(_PS_CACHE_DIR_ . '1.txt', $content);
               $this->cache2level = 'content';
           }

           if ($this->cache2level == '')
           {
               // setMedia MUST be called before postProcess
               if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
                   $this->setMedia();

               // postProcess handles ajaxProcess
               $this->postProcess();

               if (!empty($this->redirect_after))
                   $this->redirect();

               if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
                   $this->initHeader();

               if ($this->viewAccess()) {

                   $this->initContent();
               } else
                   $this->errors[] = Tools::displayError('Access denied.');

               if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className)))
                   $this->initFooter();

               // default behavior for ajax process is to use $_POST[action] or $_GET[action]
               // then using displayAjax[action]
               if ($this->ajax)
               {
                   $action = Tools::toCamelCase(Tools::getValue('action'), true);
                   if (!empty($action) && method_exists($this, 'displayAjax'.$action))
                       $this->{'displayAjax'.$action}();
                   elseif (method_exists($this, 'displayAjax'))
                       $this->displayAjax();
               }
               else
                   $this->display();
           }else
               $this->displayStaticCache();
       }
       else
       {
           $this->initCursedPage();
           $this->smartyOutputContent($this->layout);
       }

   }
*/
    public function displayStaticCache()
    {
        $path = _PS_CACHE_DIR_ . '1.txt';

        //$html = file_get_contents($path);
        $file_handle = fopen($path, "r");
        $html = '';
        while (!feof($file_handle))
            $html .= fgets($file_handle);

        fclose($file_handle);
        echo $html;
    }
/*
    public function display()
    {
        Tools::safePostVars();

        // assign css_files and js_files at the very last time
        if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache'))
        {
            // CSS compressor management
            if (Configuration::get('PS_CSS_THEME_CACHE'))
                $this->css_files = Media::cccCss($this->css_files);
            //JS compressor management
            if (Configuration::get('PS_JS_THEME_CACHE') && !$this->useMobileTheme())
                $this->js_files = Media::cccJs($this->js_files);
        }

        $this->context->smarty->assign(array(
            'css_files' => $this->css_files,
            'js_files' => ($this->getLayout() && (bool)Configuration::get('PS_JS_DEFER')) ? array() : $this->js_files,
            'js_defer' => (bool)Configuration::get('PS_JS_DEFER'),
            'errors' => $this->errors,
            'display_header' => $this->display_header,
            'display_footer' => $this->display_footer,
        ));

        $layout = $this->getLayout();
        if ($layout)
        {
            if ($this->template)
                $template = $this->context->smarty->fetch($this->template);
            else // For retrocompatibility with 1.4 controller
            {
                ob_start();
                $this->displayContent();
                $template = ob_get_contents();
                ob_clean();

            }
            $template = $this->context->smarty->assign('template', $template);
            $this->smartyOutputContent($layout);
        }
        else
        {
            Tools::displayAsDeprecated('layout.tpl is missing in your theme directory');
            if ($this->display_header)
                $this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');

            if ($this->template)
                $this->smartyOutputContent($this->template);
            else // For retrocompatibility with 1.4 controller
                $this->displayContent();

            if ($this->display_footer)
                $this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
        }
        $p=1;

        return true;
    }

*/
    /*
	* module: fsadvancedurl
	* date: 2019-03-12 14:52:38
	* version: 2.1.2
	*/
	protected function canonicalRedirection($canonical_url = '')
    {
        $fsau_params = array();
        if (Module::isEnabled('fsadvancedurl')) {
            $fsau = Module::getInstanceByName('fsadvancedurl');
            $info = $fsau->overrideCanonicalUrl($canonical_url);
            $canonical_url = $info['canonical_url'];
            if (isset($info['params']) && is_array($info['params']) && $info['params']) {
                $fsau_params = $info['params'];
            }
        }

        parent::canonicalRedirection($canonical_url);

        if ($fsau_params) {
            $_GET = array_merge($_GET, $fsau_params);
        }
    }

    /*
	* module: fsadvancedurl
	* date: 2019-03-12 14:52:38
	* version: 2.1.2
	*/
	protected function updateQueryString(array $extraParams = null)
    {
        $uriWithoutParams = explode('?', $_SERVER['REQUEST_URI']);
        if (isset($uriWithoutParams[0])) {
            $uriWithoutParams = $uriWithoutParams[0];
        }

        $url = Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$uriWithoutParams;

        if (Module::isEnabled('fsadvancedurl')) {
            $fsau = Module::getInstanceByName('fsadvancedurl');
            $url = $fsau->overrideUpdateQueryStringBaseUrl($url, $extraParams);
        }

        $params = array();
        parse_str($_SERVER['QUERY_STRING'], $params);

        if (null !== $extraParams) {
            foreach ($extraParams as $key => $value) {
                if (null === $value) {
                    unset($params[$key]);
                } else {
                    $params[$key] = $value;
                }
            }
        }

        ksort($params);

        if (null !== $extraParams) {
            foreach ($params as $key => $param) {
                if (null === $param || '' === $param) {
                    unset($params[$key]);
                }
            }
        } else {
            $params = array();
        }

        $queryString = str_replace('%2F', '/', http_build_query($params));

        return $url.($queryString ? "?$queryString" : '');
    }
}
