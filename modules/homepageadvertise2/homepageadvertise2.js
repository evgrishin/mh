/*
 * Copyright 2010 Mathieu Civel
 */
 
(function($) {
	
var modulepath = '../modules/homepageadvertise2/';
	
$(document).ready(function() {
    var $table = $('#table_images'),
        originalOrder;

    //table drag n drop
	$table.tableDnD({
		onDragStart: function(table, row) {
			originalOrder = $.tableDnD.serialize();
			$('#' + row.id).parent('tr').addClass('myDragClass');
		},
		onDrop: function(table, row) {
			if (originalOrder != $.tableDnD.serialize()) {                
				redrawTable($table);
				updateData($table);
			}
		},
		dragHandle: 'dragHandle',
		onDragClass: 'myDragClass'
	});
			
    //table arrow clicks
    $table.find('td.dragHandle a').click(arrowClickCallback($table));
	
	//table changes 
	$table.find('input, select')
	    .change(inputChangeCallback($table))
        .keydown(function(e) {
            if (e.keyCode == '13') {
                e.preventDefault();
                $(this).change();
            }
        });
	
	//upload image
	new AjaxUpload('uploadImage', {
        action          : modulepath + 'uploadimage.php',
        name            : 'userfile',
        responseType    : 'text/html',
        autoSubmit      : true,
        onSubmit        : function(file, ext) {
            if (! (ext && /^(jpe?g|png|gif)$/i.test(ext))) {
                alert('Error: invalid file extension');
                return false;
            }
            $('#loading_gif').show();
        },
        onComplete      : function(file, response) {
            if (response.substr(0, 7) === 'success') {
                appendRow($table, response.substr(8));
            }
            else {
                alert('Error: can\'t upload image (' + response + ')');
            }
            $('#loading_gif').hide();
        }
    });

    //delete image
    $table.find('td.delete_image img').click(deleteImgClickCallback($table));
	
	
	//updateData($table);
});

function arrowClickCallback($table) {
	return function(e) {
    	var $this = $(this),
    	    $row = $this.parent().parent();
    	    
        e.preventDefault();
    	
    	if ($this.hasClass('move-up')) {
            $row.insertBefore($row.prev('tr'));
    	}
    	else if ($this.hasClass('move-down')) {
            $row.insertAfter($row.next('tr'));
    	}
    	
    	redrawTable($table);
    	updateData($table);
    }
}

function inputChangeCallback($table) {
    return function() { 
        updateData($table);
    }
}

function deleteImgClickCallback($table) {
    return function() { 
        var $tr =  $(this).parents('tr').eq(0);
        
        $.get(modulepath + 'deleteimage.php', {image : $tr.find('td.imagename').text()} );
        
        $tr.remove();
    
        redrawTable($table);
        updateData($table);
    }
}


function appendRow(table, image) {
	var pos = table.find('tr').length;
	
	$('#hidden-row tr').clone()
        .attr('id', 'tr_image_' + pos)
        .children('td.positions').text(pos).end()
        .children('td.dragHandle').attr('id', 'td_image_' + pos).end()
        .children('td.imagename').text(image).end()
        .children('td.image_prev').find('img').each(function() { $(this).attr("src", $(this).attr("src") + image); }).end().parent()
        .find('td.imagelink input').attr('name', 'image_link_' + pos).end()
        .find('td.checkbox_image_enabled input').attr('name', 'image_enable_' + pos).end()
	    
	    .appendTo(table)
	   
	    //no live/delegate in jQuery 1.2.6
        .find('td.dragHandle a').click(arrowClickCallback(table)).end()
        .find('td.delete_image img').click(deleteImgClickCallback(table)).end()
        .find('input, select').change(inputChangeCallback(table));
	   
	table.tableDnDUpdate();   
	   
	redrawTable(table);
	updateData(table);
}

function redrawTable(table) {
	table.find('tr').removeClass('alt_row');
	table.find('tr:even').addClass('alt_row');
	table.find('td.dragHandle a:hidden').show();
	table.find('td.dragHandle:first a:even, td.dragHandle:last a:odd').hide();
	table.find('td.positions').each(function(i) {
		$(this).html(i + 1);
	});
}

//save table data in hidden inputs 
function updateData(table) {
	var orderdata = '',
		linkdata = '',
		langdata = '';
		currdata = '';
		
	table.find('td.positions').each(function(i) {
        var $this = $(this);
		if ($this.nextAll('.checkbox_image_enabled').children().is(':checked')) {
			orderdata += $this.nextAll('.imagename').text() + ';';
			linkdata += $this.nextAll('.imagelink').children().val() + ';';
			langdata += $this.nextAll('.imagelang').children().val() + ';';
			currdata += $this.nextAll('.imagecurr').children().val() + ';';
		}
	});
	
	$('#hidden_image_data').val(orderdata);
	$('#hidden_link_data').val(linkdata);
	$('#hidden_lang_data').val(langdata);
	$('#hidden_curr_data').val(currdata);
}

function startsWith($string, $start) {
	return substr($string, 0, strlen($start)) === $start;
}
	
})(jQuery);










