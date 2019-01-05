jQuery( document ).ready( function ( event ) {
    jQuery( "#toplevel_page_wbcomplugins .wp-submenu li" ).each( function () {
        var link = jQuery( this ).find( 'a' ).attr( 'href' );
        if ( link == 'admin.php?page=wbcom-plugins-page' || link == 'admin.php?page=wbcom-themes-page' || link == 'admin.php?page=wbcom-support-page' ) {
            jQuery( this ).addClass( 'hidden' );
        }
    } );

    jQuery( document ).off('click', '.activation_button_wrap .wbcom-plugin-action-button').on( 'click', '.activation_button_wrap .wbcom-plugin-action-button', function ( event ) {
        event.preventDefault();
        event.stopPropagation(); 
        var thisRef = jQuery( this );
        var action  = thisRef.parent('.activation_button_wrap').siblings( 'input.plugin-action' ).val()
        thisRef.find('.fa-spinner').show();
        jQuery.ajax( {
            url: wbcom_plugin_installer_params.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'wbcom_manage_plugin_installation',
                plugin_action: action,
                plugin_slug: thisRef.parent('.activation_button_wrap').siblings( 'input.plugin-slug' ).val()
            },            
            complete: function() {
                if( 'install_plugin' == action ) {
                    thisRef.parent('.activation_button_wrap').siblings( '.plugin-action' ).val( 'activate_plugin' );
                    thisRef.html('<i class="fas fa-toggle-off"></i>'+ wbcom_plugin_installer_params.activate_text + '<i class="fas fa-spinner fa-pulse" style="display:none"></i>' );
                } else if( 'activate_plugin' == action ) {
                    thisRef.parent('.activation_button_wrap').siblings( '.plugin-action' ).val( 'deactivate_plugin' );
                    thisRef.html('<i class="fas fa-toggle-on"></i>'+ wbcom_plugin_installer_params.deactivate_text + '<i class="fas fa-spinner fa-pulse" style="display:none"></i>' );
                } else {
                    thisRef.parent('.activation_button_wrap').siblings( '.plugin-action' ).val( 'activate_plugin' );
                    thisRef.html('<i class="fas fa-toggle-off"></i>'+ wbcom_plugin_installer_params.activate_text + '<i class="fas fa-spinner fa-pulse" style="display:none"></i>' );
                }
            }
        } );
    } );

    //Admin Header Animation Effect
    var ink, d, x, y;
    jQuery( '#wb_admin_header #wb_admin_nav ul li' ).on( "click", function ( e ) {
        var $this = jQuery( this );

        jQuery( this ).addClass( 'wbcom_btn_material' );
        setTimeout( function () {
            $this.removeClass( 'wbcom_btn_material' )
        }, 650 );

        if ( jQuery( this ).find( ".wbcom_material" ).length === 0 ) {
            jQuery( this ).prepend( '<span class="wbcom_material"></span>' );
        }
        ink = jQuery( this ).find( ".wbcom_material" );
        ink.removeClass( "is-animated" );
        if ( !ink.height() && !ink.width() ) {
            d = Math.max( jQuery( this ).outerWidth(), jQuery( this ).outerHeight() );
            ink.css( { height: d, width: d } );
        }
        x = e.pageX - jQuery( this ).offset().left - ink.width() / 2;
        y = e.pageY - jQuery( this ).offset().top - ink.height() / 2;
        ink.css( { top: y + 'px', left: x + 'px' } ).addClass( "is-animated" );
    } );

} );