<?php
get_header();
if (have_posts()) :
    $page_id = get_the_ID();
    ?>
    <div class="clearfix"></div>
    <section id="content">
        <div class="thumbnail-img clearfix">
            <?php if (has_post_thumbnail()): ?>
                <img src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($page_id), 'full') ?>" alt=""
                     class="img-responsive" style="width: 100%;">
            <?php endif; ?>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center mcare-page"><?php the_title() ?></h1>
                    <div class="content-post">
                        <?php the_content() ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
endif;
get_footer();