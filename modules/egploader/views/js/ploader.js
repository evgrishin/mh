function download(action, action_type, id_load, controller, ajax_url, currentIndex)
{
    var product_meta_load = $('#product_meta_load').is(":checked")?1:0;
    var product_name_load = $('#product_name_load').is(":checked")?1:0;
    var description_load = $('#description_load').is(":checked")?1:0;
    var price_load = $('#price_load').is(":checked")?1:0;
    var images_load = $('#images_load').is(":checked")?1:0;
    var features_load = $('#features_load').is(":checked")?1:0;
    var consistions_load = $('#consistions_load').is(":checked")?1:0;
    var rewiews_load = $('#rewiews_load').is(":checked")?1:0;

    var no_cache = $('#no_cache').is(":checked")?1:0;

    $.ajax({
        type: 'GET',
        url: ajax_url,
        data: {
            controller : controller, //'AdminEGPLOADER',
            action : action,
            action_type: action_type,
            ajax : true,
            id_load: id_load,
            product_meta_load: product_meta_load,
            product_name_load: product_name_load,
            description_load: description_load,
            price_load: price_load,
            images_load: images_load,
            features_load: features_load,
            consistions_load: consistions_load,
            rewiews_load: rewiews_load
        },
        success: function(data)
        {
            $("#message_display").append(currentIndex + "; " + data);
        },
        error: function(http){
            $("#message_display").append(http.responseText);
        },
    });

}

function download_connecter(action, id_connecter, currentIndex)
{

    $.ajax({
        type: 'GET',
        url: admin_pconnecter_ajax_url,
        data: {
            controller : 'AdminEGPCONNECTER',
            action : 'download',
            ajax : true,
            id_connecter: id_connecter,
            action_type: action

        },
        success: function(data)
        {
            $("#message_display").append(data);
        },
        error: function(http){
            $("#message_display").append(http.responseText);
        },
    });

}
