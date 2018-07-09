<?php
/* Template Name: Page appointments */
get_header();
global $post;
?>
<section id="content">
    <?php echo apply_filters('the_content', $post->post_content); ?>
</section>
<?php get_footer(); ?>
