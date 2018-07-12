<?php
/* Template Name: Page contact */
get_header();
global $post;
?>
<div class="page page--contact">
  <div class="page__inner">
    <h1 class="page-title active"><strong><?php the_title()?></strong></h1>
    <?php if(get_field('google_map')):?>
      <div class="googlemap"><?php echo get_field('google_map')?></div>
    <?php endif; ?>
    <div class="post-block">
      <div class="row">
          <?php echo apply_filters('the_content', $post->post_content);?>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
