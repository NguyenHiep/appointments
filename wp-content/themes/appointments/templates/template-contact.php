<?php
/* Template Name: Page contact */
get_header();
global $post;
?>
<section id="content">
    <?php if(get_field('google_map')):?>
    <div class="googlemap"><?php echo get_field('google_map')?></div>
    <?php endif; ?>
    <?php echo apply_filters('the_content', $post->post_content); ?>
</section>
<?php get_footer(); ?>
