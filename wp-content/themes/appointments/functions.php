<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('STYLE_VERSION', '1.0.1');

if (!defined('THEME_URL')) {
    define('THEME_URL', get_bloginfo('template_url') . '/');
}
define('IMAGE_DEFAULT', 'http://placehold.it/400x300');
define('CATEGORY_FACE_ID', 2);
define('CATEGORY_BODY_ID', 3);
define('APPOINTMENTS_ID', 39);

require_once('functions/admin.php');
require_once('functions/front-ends.php');
// Translation
load_theme_textdomain('appointments', get_stylesheet_directory() . '/languages');
add_filter('wpcf7_form_elements', function($content) {
    $content = preg_replace('/<(span).*?class="\s*(?:.*\s)?wpcf7-form-control-wrap(?:\s[^"]+)?\s*"[^\>]*>(.*)<\/\1>/i', '\2', $content);

    return $content;
});


