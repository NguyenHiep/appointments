<?php
/* Template Name: Page service */
get_header();
?>
<div class="page page--service">
	<div class="page__inner">
		<h1 class="page-title active"><strong><?php echo get_the_title();?></strong></h1>
		<?php
	    $blocks = ['face', 'body'];
	    if (!empty($blocks)) {
	        foreach ($blocks as $block) {
	            get_template_part('template-parts/block', $block);
	        }
	    }
		?>
		<a class="big-link" href="<?php echo get_permalink(APPOINTMENTS_ID)?>">Book an appointment →</a>
	</div>
</div>
<?php
get_footer();
?>
