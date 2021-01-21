var delay = 15000
var ldelay;

jQuery(document).ready(function() {

	LoadTable();
	
	$("#update").click(function(){
		
		ldelay = delay
		$('tbody tr').each(function() {
			var el = this;
			window.setTimeout(function () {
				UpdateProductInfo(el);
			},ldelay+=delay);
			}
		);
		
	});
	
});



function UpdateRow(elem)
{
	var e = $(elem).parent().parent().get(0); 
	UpdateProductInfo(e);
}

function UpdateProductInfo(row){
	
	var indicator = row.cells.item(0);
	var id_page = row.cells.item(1).innerText;	
	var url = row.cells.item(2).innerText;
	var product_name = row.cells.item(3);
	var product_name_db = row.cells.item(4);

	$.ajax({
		 type : "GET",
		 url : admin_ormprod_ajax_url,
		 data: {
				controller : 'AdminAjax',
				action : 'updateProductInfo',
				id_page: id_page,
				uc: $('#uc').attr("checked") ? true : false,
				up: $('#up').attr("checked") ? true : false,
				comment: $('#comment').attr("checked") ? true : false,
				upname: $('#upname').attr("checked") ? true : false,
				ajax : true
		 },
		 success : function(data) 
		 {
			 $(indicator).html('<i class="icon-ok">ok</i>');
		 },
		 error: function(){
			 $(indicator).html('<i class="icon-info-sign">error!</i>');
		 },
		 beforeSend: function(){
			 $(indicator).html('<i class="icon-ok">...</i>');
		 },
	
	});
	
}

function InsertRow(elem)
{
	
	var e = $(elem).parent().parent().get(0);
	var indicator = e.cells.item(0);
	var id_page = e.cells.item(1).innerText;
	var url_page=prompt('url of page:','http://');
	var attrgroup=prompt('input type product:','matrassize');
	//alert(id_page);
	$.ajax({
		 type : "GET",
		 url : admin_ormprod_ajax_url,
		 data: {
				controller : 'AdminAjax',
				action : 'insertProductInfo',
				id_page: id_page,
				attrgroup: attrgroup,
				url_page: url_page,
				ajax : true
		 },
		 success : function(data) 
		 {
			 $(indicator).html('<i class="icon-ok">ok</i>');
		 },
		 error: function(){
			 $(indicator).html('<i class="icon-info-sign">error!</i>');
		 },
		 beforeSend: function(){
			 $(indicator).html('<i class="icon-ok">...</i>');
		 },
	
	});
}

function LoadTable()
{

	$.ajax({
		type: 'GET',
		url: admin_ormprod_ajax_url,
		data: {
			controller : 'AdminAjax',
			action : 'getTable',
			ajax : true
		},
		success: function(data)
		{
			$("#main_table").children("tbody").html(data);;
		},
		error: function(){
			alert("error1");
		 },
	});
	
}