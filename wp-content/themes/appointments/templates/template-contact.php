<?php
/* Template Name: Page contact */
get_header();
global $post;
?>
<!--<section id="content">
    <?php /*if(get_field('google_map')):*/?>
    <div class="googlemap"><?php /*echo get_field('google_map')*/?></div>
    <?php /*endif; */?>
    <?php /*echo apply_filters('the_content', $post->post_content); */?>
</section>-->
<div class="page page--contact">
  <div class="page__inner">
    <h1 class="page-title active"><strong><?php the_title()?></strong></h1>
    <div class="post-block">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="post-title post-title--size-2">
            123 Fake St.<br/>
            Palm Springs, CA<br/>
            992262
          </h3>
        </div>
        <div class="col-sm-6">
          <div class="post-title post-title--size-2">
            tel. +1 442 555 1234<br/>
            placeholder@example.com<br/><br/>
            _<br/><br/>
            8:00 am to 10:00 pm<br/>
            Every day
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
