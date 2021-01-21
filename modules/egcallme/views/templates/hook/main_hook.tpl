{*
*  @author Evgeny Grishin <e.v.grishin@yandex.ru>
*  @copyright  2015 Evgeny grishin
*}
{strip}
{addJsDef egcallme_ajaxcontroller=$ajaxcontroller}
{if $mask!=''}
{addJsDef egcallme_mask=$mask}
{/if}	
{/strip}
{if $btn_view=='Link'}
<div class="clearfix pull-left hidden-xs hidden-sm ddd" style="text-align: center;">
{if $phone!=''}
<div class="phone">{$phone|escape:'html':'UTF-8'}</div>
	{if (strpos($phone, '800'))}
		<div>{l s='free call' mod='egcallme'}</div>
	{/if}
{/if}
<div><a class="eg_callme_btn callme" href="#" rel="#">{l s='call me order' mod='egcallme'}</a></div>
</div>
<div class="clearfix hidden-md hidden-lg" style="text-align: center;">
    <a class="btn callme_button_mobile" id="eg_callmobile" href="tel:{$phone|escape:'html':'UTF-8'}" title="{l s='call' mod='egcallme'}{$phone|escape:'html':'UTF-8'}">
        <span>{l s='call' mod='egcallme'} {$phone|escape:'html':'UTF-8'}</span>
    </a>
</div>
{/if}
{if $btn_view=='Button'}
<div class="clearfix pull-left">
	<button class="eg_callme_btn" type="button">{l s='call me order' mod='egcallme'}</button>
</div>
{/if}
{if $btn_view=='Self'}
{*$btn_self|escape:'quotes':'UTF-8'*}
{/if}
{if $phone_tube=='Show' or  $phone_tube=='Animation'}

<style type="text/css">
{if $phone_tube=='Animation'}
    @keyframes cbh-circle-anim {
        0% {
            transform: scale(1);
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -o-transform: scale(1);
            -ms-transform: scale(1);
        }
        40% {
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=90)";
            opacity: 0.9;
            transform: scale(1.2);
            -webkit-transform: scale(1.2);
            -moz-transform: scale(1.2);
            -o-transform: scale(1.2);
            -ms-transform: scale(1.2);
        }
        100% {
            transform: scale(1.5);
            -webkit-transform: scale(1.5);
            -moz-transform: scale(1.5);
            -o-transform: scale(1.5);
            -ms-transform: scale(1.5);
        }
    }

    @keyframes cbh-circle-fill-anim {
        0%, 100% {
            transform: scale(1.5);
            -webkit-transform: scale(1.5);
            -moz-transform: scale(1.5);
            -o-transform: scale(1.5);
            -ms-transform: scale(1.5);
        }
        40% {
            transform: scale(1);
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -o-transform: scale(1);
            -ms-transform: scale(1);
        }
    }

    @keyframes cbh-circle-img-anim {
        0%, 50%, 100% {
            transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
        }
        10%, 30% {
            transform: rotate(-25deg);
            -webkit-transform: rotate(-25deg);
            -moz-transform: rotate(-25deg);
            -o-transform: rotate(-25deg);
            -ms-transform: rotate(-25deg);
        }
        20%, 40% {
            transform: rotate(25deg);
            -webkit-transform: rotate(25deg);
            -moz-transform: rotate(25deg);
            -o-transform: rotate(25deg);
            -ms-transform: rotate(25deg);
        }
    }

    @-webkit-keyframes cbh-circle-anim {
        0% {
            transform: scale(1);
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -o-transform: scale(1);
            -ms-transform: scale(1);
        }
        40% {
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=90)";
            opacity: 0.9;
            transform: scale(1.2);
            -webkit-transform: scale(1.2);
            -moz-transform: scale(1.2);
            -o-transform: scale(1.2);
            -ms-transform: scale(1.2);
        }
        100% {
            transform: scale(1.5);
            -webkit-transform: scale(1.5);
            -moz-transform: scale(1.5);
            -o-transform: scale(1.5);
            -ms-transform: scale(1.5);
        }
    }

    @-webkit-keyframes cbh-circle-fill-anim {
        0%, 100% {
            transform: scale(1.5);
            -webkit-transform: scale(1.5);
            -moz-transform: scale(1.5);
            -o-transform: scale(1.5);
            -ms-transform: scale(1.5);
        }
        40% {
            transform: scale(1);
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -o-transform: scale(1);
            -ms-transform: scale(1);
        }
    }

    @-webkit-keyframes cbh-circle-img-anim {
        0%, 50%, 100% {
            transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
        }
        10%, 30% {
            transform: rotate(-25deg);
            -webkit-transform: rotate(-25deg);
            -moz-transform: rotate(-25deg);
            -o-transform: rotate(-25deg);
            -ms-transform: rotate(-25deg);
        }
        20%, 40% {
            transform: rotate(25deg);
            -webkit-transform: rotate(25deg);
            -moz-transform: rotate(25deg);
            -o-transform: rotate(25deg);
            -ms-transform: rotate(25deg);
        }
    }

    @-moz-keyframes cbh-circle-anim {
        0% {
            transform: scale(1);
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -o-transform: scale(1);
            -ms-transform: scale(1);
        }
        40% {
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=90)";
            opacity: 0.9;
            transform: scale(1.2);
            -webkit-transform: scale(1.2);
            -moz-transform: scale(1.2);
            -o-transform: scale(1.2);
            -ms-transform: scale(1.2);
        }
        100% {
            transform: scale(1.5);
            -webkit-transform: scale(1.5);
            -moz-transform: scale(1.5);
            -o-transform: scale(1.5);
            -ms-transform: scale(1.5);
        }
    }

    @-moz-keyframes cbh-circle-fill-anim {
        0%, 100% {
            transform: scale(1.5);
            -webkit-transform: scale(1.5);
            -moz-transform: scale(1.5);
            -o-transform: scale(1.5);
            -ms-transform: scale(1.5);
        }
        40% {
            transform: scale(1);
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -o-transform: scale(1);
        }
    }

    @-moz-keyframes cbh-circle-img-anim {
        0%, 50%, 100% {
            transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
        }
        10%, 30% {
            transform: rotate(-25deg);
            -webkit-transform: rotate(-25deg);
            -moz-transform: rotate(-25deg);
            -o-transform: rotate(-25deg);
            -ms-transform: rotate(-25deg);
        }
        20%, 40% {
            transform: rotate(25deg);
            -webkit-transform: rotate(25deg);
            -moz-transform: rotate(25deg);
            -o-transform: rotate(25deg);
            -ms-transform: rotate(25deg);
        }
    }
{/if}    

    #pozvonim-cover *,
    #pozvonim-cover *::before,
    #pozvonim-cover *::after,
    .pozvonim-button *,
    .pozvonim-button *::before,
    .pozvonim-button *::after,
    #pozvonim-wrapper *,
    #pozvonim-wrapper *::before,
    #pozvonim-wrapper *::after {
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    #pozvonim-wrapper {
        position: fixed !important;
        z-index: 30000000 !important;
        min-width: 0 !important;
    }

    #pozvonim-wrapper iframe {
        -webkit-transition: all 300ms ease 0s;
        -moz-transition: all 300ms ease 0s !important;
        -o-transition: all 300ms ease 0s !important;
        transition: all 300ms ease 0s !important;
        display: block !important;
    }

    #pozvonim-wrapper .pozvonim-wrapper-opening {
        background-color: #e6e6e6 !important;
    }

    #pozvonim-wrapper .pozvonim-wrapper-toggler {
        position: absolute !important;
        background-position: 50% 50% !important;
        background-repeat: no-repeat !important;
        cursor: pointer !important;
    }

    #pozvonim-wrapper .pozvonim-wrapper-closing,
    #pozvonim-wrapper.opened .pozvonim-wrapper-opening {
        display: none !important;
    }

    #pozvonim-wrapper .pozvonim-wrapper-opening,
    #pozvonim-wrapper.opened .pozvonim-wrapper-closing {
        display: block !important;
    }

    #pozvonim-wrapper.TOP_LEFT {
        left: 0 !important;
        top: 0 !important;
        height: 100% !important;
    }

    #pozvonim-wrapper.TOP_LEFT iframe {
        height: 100% !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        margin-left: -320px !important;
        width: 320px !important;
    }

    #pozvonim-wrapper.TOP_LEFT.opened iframe {
        margin-left: 0 !important;
    }

    #pozvonim-wrapper.TOP_LEFT .pozvonim-wrapper-toggler {
        margin-top: -32.5px !important;
        width: 25px !important;
        height: 65px !important;
        top: 50% !important;
    }

    #pozvonim-wrapper.TOP_LEFT .pozvonim-wrapper-opening {
        right: -25px !important;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAYAAAAXCAYAAAAoRj52AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDoAABSCAABFVgAADqXAAAXb9daH5AAAAD0SURBVHjaXNExLwRRFIbhZ1ehUBGVCqGT0NlEYRU7CiIS+wPM7zCd+R/TqYRCRNysUiERSolCNAoqjYJgNNfkZk5zizffm3PP1xnsHxyiwHaoynNxurjEN3Yk08U9bjDM8mKyAaEqP3CFKQzSBIzwi90sLzopuIvKdcw1IOouMINemoDjqNvK8mIsBQ9xuz5mGxCq8gunUbeWJv63e8deGzzjBSttsIwFhDbYxDjOOnVdgywvJuInf9BPE6tYRMBbCoaoEUJV1t2omY7+R9ymJ+lhHqNQla8pyOJ70vQRNRt4wnVaVA9LOApV+dkGb7GPZv4GAHMLRBks9t3xAAAAAElFTkSuQmCC');
        border-radius: 0 3px 3px 0 !important;
    }

    #pozvonim-wrapper.TOP_LEFT .pozvonim-wrapper-closing {
        right: 0 !important;
        background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAYAAAAXCAYAAAAoRj52AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDoAABSCAABFVgAADqXAAAXb9daH5AAAADySURBVHjaXNItS8RBEIDx51bRcEXh0GCRE8RmEQRf2qEYLIcfyS/hB9CkIIJBi+U4DAZBQREMBoNwRoPI8VhmZfwvLBtm57c7w6CS9ob6oR4W/q89YA64zben1Vf1Qe3kjE2gC9wAoxzox3kNUJl59TmojkrNWAOWgStgBFCAFrAT5+kfHMyT+qK26y8LsA6sAGfAV00owD7wHT6ZelMf1dncngLcAwvAYk4oYc8AvSa1pL6rQ3WqUqgT6rE6VlfzG2PgMtiDTKF2g7urRdZASz0Jbjs3UeA8uF6miAI/1YHapjEMR+qPutUchgtgEtj9HQCxFOt2q53iKQAAAABJRU5ErkJggg==);
    }

    #pozvonim-wrapper.TOP_RIGHT {
        top: 0 !important;
        right: 0 !important;
        height: 100% !important;
    }

    #pozvonim-wrapper.TOP_RIGHT iframe {
        height: 100% !important;
        width: 320px !important;
        margin-right: -320px !important;
    }

    #pozvonim-wrapper.TOP_RIGHT.opened iframe {
        margin-right: 0 !important;
    }

    #pozvonim-wrapper.TOP_RIGHT .pozvonim-wrapper-toggler {
        top: 50% !important;
        width: 25px !important;
        height: 65px !important;
        margin-top: -32.5px !important;
    }

    #pozvonim-wrapper.TOP_RIGHT .pozvonim-wrapper-opening {
        left: -25px !important;
        border-radius: 3px 0 0 3px !important;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAYAAAAXCAYAAAAoRj52AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAPlJREFUeNpi/P//PwMMuCXWeAOpLUDcwsSACvyA+A8Q72RCUi0IpEKA+CQQn0fW4QrEQkC8d9f8lq9MUNWMQCoAiP8B8R6QGEyHIhDbg4wA4nPIEhZALAXE20HGgCWAxjADaW+oMWtgFoJ0KACxA9Q115ElrKHGrAca8wtZIhiIP8BcgyxhAMRPgfgBusQuIFYBYn10ic1AzA7E7ugSINfcANkFdDo3ssQrqHGqQGwOlwA68T9U4j80dBmQg+QMEN8C2QM0TgQuAdT1EuoPJWi4MSDHxzpYnKFLHAXie0DsCDIOLgE07ieQWgbEOiDj0BPDdqjzLQACDABkKEGr78e33gAAAABJRU5ErkJggg==');
    }

    #pozvonim-wrapper.TOP_RIGHT .pozvonim-wrapper-closing {
        left: 0 !important;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAYAAAAXCAYAAAAoRj52AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAO5JREFUeNpc0r9qAkEQx/G9M2ghgoIkhY0YELs0gYB/ukOxsBEfyZfIAySVgSCkMI2FIhYWgoUiWFoIsbQQOS7fOXZhvIHPbTHsb2eXM1EUDXFCHcbxjTFLPKJrdNEtYoMDMnrHH6aooOE2+Hb9tWtfR7k4idrhyUUZGzdBFa86SmoED+14VbNnscdW4vSOC75Qw5tv7kvOuaLnSYyqAmbIJXeUUcI62QiQj89SU6WxwBHPuvGCEB9I6aiBvfAPQn25lY2pxJPaRsvGfMLTjxjYmG95cPfsEjPHGQU3jHyauOE9+TN08ICxvum/AAMA77jboW0LeaAAAAAASUVORK5CYII=');
    }

    #pozvonim-wrapper.TOP_CENTER {
        left: 0 !important;
        top: 0 !important;
        height: auto !important;
        width: 100% !important;
    }

    #pozvonim-wrapper.TOP_CENTER iframe {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        width: 100% !important;
        margin-top: -300px !important;
    }

    #pozvonim-wrapper.TOP_CENTER.opened iframe {
        margin: 0 !important;
        height: 300px !important;
    }

    #pozvonim-wrapper.TOP_CENTER .pozvonim-wrapper-toggler {
        top: auto !important;
        left: 50% !important;
        margin-left: -32.5px !important;
        height: 15px !important;
        width: 65px !important;
    }

    #pozvonim-wrapper.TOP_CENTER .pozvonim-wrapper-opening {
        bottom: -15px !important;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAGCAYAAAAooAWeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDoAABSCAABFVgAADqXAAAXb9daH5AAAADhSURBVHjapNGxK4VxFMbxz+8mBouJwSKDGK/ULUbeK4OB7mR6/R3X+P4f7z9ASRm8Bgzq2igbYVJKTAaDXsshdA1y6kznPN/nOZ2U5d0CPfSqsnj0z2pvbg2jieWU5d0HjOISR6j+ahTAWSxhES0MpCzvzmMFG5iM/RscYBtnVVm89AEOYgad0DfRwFPo9lJd1x/LQ1jAeiSYQsIVdsLsAhMxX4uEDdzjBLs4rMriGT7hP1KNYQ7t6Gm84hrjGAngMfbjZ7dVWXyD9YV/MUnxjxZWw+g8LjnFXVUWb7/p3wcAePZGK3bqMAgAAAAASUVORK5CYII=');
        border-radius: 0 0 3px 3px !important;
    }

    #pozvonim-wrapper.TOP_CENTER .pozvonim-wrapper-closing {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAGCAYAAAAooAWeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDoAABSCAABFVgAADqXAAAXb9daH5AAAADXSURBVHjalJG9SoIBFIafD8IGl1xq8AbcxBAEp6iwQVq9E68goRsQwRtwc3Rx0aVoaGhokEh0SRDqAhrscfBIn4NSL5ztvM97fhKVA0qAU6AC3AI14AXoAw/AHFjtNe+BnwHlgNWAAvANvAN54ARYAGNgADwBM2AXpm7rWL1U2+pE/XGjN/VevVBzakltqo/qKno+1J7aiB5UUKvqnTr1V1O1E2HZ1ADpyqjF8D6ngr7UrlpP1GXc9RUYAcNY85O/KwucA9fAVfzoKFFbAfsv8FBQCbhZDwCN9MFYxMJfmAAAAABJRU5ErkJggg==');
        margin-top: 0 !important;
        bottom: 0 !important;
    }

    #pozvonim-wrapper.BOTTOM_CENTER {
        left: 0 !important;
        bottom: 0 !important;
        height: auto !important;
        width: 100% !important;
    }

    #pozvonim-wrapper.BOTTOM_CENTER iframe {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        width: 100% !important;
        margin-bottom: -300px !important;
    }

    #pozvonim-wrapper.BOTTOM_CENTER.opened iframe {
        margin: 0 !important;
        height: 300px !important;
    }

    #pozvonim-wrapper.BOTTOM_CENTER .pozvonim-wrapper-toggler {
        margin-left: -32.5px !important;
        width: 65px !important;
        left: 50% !important;
        height: 15px !important;
    }

    #pozvonim-wrapper.BOTTOM_CENTER .pozvonim-wrapper-opening {
        top: -15px !important;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAGCAYAAAAooAWeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDoAABSCAABFVgAADqXAAAXb9daH5AAAADhSURBVHjalNCxK0VxGMbxz09isJgYLDKI8UopRu6RwUAm0zl/xz3j+T/OP0BJGRwDBnVtlI0wKSUmg0HH8o73hmd9e77P+zypbVvDlBVlwhRWsI0MNzjEFZ6buvoe5k+D4FlRTmM5YBkW8IUHzGASL7jACfp4auqqHQjPinIca9jFBuaRcB+fnuIWs3HfiUYjEXSJI5w1dfUBqZv3VrGFfcxF6GPADnDd1NXngHZjWMRe+DsR9B6+49TNe6+x6x3O0aDf1NWbPyorygksRaP1aDSaunmvis3+BfwlqIPNnwEAthFGK1lcy2EAAAAASUVORK5CYII=');
        border-radius: 3px 3px 0 !important;
        bottom: auto !important;
    }

    #pozvonim-wrapper.BOTTOM_CENTER .pozvonim-wrapper-closing {
        top: 0 !important;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAGCAYAAAAooAWeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDoAABSCAABFVgAADqXAAAXb9daH5AAAADUSURBVHjapJG9SoIBFIafD0KHb8nFBm+gTZRAcJIKG6TVO+kKErwBCboBt8YWF12MhoYGB5HElgQhL6BBH5dDGuhiL7zTeX845yRqC3gNfvN/pEAJuEnUBZAHRkAf6B1RlAJl4Bq4AirACWpVvVenbjFVH9RLNVXZw4xaDO+bugrvUn1UG7vibIR11LG6DvFEbas1NaeW1Dv1ZSfwS+2qzdCgkqj71jwDLoB68Bz4AT6AAnAKzIEB8BxnnAF/wg6F/87jHxXgNoregSdgCHwCq0PmzQCi5MFYiklGogAAAABJRU5ErkJggg==');
    }

    #pozvonim-wrapper.CENTER {
        width: 100% !important;
        height: 100% !important;
        left: 0 !important;
        top: 0 !important;
        visibility: hidden !important;
        background-color: rgba(0, 0, 0, 0.3) !important;
    }

    #pozvonim-wrapper.CENTER.opened {
        visibility: visible !important;
    }

    #pozvonim-wrapper.CENTER iframe {
        border-radius: 5px !important;
        height: 360px !important;
        margin: -175px auto 0 !important;
        position: relative !important;
        top: 50% !important;
        width: 570px !important;
        display: none !important;
    }

    #pozvonim-wrapper.CENTER.opened iframe {
        display: block !important;
    }

    #pozvonim-wrapper.CENTER .pozvonim-wrapper-toggler {
        display: none !important;
    }

    .pozvonim-button {

        opacity: 0;
        cursor: pointer !important;
        position: fixed;
        display: block;
        z-index: 10000000 !important;
        background: transparent none repeat scroll 0 0 !important;
        transform: scale( 1 ) !important;
    }

    

    
    .pozvonim-button.pozvonim-button-animation {
    transition: all 0.8s ease 0s;
    }

    .pozvonim-button * {
        transform-origin: center center 0 !important;
    }

    .pozvonim-button.pozvonim-draggable {
        transition: none 0s ease 0s !important;
    }

    .pozvonim-button .pozvonim-button-phone {
        -webkit-animation: cbh-circle-img-anim 1.2s ease-in-out 0s normal none infinite running ;
        -moz-animation: cbh-circle-img-anim 1.2s ease-in-out 0s normal none infinite running ;
        -o-animation: 1.2s ease-in-out 0s normal none infinite running cbh-circle-img-anim;
        animation: cbh-circle-img-anim 1.2s ease-in-out 0s normal none infinite running ;
        position: absolute !important;
        background-color: #{$phone_color|escape:'htmlall':'UTF-8'} !important;
        background-color: rgba({$rgb[0]|escape:'htmlall':'UTF-8'}, {$rgb[1]|escape:'htmlall':'UTF-8'}, {$rgb[2]|escape:'htmlall':'UTF-8'}, 0.8) !important;
        background-color: rgba({$rgb[0]|escape:'htmlall':'UTF-8'}, {$rgb[1]|escape:'htmlall':'UTF-8'}, {$rgb[2]|escape:'htmlall':'UTF-8'}, 0.8) !important;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEQAAABECAYAAAA4E5OyAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAJKSURBVHja7Nk7aBRRFIDh/8QHPvBBQNTGRgVNZ7AI2Ai+sBOLiAabINgKirWCjQQRQUtLC8FCgoUprERsVLAzRhAUo60WIWrgt3BFXTK7i9nZZJxz4DZzL7tzv7mvMxMqGb+jLwkSJEESJEESJEESJEESJEESJEESJEESJEESJCNBeggSEV0pwHbgOTALjAHLmurLCbWrpUv3tEF97d/xUF1X1n3/Kkt1ytwAdjZdOwJMqGtK/eelNkLUQ7aOm2WOkOj2d5mFzm/1BbCnVRNgAHj13+8y6mAbDIAARuuy7e7osN2BuoC8bEyJdrGrFiARMQmc76BpaTvNkltUG2vJCeA2sLagySywujZH94i4Cxxt0WSqdrlMRDwGJguqn9YORO0DthZUj9cx2z0GrJ/n+jQwUSsQdRVwpaD6ekTM1W2EXAV2z3P9HXCrbsndSIvE7njZ6X9PQNSN6pYOMPapXwsw7pX5IHsCom5W76hzjU49UjcVYAypnwsw3qv9lQZRB9VP83RuSt3W1LlT6kwBxqw6VPZULxVEPah+abEWfFTPqMPqeJsXQiO9WPtKe0EEHAYeACu68FsXIuJaEUglkjvgDT/fmC80LkfEpVa7Y1VAZrqQiV6MiLF2x4WqHMzOLRDzZDuMyh3M1NE/ttpO45k6sFj33Yttd7/6tgOIafWsunwxH2RPPkOoK4HTwDCwF+gHvgMfgCeNNP5+RHz7l5FdlUW10pFf/xMkQRIkQRIkQRIkQRIkQRIkQRIkQRIkQRIkI0FaxI8BAMGiej+TuldEAAAAAElFTkSuQmCC');
        border-radius: 100% !important;
        height: 70px !important;
        width: 70px !important;
        left: 44px !important;
        top: 44px !important;
        z-index: 10000000 !important;
    }

    .pozvonim-button:hover .pozvonim-button-phone {
        background-color: #79D000 !important;
        background-color: rgba(121, 208, 0, 0.8) !important;
        background-color: rgba(121, 208, 0, 0.8) !important;
    }

    .pozvonim-button .pozvonim-button-border-inner {
        -webkit-animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-fill-anim;
        -moz-animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-fill-anim;
        -o-animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-fill-anim;
        animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-fill-anim;
        border: 1px solid #{$phone_color|escape:'htmlall':'UTF-8'} !important;
        border: 1px solid #{$phone_color|escape:'htmlall':'UTF-8'} !important;
        border-radius: 100% !important;
        opacity: 0.5 !important;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)" !important;
        height: 70px !important;
        left: 44px !important;
        top: 44px !important;
        width: 70px !important;
        position: absolute !important;
        z-index: 10000000 !important;
    }

    .pozvonim-button .pozvonim-button-border-outer {
        -webkit-animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-anim;
        -moz-animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-anim;
        -o-animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-anim;
        animation: 2.3s ease-in-out 0s normal none infinite running cbh-circle-anim;
        border: 1px solid #{$phone_color|escape:'htmlall':'UTF-8'} !important;
        border: 1px solid #{$phone_color|escape:'htmlall':'UTF-8'} !important;
        border-radius: 100% !important;
        width: 100px !important;
        height: 100px !important;
        left: 30px !important;
        top: 30px !important;
        position: absolute !important;
        z-index: 10000000 !important;
    }

    .pozvonim-button:hover .pozvonim-button-border-inner {
        border: 1px solid #{$phone_color|escape:'htmlall':'UTF-8'} !important;
        border: 1px solid #{$phone_color|escape:'htmlall':'UTF-8'} !important;
    }

    .pozvonim-button:hover .pozvonim-button-border-outer {
        border: 1px solid #b7de69 !important;
        border: 1px solid #{$phone_color|escape:'htmlall':'UTF-8'} !important;
    }

    
    .pozvonim-button:hover .pozvonim-button-phone {
        display: none;
    }

    .pozvonim-button:hover .pozvonim-button-border-inner {
        height: 90px !important;
        left: 33px !important;
        top: 33px !important;
        width: 90px !important;
    }

    .pozvonim-button:hover .pozvonim-button-border-outer {
        height: 100px !important;
        left: 28px !important;
        top: 28px !important;
        width: 100px !important;
    }

    .pozvonim-button:hover .pozvonim-button-text {
        display: block;
    }

    .pozvonim-button .pozvonim-button-text {
        background-color: rgba(121, 208, 0, 0.8) !important;
        color: #ffffff !important;
        border-radius: 100% !important;
        display: none;
        width: 90px !important;
        height: 90px !important;
        left: 33px !important;
        top: 33px !important;
        margin: 0 !important;
        position: absolute;
        text-align: center !important;
        font-size: 14px !important;
        font-family: "Open Sans", "Helvetica Neue", "Helvetica", arial, sans-serif !important;
        font-weight: 500 !important;
        line-height: 19px !important;

    }

    .pozvonim-button .pozvonim-button-center-text {
        vertical-align: middle;
        text-align: center;
        display: table-cell;
        height: 90px;
        width: 90px;
        word-break: break-all !important;
        font-size: 14px !important; font-weight: 500 !important;
        font-family: "Open Sans", "Helvetica Neue", "Helvetica", arial, sans-serif !important;
        white-space: pre-wrap !important;
    }


    
    .pozvonim-button .pozvonim-button-wrapper {
        width: 160px;
        height: 160px;
    }

    #pozvonim-cover {
        background-color: rgba(0, 0, 0, 0.25) !important;
        display: none;
        height: 100% !important;
        position: fixed !important;
        top: 0 !important;
        width: 100% !important;
        z-index: 20000000 !important;
    }    
</style>

<div class="eg_callme_btn pozvonim-button BOTTOM_RIGHT pozvonim-button-animation" style="position: fixed; bottom: 30px;right: 50px;z-index: 999;; cursor: pointer; opacity: 1; display: block;">
    <div class="pozvonim-button-wrapper actionShow">    
        <div class="pozvonim-button-border-inner"></div>
        <div class="pozvonim-button-border-outer"></div>
        <div class="pozvonim-button-text">
            <span class="pozvonim-button-center-text">{l s='Free callback' mod='egcallme'}</span>
        </div>
        <div class="pozvonim-button-phone"></div>
    </div>
</div>
{/if}