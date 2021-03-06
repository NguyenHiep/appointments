<?php
/* Template Name: Page appointments */
get_header();
global $post;
?>
<section class="page page--appointments">
    <div class="row page__inner">
      <h1 class="page-title active"><strong><?php the_title()?></strong></h1>
      <div class="col-md-6">
        <div class="post-desc">
            <?php if(get_field('instroduction')):?>
              <?php echo get_field('instroduction')?>
            <?php endif; ?>
        </div>
      </div>
      <div class="col-md-6">
          <?php echo apply_filters('the_content', $post->post_content); ?>
      </div>
    </div>
</section>
<?php get_footer(); ?>
