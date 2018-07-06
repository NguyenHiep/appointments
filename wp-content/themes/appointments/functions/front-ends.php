<?php

// Register Custom Navigation Walker
require_once dirname( __FILE__ ) . '/wp_bootstrap_pagination.php';

set_post_thumbnail_size( 250, 250 );

/*==================================================Javascript==================================================*/
add_action('init', 'modify_jquery');
function modify_jquery() {
    if (!is_admin()) {
        wp_deregister_script('wp-embed');

    }
}
add_action('wp_enqueue_scripts', 'remove_head_scripts');
function remove_head_scripts() {
    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);
    remove_action('wp_head', 'wlwmanifest_link');
    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);
}
add_action('init', 'disable_wp_emojicons');
function disable_wp_emojicons() {
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
}

/*==================================================Clean Wordpress Header==================================================*/

function rmyoast_ob_start() {
    ob_start('remove_yoast');
}
function rmyoast_ob_end_flush() {
    ob_end_flush();
}
function remove_yoast($output) {
    if (defined('WPSEO_VERSION')) {
        $output = str_ireplace([
            '<!-- This site is optimized with the Yoast SEO plugin v' . WPSEO_VERSION . ' - https://yoast.com/wordpress/plugins/seo/ -->',
            '<!-- / Yoast SEO plugin. -->'
        ], '', $output);
    }
    if (defined('QTX_VERSION')) {
        $output = str_replace('<meta name="generator" content="qTranslate-X ' . QTX_VERSION . '" />', '', $output);
    }
    return $output;
}
add_action('get_header', 'rmyoast_ob_start');
add_action('wp_head', 'rmyoast_ob_end_flush', 100);

add_filter('w3tc_can_print_comment', function( $w3tc_setting ) { return false; }, 10, 1);

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

/*==================================================Filter The Content==================================================*/

function add_img_responsive($content) {
    $pattern = "/<img(.*?)class=\"(.*?)\/>/";
    $replacement = '<img$1class="img-responsive $2/>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}
add_filter('the_content', 'add_img_responsive');

/*
 * get related post
 */
if ( ! function_exists( 'bds_get_related_posts' ) ) {
    function bds_get_related_posts($post_id, $limit = -1) {
        $query = new WP_Query();
        $args = '';
        $args = wp_parse_args($args, array(
            'posts_per_page' => $limit,
            'post__not_in' => array($post_id),
            'ignore_sticky_posts' => 0,
            'category__in' => wp_get_post_categories($post_id)
        ));
        $query = new WP_Query($args);
        return $query;
    }
}
/***
 * Check has sub menu
 * @param int $menu_id
 * @return bool
 */
function hassubMenu($menu_id = 0)
{
    $menu_name = 'header-menu'; //menu slug
    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object($locations[ $menu_name ]);
    $menuitems = wp_get_nav_menu_items($menu->term_id, array('order' => 'DESC'));
    if (empty($menuitems)) {
        return false;
    }
    if ($menu_id == 0) {
        return false;
    }
    foreach ($menuitems as $item) {
        if ($item->menu_item_parent == $menu_id) {
            return true;
        }
    }
    return false;
}

/*
 * get related post
 */
if ( ! function_exists( 'get_related_posts' ) ) {
    function get_related_posts($post_id, $limit = -1) {
        $query = new WP_Query();
        $args = '';
        $args = wp_parse_args($args, array(
            'posts_per_page' => $limit,
            'post__not_in' => array($post_id),
            'ignore_sticky_posts' => 0,
            'category__in' => wp_get_post_categories($post_id),

        ));
        $query = new WP_Query($args);
        return $query;
    }
}


/**
 * Filter the categories archive widget to add a span around post count
 */
function smittenkitchen_cat_count_span( $links ) {
    $links = str_replace( '</a> (', '</a><span class="post-count">', $links );
    $links = str_replace( ')', ' bài viết</span>', $links );
    return $links;
}
add_filter( 'wp_list_categories', 'smittenkitchen_cat_count_span' );
/**
 * Get content export
 * @param $str
 * @param int $limit
 * @param string $dot
 * @return bool|string
 */
if (!function_exists('get_content_export')) {
    function get_content_export($str, $limit = 100, $dot = '...')
    {
        if (!empty($str)) {
            $excerpt = substr($str, 0, $limit);
            if (strlen($str) > strlen($excerpt)) {
                return $excerpt . $dot;
            }
            return $excerpt;
        }
    }
}


if (!function_exists('limit_text')) {
    function limit_text($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }
}
ob_start("ob_gzhandler");
ini_set('zlib.output_compression', '1');

if (!is_admin()) ob_start('ob_gzhandler');     //because, in admin pages, it causes plugin installation freezing

remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

// LIMIT 15 for category
function main_query_mods( $query ) {
    if(!$query->is_main_query()) {
        return;
    }
    // show 15 posts per page if category has id 7
    // check http://codex.wordpress.org/Conditional_Tags#A_Category_Page
    if ( is_category(CO_SO_VAT_CHAT_ID)) {
        $query->set('posts_per_page',15);
    }
}
add_action( 'pre_get_posts', 'main_query_mods' );