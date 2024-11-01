jQuery( function( $ ) {
	'use strict';
	(
		function() {
			if ( $( '.woopv-btn' ).length > 0 ) {
				$( '.woocommerce-product-gallery' ).lightGallery( {
					selector: '.woopv-btn'
				} );

				var woopv_img = 0;
				$( '.woocommerce-product-gallery .woocommerce-product-gallery__image' ).each( function() {
					if ( $( this ).find( '.woopv-btn-video' ).length ) {
						var woopv_btn = $( this ).find( '.woopv-btn-video' ).clone();
						console.log( woopv_btn );
						$( '.flex-control-thumbs li' ).eq( woopv_img ).addClass( 'woopv-thumb-video' ).append( woopv_btn );
					}
					woopv_img ++;
				} );
			}
		}()
	);
} );