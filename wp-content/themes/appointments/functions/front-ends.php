<?php
set_post_thumbnail_size( 250, 250 );

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
 * get category post by id
 */
if (!function_exists('getImageCategoryById')) {
    function getImageCategoryById($cat_id)
    {
        // get the current taxonomy term
        $term  = get_term_by('id', $cat_id, 'category');
        $image = get_field('image', $term);
        if ($image) {
            return $image;
        }
        return false;
    }
}

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

remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );