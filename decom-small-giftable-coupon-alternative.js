

jQuery(document.body).on('updated_cart_totals', function () {

    if (jQuery('#dgfw-choose-gift').length) {
        jQuery.ajax({
            url : decomsgcapost.ajax_url,
            type : 'post',
            data : {
                action : 'post_decom_sgca'
            },
            success : function( response ) {
                if ( ! response || response.error )
                    return;
                var lets_hide_giftable = response.lets_hide_giftable;
                if ( lets_hide_giftable ) {
                    jQuery('body').addClass('remove-giftable-slider');
                    jQuery('#dgfw-choose-gift').parent().remove();
                }

            }
        });
    }

});



jQuery(window).on("load", function () {
    if ( jQuery('#dgfw-gifts-carousel').length) {

    }
});