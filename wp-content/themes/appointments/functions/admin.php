<?php
/**
 * Init theme appointments
 */
if ( ! function_exists( 'appointments_init' ) ) {
    function appointments_init(){
        ob_start();
        // register header nav menu location
        register_nav_menus( array(
            'header-menu' => 'Header Menu',
        ) );
        add_theme_support("post-thumbnails");
        add_theme_support('menus');
        add_post_type_support('page', 'excerpt');
        add_theme_support( 'title-tag' );
        add_theme_support( 'custom-logo', array(
            'height'      => 50,
            'width'       => 200,
            'flex-height' => true,
            'flex-width'  => true,
            'header-text' => array( 'site-title', 'site-description' ),
        ) );
    }
}
add_action('init', 'appointments_init');

/*===============================================Modify Menu Wordpress=============================================*/
function remove_menus() {
    global $menu, $submenu;
    remove_menu_page('tools.php');
}
add_action( 'admin_menu', 'remove_menus', 999 );

function debug_admin_menu() {
    echo '<pre>' . print_r( $GLOBALS[ 'menu' ], TRUE) . '</pre>';
}
//add_action( 'admin_init', 'debug_admin_menu' );
add_action( 'wp_before_admin_bar_render', function() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
} );

/*===============================================Wordpress Image=============================================*/
function wpb_imagelink_setup() {
    $image_set = get_option( 'image_default_link_type' );
    if ($image_set !== 'none') {
        update_option('image_default_link_type', 'none');
    }
}


function themes_widgets_collection($folders){
    $folders[] = THEME_URL.'/widgets/';
    return $folders;
}

add_filter('siteorigin_widgets_widget_folders', 'themes_widgets_collection');

/* Custom script in addmin */
function my_enqueue($hook) {
    if ('plugins.php' !== $hook) {
        return;
    }
}

add_action('admin_enqueue_scripts', 'my_enqueue');