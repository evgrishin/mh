{strip}
{addJsDef egmultishop_citycontroller=$city_link}
{/strip}
{if !empty($yandex_metr)}
{$yandex_metr}
{/if}
{if !empty($google_anal)}
{$google_anal}
{/if}{if !empty($yandex_verify)}<meta name="yandex-verification" content="{$yandex_verify|escape:'html':'UTF-8'}" />{/if}
{if !empty($google_verify)}<meta name="google-site-verification" content="{$google_verify|escape:'html':'UTF-8'}" />{/if}
{if !empty($mail_verify)} 
<meta name="wmail-verification" content="{$mail_verify|escape:'html':'UTF-8'}" />{/if}