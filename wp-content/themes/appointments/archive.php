<?php
get_header();
?>
<?php
$paged = 1;
if (get_query_var('paged')) {
    $paged = get_query_var('paged');
}
if (get_query_var('page')) {
    $paged = get_query_var('page');
}
$category = get_queried_object();

$args = [
    'post_type'   => 'services',
    'orderby'     => 'ID',
    'order'       => 'DESC',
    'post_status' => 'publish',
    'tax_query'   => [
        [
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $category->term_id
        ]
    ],
    'paged'       => $paged
];
query_posts( $args );

if (have_posts()) :?>
    <section class="mcare-text-ibox2-wrap ss-style-triangles sep-section">
        <div class="container">
            <h1 class="text-center"><?php single_cat_title();?></h1>
            <div class="row">
                <?php while (have_posts()): the_post();
                    $thumbnail_url = get_the_post_thumbnail_url($post->ID);
                    if(empty($thumbnail_url)) $thumbnail_url = IMAGE_DEFAULT;
                    ?>
                    <!--Icon Box-->
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="pic pic-3d">
                            <div class="mcare-text-ibox2 border-effect">
                                <a href="<?php the_permalink()?>" class="clearfix" title="<?php the_title();?>">
                                    <div class="">
                                        <p class="main-content ibox2-content text-content">
                                            <img style="width:100%" src="<?php echo $thumbnail_url; ?>" alt="<?php the_title()?>" title="<?php the_title()?>">
                                        </p>
                                        <h4><?php the_title(); ?></h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php  wp_reset_postdata(); ?>
            </div>
            <div class="row ">
                <div class="col-md-8 col-lg-offset-4">
                    <?php
                    /*if ( function_exists('wp_bootstrap_pagination') )
                        wp_bootstrap_pagination();

                    /*if ( function_exists('paginate') ){
                        paginate();
                    }*/
                    ?>

                </div>
            </div>
        </div>
    </section>
    <?php
endif;
?>
<?php
get_footer();
?>
