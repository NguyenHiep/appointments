<?php
/* Template Name: Page about */
get_header();
global $post;
?>
<!--<section id="content">
    <?php /*echo apply_filters('the_content', $post->post_content); */?>
</section>-->
<div class="page page--about">
  <div class="page__inner">
    <h1 class="page-title active"><strong><?php the_title()?></strong></h1>
    <div class="row">
      <div class="col-sm-6">
        <img src="imgs/static1.squarespace.jpg" alt="" class="img-responsive">
      </div>
      <div class="col-sm-5 col-sm-offset-1">
        <div class="post-block">
          <h3 class="post-title post-title--size-2">
            Proin pretium quam sit amet maximus fermentum. Vivamus vel finibus turpis, sed interdum.
          </h3>
          <p class="post-desc">
            Add a description of your business here. In hac habitasse platea dictumst. Morbi lectus quam, fringilla ac velit vel, luctus pulvinar nisi. In mattis gravida sed nunc. Vestibulum tellus dolor, scelerisque dui sed, egestas ornare ligula. Nunc commodo, nunc et vulputate egestas, elit nulla condimentum nulla, sit amet placerat risus justo sed eros.
          </p>
          <div class="mb50"></div>
          <p class="post-desc">
            Find us on: <br/>
            <a href="#">Instagram</a><br/>
            <a href="#">Facebook</a><br/>
            <a href="#">Twitter</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
