<?php
/**
 * category  template
 */

get_header(); ?>
  <div class="clearfix"></div>
  <section class="mcare-news-wrap blog-page col3-page">
    <div class="container">
      <h2 class="mcare-h2"><?php echo single_term_title(); ?></h2>

      <div class="mcare-news-inner">
        <div class="row">
          <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
              <?php if ( have_posts() ) : ?>

                <?php
                global $wp_query;
                $total_post = count($wp_query->posts);
                $count = 0;
                while ( have_posts() ) : the_post();
                  $thumbnail = get_the_post_thumbnail_url(get_the_ID(),'full');
                  if($count % 2 == 0){
                      echo '<div class="row">';
                  }
                  if (($count == $total_post) && $total_post % 2 == 1) {
                      echo '<div class="row">';
                  }
                ?>
                  <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12 look2">
                    <div class="mcare-news-box">
                      <div class="news-img">
                          <?php if(!empty($thumbnail)): ?>
                            <a href="<?php the_permalink();?>"><img src="<?php echo $thumbnail; ?>" class="img-responsive" alt="<?php the_title();?>">
                            </a>
                          <?php endif; ?>
                        <div class="post-meta">
                          <div class="post-date"><a href="<?php the_permalink(); ?>"><?php echo date('j / M', strtotime($post->post_date))?> <br><strong><?php echo date('Y', strtotime($post->post_date))?></strong></a></div>
                        </div>
                      </div>
                      <h4><a href="<?php the_permalink()?>"><?php echo get_the_title()?></a></h4>
                      <?php ?>
                      <div class="text-content"><?php echo limit_text(get_the_excerpt(), 60)?></div>
                      <div class="foot-meta">
                        <div class="readmore"><a href="<?php the_permalink()?>">Xem Thêm</a></div>
                      </div>
                      <div class="doc-social-wrap">
                        <i class="ion-android-share-alt share-social share-toggle"></i>
                        <ul class="doc-social share">
                          <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                          <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                          <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                      </div>

                    </div>
                  </div>
                    <?php
                $count++;
                if($count % 2 == 0){
                  echo '</div>';
                }
                if (($count == $total_post) && $total_post % 2 == 1) {
                    echo '</div>';
                }
                endwhile;
                ?>
                  <?php
              else :
                  //get_template_part( 'content', 'none' );
              endif;
              ?>
          <div class="row ">
              <div class="col-md-8 col-lg-offset-4">
                  <?php
                  if ( function_exists('wp_bootstrap_pagination') )
                      wp_bootstrap_pagination();
                  ?>
              </div>
            </div>

          </div>
          <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 look2">
              <?php
                  get_sidebar();
              ?>
          </div>
        </div>
      </div>

  </section>
<?php
get_footer();