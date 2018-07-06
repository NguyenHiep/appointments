<?php
/**
 * Init theme bds
 */
if ( ! function_exists( 'bds_init' ) ) {
    function bds_init(){
        ob_start();
        // register header nav menu location
        register_nav_menus( array(
            'header-menu' => 'Header Menu',
           /* 'footer-menu' => 'Footer Menu',*/
        ) );
        add_theme_support("post-thumbnails");
        add_theme_support('menus');
        add_post_type_support('page', 'excerpt');
        add_theme_support( 'post-formats', array(
            'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
        ) );
        add_theme_support( 'title-tag' );
    }
}
add_action('init', 'bds_init');

/*===============================================Modify Menu Wordpress=============================================*/
function remove_menus() {
    global $menu, $submenu;

    //$menu[21] = $menu[5];
    //Move Contact Form DB to below Contact CPT
   /* if (array_key_exists(80.025, $menu) and array_key_exists(100, $menu)) {
        $menu[28] = $menu[100];
        unset($menu[100]);
    }*/
    //unset($menu['80.025']);
    //unset($menu['100']);
    //unset($menu['102']);
    //remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
    //remove_menu_page('edit-comments.php');
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
    /*$wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('wpseo-menu');*/
} );

/*===============================================Wordpress Image=============================================*/
function wpb_imagelink_setup() {
    $image_set = get_option( 'image_default_link_type' );
    if ($image_set !== 'none') {
        update_option('image_default_link_type', 'none');
    }
}

/*===============================================Admin Columns=============================================*/

add_action( 'redux/loaded', 'remove_demo' );

/**
 * Removes the demo link and the notice of integrated demo from the redux-framework plugin
 */
if ( ! function_exists( 'remove_demo' ) ) {
    function remove_demo() {
        // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
        if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
            remove_filter( 'plugin_row_meta', array(
                ReduxFrameworkPlugin::instance(),
                'plugin_metalinks'
            ), null, 2 );
            // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
            remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
        }
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