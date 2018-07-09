<?php
get_header();
?>
    <section class="mcare-news-wrap blog-page col3-page">
        <div class="container">
            <h1 class="mcare-h1"><?php the_title(); ?></h1>
            <article class="post-mcare-news-detail">
                <div class="row">
                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                        <div class="clearfix box-content">
                            <!--mcare news box-->
                            <div class="content-detail clearfix">
                                <?php the_content(); ?>
                            </div>
                            <?php
                            global $post;
                            $related_post = get_related_posts($post->ID, 3);
                            if ($related_post->have_posts()):
                                ?>
                                <h2 class="text-uppercase">Bài viết liên quan</h2>
                                <div class="related-posts row clearfix">
                                    <?php
                                    while ($related_post->have_posts()) : $related_post->the_post();
                                        $thumbnail_url = get_the_post_thumbnail_url($related_post->ID);
                                        if (empty($thumbnail_url)) {
                                            $thumbnail_url = IMAGE_DEFAULT;
                                        }
                                        ?>
                                        <div class="col-md-4 col-lg-4 col-sm-2 col-xs-12 look2">
                                            <!--mcare news box-->
                                            <div class="mcare-news-box">
                                                <div class="news-img">
                                                    <a href="<?php the_permalink() ?>">
                                                        <img src="<?php echo $thumbnail_url; ?>" class="img-responsive"
                                                             alt="<?php the_title() ?>">
                                                    </a>
                                                    <div class="post-meta">
                                                        <div class="post-date">
                                                            <a href="<?php the_permalink() ?>"><?php echo date('j / M',
                                                                    strtotime($post->post_date)) ?> <br>
                                                                <strong><?php echo date('Y',
                                                                        strtotime($post->post_date)) ?></strong>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4><a href="<?php the_permalink() ?>"><?php echo get_the_title() ?></a>
                                                </h4>
                                                <div class="text-content"><?php echo get_content_export(get_the_excerpt(),
                                                        200); ?></div>
                                                <div class="foot-meta">
                                                    <div class="readmore"><a href="<?php the_permalink() ?>">Xem
                                                            Thêm</a></div>
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
                                        </div>
                                        <?php
                                        $count++;
                                    endwhile; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 look2">
                        <?php
                        get_sidebar();
                        ?>
                    </div>
                </div>
            </article>
        </div>
    </section>
<?php
get_footer();