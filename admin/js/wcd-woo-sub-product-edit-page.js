(function( $ ) {
	'use strict';
    // @credit taken refernce from "wpswings" plugin
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
     $(document).ready(function() {

        function wcd_woo_hide_show_sub_expiry_setting(){
        $('input[type="radio"]').click(function(){
            var val = $(this).attr("value");
            console.log(val);

            if(val == 0){
                $(document).find('.wcd_sub_expiry').hide();
            } else {
                $(document).find('.wcd_sub_expiry').show();
            }
          });
        }

        wcd_woo_hide_show_sub_expiry_setting();


     function wcd_woo_hide_show_sub_setting_tab(){
        if( $('#_wcd_woo_product_is_sub').prop('checked') ) {
            $(document).find('.wcd_woo_product_is_sub_options').show();
            $(document).find('.wcd_woo_product_is_sub_options').removeClass('active');
        }
        else{
         $(document).find('.wcd_woo_product_is_sub_options').hide();
         $(document).find('#wcd_woo_product_is_sub_target_section').hide();
         $(document).find('.general_tab').addClass('active');
         $(document).find('#general_product_data').show();
         
        }
    }

    // $('.wcd_sub_expiry').hide();

    wcd_woo_hide_show_sub_setting_tab();
    $('#_wcd_woo_product_is_sub').on('change', function(){
        wcd_woo_hide_show_sub_setting_tab();
    });

            // Product type specific options.
            $( 'select#product-type' ).change( function() {

                var select_val = $( this ).val();
               
                if ( 'variable' === select_val ) {
                    $( 'input#_wcd_woo_product_is_sub' ).prop( 'checked', false );
                    wcd_woo_hide_show_sub_setting_tab();
                } else if ( 'grouped' === select_val ) {
                    $( 'input#_wcd_woo_product_is_sub' ).prop( 'checked', false );
                    wcd_woo_hide_show_sub_setting_tab();
                } else if ( 'external' === select_val ) {
                    $( 'input#_wcd_woo_product_is_sub' ).prop( 'checked', false );
                    wcd_woo_hide_show_sub_setting_tab();
                }
            });
        });

        
 })( jQuery );