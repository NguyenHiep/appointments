<?php
/* Template Name: Page about */
get_header();
global $post;
?>
<div class="page page--about">
  <div class="page__inner">
    <h1 class="page-title active"><strong><?php the_title()?></strong></h1>
    <div class="row">
      <div class="col-sm-6">
          <?php if (has_post_thumbnail()): ?>
              <img src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full') ?>" alt=""
                   class="img-responsive">
          <?php endif; ?>
      </div>
      <div class="col-sm-5 col-sm-offset-1">
        <div class="post-block">
        <?php echo apply_filters('the_content', $post->post_content);?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
