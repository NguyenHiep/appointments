<?php
global $theme_options;
$posts_per_page = (!empty($theme_options['block_face'])) ? $theme_options['block_face'] : 6;
$args = [
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'order'          => 'DESC',
    'orderby'        => 'ID',
    'tax_query'      => [
        [
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => CATEGORY_FACE_ID
        ]
    ]
];
$the_query = new WP_Query($args);
if ($the_query->have_posts()) :
    ?>
	<div class="category-block">
		<h2 class="category-title">face</h2>
      <?php while ($the_query->have_posts()): $the_query->the_post();
          $thumbnail_url = get_the_post_thumbnail_url($the_query->ID, 'full');
          if (empty($thumbnail_url)) {
              $thumbnail_url = IMAGE_DEFAULT;
          }
          ?>
				<div class="post-block">
					<div class="row">
						<div class="col-sm-4 col-sm-push-2">
							<div class="post-img">
								<img src="<?php echo $thumbnail_url ?>" class="img-responsive" alt="<?php the_title(); ?>">
							</div>
						</div>
						<div class="col-sm-2 col-sm-pull-4">
							<div class="post-left">
								<h3 class="post-title"><?php the_title(); ?></h3>
								<?php $key = 'times';
	                if (get_field($key)):
                    $field = get_field_object($key);
                    $times = get_field($key).' '.$field['append']; ?>
                  <div class="post-time"><?php echo $times; ?></div>
                <?php endif; ?>

                <?php $key = 'price';
                if (get_field($key)):
                    $field = get_field_object($key);
                    $price = $field['append'] . get_field($key); ?>
	                <div class="post-cost"><?php echo $price; ?></div>
                <?php endif; ?>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="post-desc">
                  <?php echo get_content_export(get_the_excerpt(), 300, '...'); ?>
							</div>
						</div>
					</div>
				</div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
	</div>
    <?php
endif;
?>