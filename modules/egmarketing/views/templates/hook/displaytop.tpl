
<div id="leadia_science_widget" style="display:none;">
    <div class="leadia_widget" id="leadia-widget" style="right: 0px; bottom: 180px;">
        <div class="leadia_widget_header" id="leadia-widget-header">
<!-- leadia_widget_photo_flipped -->
            <div class="leadia_widget_photo">
              <div class="leadia_widget_photo_flipper">
                <div class="leadia_widget_photo_front">
                  <img class="leadia-widget-header-img" src="{$oimg}">
                </div>
                <!-- div class="leadia_widget_photo_back">
                  <img class="" src="//s3-eu-west-1.amazonaws.com/static.venyoo.ru/widget/img/call.png">
                </div-->
              </div>
            </div>

            <div class="leadia_widget_title">
                <div class="leadia-widget-worker-name">{$mname}</div>
            </div>
            <div class="leadia_widget_text">{$mtiz}</div>
            <div class="leadia_widget_minimize" style=""></div>
            <div class="leadia_widget_bell">
              <div class="leadia_widget_bell_icon"></div>
              <div class="leadia_widget_bell_notif"></div>
            </div>
        </div>
        <div class="leadia_widget_body">
            <div id="leadia_content_custom_scroll" class="leadia_content_custom_scroll mCustomScrollbar _mCS_10000 mCS-autoHide mCS_no_scrollbar" style="position: relative; overflow: visible; height: 175px;"><div id="mCSB_10000" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" tabindex="0">
            <div id="mCSB_10000_container" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" style="position: relative; left: 0px; top: 0px;" dir="ltr">
                <div class="leadia_widget_list" id="leadia_widget_list">
                    

                <div class="leadia_widget_msg leadia-widget-message-block leadia-widget-message-me" style="">
    <div class="msg_wrap">
        <div class="leadia-widget-message-text">{$message}</div>
        <div class="msg_wrap_corner"></div>
    </div>
    {if $message2!=''}
    <div class="msg_wrap">
        <div class="leadia-widget-message-text">{$message2}</div>
        <div class="msg_wrap_corner"></div>
    </div>
   {/if}
</div>
                </div>

            </div></div>
            <div id="mCSB_10000_scrollbar_vertical" class="mCSB_scrollTools mCSB_10000_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: none;"><div class="mCSB_draggerContainer"><div id="mCSB_10000_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; height: 0px; top: 0px;" oncontextmenu="return false;"><div class="mCSB_dragger_bar" style="line-height: 50px;"></div></div><div class="mCSB_draggerRail"></div></div></div>
            
            </div>



        </div>
        <div class="leadia_develope_by"><div class="leadia_develop_container"><div class="leadia_develop_container_inset"></div></div></div>
    </div>



<script>
$(document).ready(function(){
	window.setTimeout('messagerShow()',{$delay}000);
	$(document).on("click", '.leadia_widget_minimize, .leadia-widget-worker-name', function(e){
		$('#leadia_science_widget').fadeOut();
	});
});

function messagerShow()
{
	$('#leadia_science_widget').fadeIn(900);//show("slow");
	$('#leadia_science_widget').css('zIndex', '999999');
}
</script>