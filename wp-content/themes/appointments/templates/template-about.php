<?php
/* Template Name: Page about */
get_header();
global $post;
?>
<section id="content">
    <?php echo apply_filters('the_content', $post->post_content); ?>
</section>
<?php get_footer(); ?>
