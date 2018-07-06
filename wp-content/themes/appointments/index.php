<?php global $theme_options; ?>
<?php $face_image = getImageCategoryById(CATEGORY_FACE_ID);
if (empty($face_image)) {
    $face_image = THEME_URL . 'assets/' . 'imgs/img-face-service.jpg';
}
$body_image = getImageCategoryById(CATEGORY_BODY_ID);
if (empty($body_image)) {
    $body_image = THEME_URL . 'assets/' . 'imgs/img-body-service.jpg';
} ?>
<?php get_header(); ?>

	<div class="page page--index">
		<div class="page__inner">
			<div class="index-block">
				<div class="index-text" data-background="face-background">face</div>
				<div class="index-text" data-background="body-background">body</div>
			</div>
			<div class="background face-background active" style="background: url(<?php echo $face_image ?>) no-repeat center center"></div>
			<div class="background body-background" style="background: url(<?php echo $body_image ?>) no-repeat center center; "></div>
		</div>
	</div>
<?php get_footer();
