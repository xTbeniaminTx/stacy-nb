<?php

// Global variables define
define('STACY_NB_PARENT_TEMPLATE_DIR_URI',get_template_directory_uri());
define('STACY_NB_ST_TEMPLATE_DIR_URI',get_stylesheet_directory_uri());
define('STACY_NB_ST_TEMPLATE_DIR',get_stylesheet_directory());

if (!function_exists('wp_body_open')) {

    function wp_body_open() {
        /**
         * Triggered after the opening <body> tag.
         */
        do_action('wp_body_open');
    }

}

require( STACY_NB_ST_TEMPLATE_DIR . '/css/custom-css.php');

add_action('wp_enqueue_scripts', 'stacy_nb_theme_css', 999);
function stacy_nb_theme_css() {

	if(get_theme_mod('custom_color_enable') == false)
	{
		wp_enqueue_style('stacy-nb-default-style-css', STACY_NB_ST_TEMPLATE_DIR_URI."/css/default.css" );
	}else{
            stacy_nb_custom_light();
        }

        wp_enqueue_style( 'stacy-nb-parent-style', STACY_NB_PARENT_TEMPLATE_DIR_URI . '/style.css' );
        wp_enqueue_style( 'stacy-nb-child-style', STACY_NB_ST_TEMPLATE_DIR_URI . '/style.css', array( 'stacy-nb-parent-style' ) );

	wp_enqueue_style('stacy-nb-media-responsive-css', STACY_NB_ST_TEMPLATE_DIR_URI."/css/media-responsive.css" );
	wp_dequeue_style('stacy-nb-default-css', STACY_NB_PARENT_TEMPLATE_DIR_URI .'/css/default.css');
}


if ( ! function_exists( 'stacy_nb_theme_setup' ) ) :

function stacy_nb_theme_setup() {

//Load text domain for translation-ready
load_theme_textdomain('stacy-nb', STACY_NB_ST_TEMPLATE_DIR . '/languages');

if ( is_admin() ) {
	require STACY_NB_ST_TEMPLATE_DIR . '/admin/admin-init.php';
}

// Add default posts and comments RSS feed links to head.

add_theme_support( 'automatic-feed-links' );

/* Let WordPress manage the document title. */

add_theme_support( 'title-tag' );

}
endif;
add_action( 'after_setup_theme', 'stacy_nb_theme_setup' );

/**
 * Import options from SpicePress
 *
 */
function stacy_nb_get_lite_options() {
	$spicepress_mods = get_option( 'theme_mods_spicepress' );
	if ( ! empty( $spicepress_mods ) ) {
		foreach ( $spicepress_mods as $spicepress_mod_k => $spicepress_mod_v ) {
			set_theme_mod( $spicepress_mod_k, $spicepress_mod_v );
		}
	}
}
add_action( 'after_switch_theme', 'stacy_nb_get_lite_options' );

add_action( 'admin_init', 'stacy_nb_detect_button' );
	function stacy_nb_detect_button() {
	wp_enqueue_style('stacy-nb-info-button', STACY_NB_ST_TEMPLATE_DIR_URI .'/css/import-button.css');
}

if (!get_option('stacy_nb_user', false)) {
     //detect old user and set value
            if (get_theme_mod('home_news_section_title',false) ||
            get_theme_mod('home_news_section_discription',false) ||
            get_theme_mod('home_service_section_title',false) ||
            get_theme_mod('home_service_section_discription',false)) {
                add_option('stacy_nb_user', 'old');
            }else{
                add_option('stacy_nb_user', 'new');
            }
}

require_once STACY_NB_ST_TEMPLATE_DIR. '/functions/customizer/custom-controls.php';
require_once STACY_NB_ST_TEMPLATE_DIR. '/functions/customizer/customizer_header_layout_settings.php';
require_once STACY_NB_ST_TEMPLATE_DIR. '/functions/customizer/customizer_blog_layout_settings.php';
require_once STACY_NB_ST_TEMPLATE_DIR. '/functions/template-functions.php';

if (!function_exists('stacy_nb_image_radio_button_sanitization')) {

    function stacy_nb_image_radio_button_sanitization($input, $setting) {
        //get the list of possible radio box or select options
        $choices = $setting->manager->get_control($setting->id)->choices;

        if (array_key_exists($input, $choices)) {
            return $input;
        } else {
            return $setting->default;
        }
    }

}

//Set for old user before 1.3.7
if (!get_option('stacy_nb_user_with_1_3_7', false)) {
    //detect old user and set value
    $stacy_nb_service_title=get_theme_mod('home_service_section_title');
    $stacy_nb_service_discription=get_theme_mod('home_service_section_discription');
    $stacy_nb_blog_title=get_theme_mod('home_news_section_title');
    $stacy_nb_blog_discription=get_theme_mod('home_news_section_discription');
    $stacy_nb_slider_title=get_theme_mod('home_slider_title');
    $stacy_nb_slider_discription=get_theme_mod('home_slider_discription');
    $stacy_nb_testimonial_title=get_theme_mod('home_testimonial_section_title');
    $stacy_nb_testimonial__discription=get_theme_mod('home_testimonial_section_discription');
    $stacy_nb_footer_credit=get_theme_mod('footer_copyright_text');

    if ($stacy_nb_service_title !=null || $stacy_nb_service_discription !=null || $stacy_nb_blog_title !=null || $stacy_nb_blog_discription !=null || $stacy_nb_slider_title !=null || $stacy_nb_slider_discription !=null || $stacy_nb_testimonial_title !=null || $stacy_nb_testimonial__discription !=null || $stacy_nb_footer_credit !=null )  {
        add_option('stacy_nb_user_with_1_3_7', 'old');

    } else {
        add_option('stacy_nb_user_with_1_3_7', 'new');
    }
}

//Remove Footer section
function stacy_nb_remove_customize_register( $wp_customize ) {

   $wp_customize->remove_section( 'spicepress_footer_copyright');

}
add_action( 'customize_register', 'stacy_nb_remove_customize_register',11);
