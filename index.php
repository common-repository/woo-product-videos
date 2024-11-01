<?php
/*
Plugin Name: WPC Product Videos for WooCommerce
Plugin URI: https://wpclever.net/
Description: WPC Product Videos helps you add many videos for a product and linked to the feature image or product gallery images.
Version: 1.0.6
Author: WPClever.net
Author URI: https://wpclever.net
Text Domain: woopv
Domain Path: /languages/
Requires at least: 4.0
Tested up to: 5.3.2
WC requires at least: 3.0
WC tested up to: 3.9.2
*/

defined( 'ABSPATH' ) || exit;

! defined( 'WOOPV_VERSION' ) && define( 'WOOPV_VERSION', '1.0.6' );
! defined( 'WOOPV_URI' ) && define( 'WOOPV_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOPV_REVIEWS' ) && define( 'WOOPV_REVIEWS', 'https://wordpress.org/support/plugin/woo-product-videos/reviews/?filter=5' );
! defined( 'WOOPV_CHANGELOG' ) && define( 'WOOPV_CHANGELOG', 'https://wordpress.org/plugins/woo-product-videos/#developers' );
! defined( 'WOOPV_DISCUSSION' ) && define( 'WOOPV_DISCUSSION', 'https://wordpress.org/support/plugin/woo-product-videos' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOPV_URI );

include 'includes/wpc-menu.php';
include 'includes/wpc-dashboard.php';

if ( ! function_exists( 'woopv_init' ) ) {
	add_action( 'plugins_loaded', 'woopv_init', 11 );

	function woopv_init() {
		// load text-domain
		load_plugin_textdomain( 'woopv', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0.0', '>=' ) ) {
			add_action( 'admin_notices', 'woopv_notice_wc' );

			return;
		}

		if ( ! class_exists( 'WPCleverWoopv' ) ) {
			class WPCleverWoopv {
				function __construct() {
					// Enqueue scripts
					add_action( 'wp_enqueue_scripts', array( $this, 'woopv_wp_enqueue_scripts' ) );

					// settings page
					add_action( 'admin_menu', array( $this, 'woopv_admin_menu' ) );

					// settings link
					add_filter( 'plugin_action_links', array( $this, 'woopv_action_links' ), 10, 2 );
					add_filter( 'plugin_row_meta', array( $this, 'woopv_row_meta' ), 10, 2 );

					// Meta data
					add_filter( 'attachment_fields_to_edit', array( $this, 'woopv_attachment_field_video' ), 10, 2 );
					add_filter( 'attachment_fields_to_save', array(
						$this,
						'woopv_attachment_field_video_save'
					), 10, 2 );

					// Show videos
					add_filter( 'woocommerce_single_product_image_thumbnail_html', array(
						$this,
						'woopv_single_product_image_thumbnail_html'
					), 10, 2 );
				}

				function woopv_wp_enqueue_scripts() {
					// light gallery
					wp_enqueue_script( 'lightgallery', WOOPV_URI . 'assets/libs/lightgallery/js/lightgallery-all.min.js', array( 'jquery' ), WOOPV_VERSION, true );
					wp_enqueue_style( 'lightgallery', WOOPV_URI . 'assets/libs/lightgallery/css/lightgallery.min.css' );

					// feather
					wp_enqueue_style( 'woopv-feather', WOOPV_URI . 'assets/libs/feather/feather.css' );

					// main
					wp_enqueue_style( 'woopv-frontend', WOOPV_URI . 'assets/css/frontend.css' );
					wp_enqueue_script( 'woopv-frontend', WOOPV_URI . 'assets/js/frontend.js', array( 'jquery' ), WOOPV_VERSION, true );
				}

				function woopv_admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Product Videos', 'woopv' ), esc_html__( 'Product Videos', 'woopv' ), 'manage_options', 'wpclever-woopv', array(
						$this,
						'woopv_settings_page'
					) );
				}

				function woopv_settings_page() {
					$page_slug  = 'wpclever-woopv';
					$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'how';
					?>
                    <div class="wpclever_settings_page wrap">
                        <h1 class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Product Videos', 'woopv' ) . ' ' . WOOPV_VERSION; ?></h1>
                        <div class="wpclever_settings_page_desc about-text">
                            <p>
								<?php printf( esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'woopv' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
                                <br/>
                                <a href="<?php echo esc_url( WOOPV_REVIEWS ); ?>"
                                   target="_blank"><?php esc_html_e( 'Reviews', 'woopv' ); ?></a> | <a
                                        href="<?php echo esc_url( WOOPV_CHANGELOG ); ?>"
                                        target="_blank"><?php esc_html_e( 'Changelog', 'woopv' ); ?></a>
                                | <a href="<?php echo esc_url( WOOPV_DISCUSSION ); ?>"
                                     target="_blank"><?php esc_html_e( 'Discussion', 'woopv' ); ?></a>
                            </p>
                        </div>
                        <div class="wpclever_settings_page_nav">
                            <h2 class="nav-tab-wrapper">
                                <a href="?page=<?php echo $page_slug; ?>&amp;tab=settings"
                                   class="nav-tab <?php echo $active_tab === 'how' ? 'nav-tab-active' : ''; ?>">
									<?php esc_html_e( 'How to use?', 'woopv' ); ?>
                                </a>
                                <a href="https://wpclever.net/support?utm_source=support&utm_medium=woopv&utm_campaign=wporg"
                                   class="nav-tab" target="_blank">
									<?php esc_html_e( 'Premium Support', 'woopv' ); ?>
                                </a>
                            </h2>
                        </div>
                        <div class="wpclever_settings_page_content">
							<?php if ( $active_tab === 'how' ) { ?>
                                <div class="wpclever_settings_page_content_text">
                                    <p>
										<?php esc_html_e( 'When set product image or add product gallery images you can add the video URL for each image. You also can do it when editing an image via Media Library.', 'woopv' ); ?>
                                    </p>
                                    <p><img src="<?php echo WOOPV_URI; ?>assets/images/how-01.jpg"/></p>
                                    <p>
										<?php esc_html_e( 'After that, the video will be linked to the image on the product page.', 'woopv' ); ?>
                                    </p>
                                    <p><img src="<?php echo WOOPV_URI; ?>assets/images/how-02.jpg"/></p>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}

				function woopv_action_links( $links, $file ) {
					static $plugin;
					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}
					if ( $plugin === $file ) {
						$settings_link = '<a href="' . admin_url( 'admin.php?page=wpclever-woopv&tab=how' ) . '">' . esc_html__( 'How to use?', 'woopv' ) . '</a>';
						array_unshift( $links, $settings_link );
					}

					return (array) $links;
				}

				function woopv_row_meta( $links, $file ) {
					static $plugin;
					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}
					if ( $plugin === $file ) {
						$row_meta = array(
							'support' => '<a href="https://wpclever.net/support?utm_source=support&utm_medium=woopv&utm_campaign=wporg" target="_blank">' . esc_html__( 'Premium support', 'woopv' ) . '</a>',
						);

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function woopv_attachment_field_video( $form_fields, $post ) {
					$form_fields['woopv-video-url'] = array(
						'label' => esc_html__( 'Video', 'woopv' ),
						'input' => 'text',
						'value' => get_post_meta( $post->ID, 'woopv_video_url', true ),
						'helps' => esc_html__( 'Add Youtube/Vimeo URL', 'woopv' )
					);

					return $form_fields;
				}

				function woopv_attachment_field_video_save( $post, $attachment ) {
					if ( isset( $attachment['woopv-video-url'] ) ) {
						update_post_meta( $post['ID'], 'woopv_video_url', esc_url( $attachment['woopv-video-url'] ) );
					}

					return $post;
				}

				function woopv_single_product_image_thumbnail_html( $html, $attachment_id ) {
					$thumbnail_src = wp_get_attachment_image_src( $attachment_id, 'woocommerce_gallery_thumbnail_size' );
					if ( $video = get_post_meta( $attachment_id, 'woopv_video_url', true ) ) {
						$html = str_replace( '</div>', '<span class="woopv-btn woopv-btn-video" data-src="' . esc_url( $video ) . '"><img src="' . esc_url( $thumbnail_src[0] ) . '"/></span></div>', $html );
					} else {
						$html = str_replace( '</div>', '<span class="woopv-btn woopv-btn-image" data-src="' . wp_get_attachment_url( $attachment_id ) . '"><img src="' . esc_url( $thumbnail_src[0] ) . '"/></span></div>', $html );
					}

					return strip_tags( $html, '<div><img><span>' );
				}
			}

			new WPCleverWoopv();
		}
	}

	function woopv_notice_wc() {
		?>
        <div class="error">
            <p><?php esc_html_e( 'WPC Product Videos require WooCommerce version 3.0.0 or greater.', 'woopv' ); ?></p>
        </div>
		<?php
	}
}