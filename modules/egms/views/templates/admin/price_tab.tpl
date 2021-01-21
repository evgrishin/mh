<script>
    var admin_egms_ajax_url = '{$ajax_url}';
</script>
<script>
$(document).ready(function() {
	$(document).on("click", "#result", function(e){
        $("#content1").empty();
        $("#content1").append('<table id="tableprice" border="1"></table>');
        $("#content2").empty();
		var ctype = $("#ctype option:selected").val();

		switch (ctype) {
               case "1":
                   calculationType1();
                   break;

               case "2":
                   calculationType2();
                   break;
			   case "3":
					calculationType1();
					break;
			   case "4":
                   calculationType4();
                   break;
			   case "5":
				   calculationType5();
				   break;
			default:
				   alert("define calculation type!!!");
				   break;
        }

        var attr_content = $("textarea#attr_content").val();
        var attr_lines = attr_content.split("\n");

        $("#content2").append('<select id="attr"></select>');
        var attrs = new Array();
        for(i=0;i<attr_lines.length;i++)
        {
            $("#attr").append('<option>' + attr_lines[i] + '</option>');
            attrs.push(attr_lines[i]);
        }
        $("#attrs").val(attrs);
			

	});
	$(document).on("click", "#clear", function(e){
		$("#content1").empty();
        $("#content2").empty();
		$("#update_price").val("false");
	});
});

function calculationType4()
{
    var price_text = $("textarea#price_content").val();
    var lines = price_text.split("\n");
    var demping = $("#demping").val();
    var discount = $("#discount").val();

    var size = new Array();
    var price = new Array();
    var price_dif = new Array();

	$.ajax({
        type: 'GET',
        url: admin_egms_ajax_url,
        data: {
            action : 'getUrl',
            ajax : true,
            id_url: price_text,
        }})
        .done(function (data) {

            var base = 0;
            var diff = 0;

            $("#content1").append('<select id="sl"></select>');
            $("#update_price").val("true");

            $(data).find('#mod_id option').each(function(i){



                var pre_price = $(this).attr('data-text');
                console.log($(this).attr('data-kit') + ' - ' + pre_price.split(/[—]/)[1].replace(/[^-0-9]/gim,''));

				var text_size = $(this).attr('data-kit').replace('x','x').trim();
                var col_3 = pre_price.split(/[—]/)[1].replace(/[^-0-9]/gim,'')-demping;

                if (discount > 0)
                    col_3 = Math.round(col_3/((100 - discount)/100));

                if (base == 0) {
                    base = col_3;
                }

                diff = col_3 - base;
                $("#tableprice").append('<tr><td>' + text_size + '</td><td>' + col_3 + '</td><td> ' + diff + ' </td></tr>');
                $("#sl").append('<option>' + text_size + ' - ' + col_3 + ' - ' + diff + '</option>');

                size.push(text_size);
                price.push(col_3);
                price_dif.push(diff);

                $("#sizes").val(size);
                $("#prices").val(price);
                $("#prices_dif").val(price_dif);
            });

        })
        .fail(function (data){
            console.log("fail connection");
        });
}

function calculationType5()
{
    var price_text = $("textarea#price_content").val();
    var lines = price_text.split("\n");
    var demping = $("#demping").val();
    var discount = $("#discount").val();

    var size = new Array();
    var price = new Array();
    var price_dif = new Array();

    $.ajax({
        type: 'GET',
        url: admin_egms_ajax_url,
        data: {
            action : 'getUrl',
            ajax : true,
            id_url: price_text,
        }})
        .done(function (data) {

            var base = 0;
            var diff = 0;

            $("#content1").append('<select id="sl"></select>');
            $("#update_price").val("true");

            $(data).find('.style-select option').each(function(i){

                var pre_price = $(this).text();

                var text_size = pre_price.split(/[—-]/)[0];;
                var col_3 = pre_price.split(/[—-]/)[1].replace(/[^-0-9]/gim,'')-demping;

                if (discount > 0)
                    col_3 = Math.round(col_3/((100 - discount)/100));

                if (base == 0) {
                    base = col_3;
                }

                diff = col_3 - base;
                $("#tableprice").append('<tr><td>' + text_size + '</td><td>' + col_3 + '</td><td> ' + diff + ' </td></tr>');
                $("#sl").append('<option>' + text_size + ' - ' + col_3 + ' - ' + diff + '</option>');

                size.push(text_size);
                price.push(col_3);
                price_dif.push(diff);

                $("#sizes").val(size);
                $("#prices").val(price);
                $("#prices_dif").val(price_dif);
            });

        })
        .fail(function (data){
            console.log("fail connection");
        });
}

function calculationType2()
{
    var price_text = $("textarea#price_content").val();
    var lines = price_text.split("\n");
    var demping = $("#demping").val();
    var discount = $("#discount").val();
    if (lines.length > 0) {

        var base = 0;
        var diff = 0;

        $("#content1").append('<select id="sl"></select>');
        var size = new Array();
        var price = new Array();
        var price_dif = new Array();
        $("#update_price").val("true");
        for (i = 0; i < lines.length; i++) {

            var cols = lines[i].split('	');
            var rows_add = cols[0].split(',');
            for(j=0; j<rows_add.length; j++)
                {
                    var col_3 = cols[3]-demping;

                    if (discount > 0)
                        col_3 = Math.round(col_3/((100 - discount)/100));

                    if (base == 0) {
                        base = col_3;
                    }

                    diff = col_3 - base;
                    $("#tableprice").append('<tr><td>' + cols[2] + ' x ' + rows_add[j] + '</td><td>' + col_3 + '</td><td> ' + diff + ' </td></tr>');
                    $("#sl").append('<option>' + cols[2] + 'x' +  rows_add[j] + ' - ' + col_3 + ' - ' + diff + '</option>');

                    size.push(cols[2] + 'x' +  rows_add[j]);
                    price.push(col_3);
                    price_dif.push(diff);

                    $("#sizes").val(size);
                    $("#prices").val(price);
                    $("#prices_dif").val(price_dif);
                }

        }
    }
}

function calculationType1()
{
    var price_text = $("textarea#price_content").val();
    var lines = price_text.split("\n");
    var demping = $("#demping").val();
    var discount = $("#discount").val();
    if (lines.length > 0) {

        var rtmp = new Array(lines.length);

        for (i = 0; i < lines.length; i++) {
            $('#tableprice').append('<tr id="tr' + i + '"></tr>');
            var rows = lines[i].split('	');
            rtmp[i] = new Array(rows.length);

            for (j = 0; j < rows.length; j++) {
                var tmp = (isNaN(parseInt(rows[j])) ) ? parseInt(0) : parseInt(rows[j]);
                rtmp[i][j] = tmp;
                $("#tr" + i).append('<td>' + rows[j] + '</td>');
            }
        }


        $("#content1").append('<select id="sl"></select>');
        var size = new Array();
        var price = new Array();
        var price_dif = new Array();
        $("#update_price").val("true");

        var base = 0;
        var diff = 0;
        for (j = 1; j < rtmp[0].length; j++) {
            for (i = 1; i < rtmp.length; i++) {
                var col_3 = rtmp[i][j];
                if (rtmp[i][j] == 0)
                    continue;
                if (rtmp[i][j] == undefined)
                    continue;

                if (discount > 0)
                    col_3 = Math.round(col_3/((100 - discount)/100));

                col_3 = col_3 - demping;

                if (base == 0) {
                    base = col_3;
                }

                diff = col_3 - base;

                //if (discount > 0)
                 //   col_3 = Math.round(col_3/((100 - discount)/100));

                $("#sl").append('<option>' + rtmp[0][j] + 'x' + rtmp[i][0] + ' - ' + col_3 + ' - ' + diff + '</option>');

                size.push(rtmp[0][j] + 'x' + rtmp[i][0]);
                price.push(col_3);
                price_dif.push(diff);

                $("#sizes").val(size);
                $("#prices").val(price);
                $("#prices_dif").val(price_dif);
            }
        }
    }
}
</script>

<div id="product-updateprice" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="updateprice" />
	<h3>{l s='Update price'}</h3>
	

	
	<div class="form-group">
		<div class="col-lg-1">
			<label class="control-label col-lg-3" for="text_fields">
				<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Discount'}">
					{l s='Discount'}
				</span>
			</label>
		</div>	
		<div class="col-lg-1">
			<input type="text" name="discount" id="discount" value="{$procent}" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-1">
			<label class="control-label col-lg-3" for="text_fields">
				<span class="label-tooltip" data-toggle="tooltip"
					  title="{l s='Demping'}">
					{l s='Demping'}
				</span>
			</label>
		</div>
		<div class="col-lg-1">
			<input type="text" name="demping" id="demping" value="{$demping}" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-1">
			<select id="ctype" name="ctype">
				<option value="7" {if $ctype == 0}selected="selected"{/if} > - </option>
				<option value="1" {if $ctype == 1}selected="selected"{/if} > OPMATEK - 1 </option>
				<option value="2" {if $ctype == 2}selected="selected"{/if}> OPMATEK - 2 </option>
				<option value="3" {if $ctype == 3}selected="selected"{/if}> Dreamline </option>
				<option value="4" {if $ctype == 4}selected="selected"{/if}> matras-ru </option>
				<option value="5" {if $ctype == 5}selected="selected"{/if}> omatras-ru </option>
			</select>
		</div>
	</div>


	<div class="form-group">
		<div class="col-lg-1">
			<select id="attrr_group_add" name="attrr_group_add">
				<option value="7" {if $id_attr_group1 == 0}selected="selected"{/if} > - </option>
				<option value="7" {if $id_attr_group1 == 7}selected="selected"{/if} >color</option>
				<option value="10" {if $id_attr_group1 == 10}selected="selected"{/if} >chehol</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-12">
			<input type="hidden" id="attrs" name="attrs">
			<fieldset style="border:none;">
				<textarea name="attr_content" id="attr_content" rows="10" cols="15">{$attr_content}</textarea>
			</fieldset>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-1">
			<select id="attrr_group" name="attrr_group">
				<option value="1" {if $id_attr_group == 1}selected="selected"{/if} >matrass</option>
				<option value="4" {if $id_attr_group == 4}selected="selected"{/if} >pillows</option>
				<option value="5" {if $id_attr_group == 5}selected="selected"{/if} >bedsize</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-12">
			<input type="hidden" id="sizes" name="sizes">
			<input type="hidden" id="prices" name="prices">
			<input type="hidden" id="prices_dif" name="prices_dif">						
			<fieldset style="border:none;">
		        <textarea name="price_content" id="price_content" rows="10" cols="45">{$price_content}</textarea>
		    </fieldset>	
	    </div>
	</div>
	
    <div class="form-group">
	    <div class="col-lg-8">
	    	<button class="ladda-button btn btn-primary" data-style="expand-right" type="button" id="result" style="">
				<span class="ladda-label"><i class="icon-check"></i> {l s='Preview'}</span>
			</button>
			
			<button class="ladda-button btn btn-primary" data-style="expand-right" type="button" id="clear" style="">
				<span class="ladda-label"><i class="icon-cancel"></i> {l s='Clear'}</span>
			</button>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-lg-8">
			<input type="hidden" name="update_price" id="update_price" value="false" />
			<div id="content1"></div>
			<div id="content2"></div>
	    </div>
	    
	</div>	
    
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>