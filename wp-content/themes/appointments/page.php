<?php
get_header();
if (have_posts()) :
    global $post;
    ?>
    <div class="page">
        <div class="page__inner">
            <h1 class="page-title active"><strong><?php the_title() ?></strong></h1>
            <div class="post-block">
                <div class="row">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="thumbnail-img clearfix col-md-3">
                            <img src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full') ?>"
                                 alt="thumbnail-img"
                                 class="img-responsive" style="width: 100%;">
                        </div>
                        <div class="col-md-9">
                            <?php echo apply_filters('the_content', $post->post_content); ?>
                        </div>
                    <?php else: ?>
                        <div class="col-md-12">
                            <?php echo apply_filters('the_content', $post->post_content); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
    <?php
endif;
get_footer();
?>