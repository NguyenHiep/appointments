<?php global $theme_options; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Averia+Serif+Libre|Source+Sans+Pro" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo THEME_URL?>style.css">
  <?php wp_head(); ?>
</head>
<body>
	<header class="header">
      <?php if (!empty($theme_options[ 'logo' ])) : ?>
				<a href="<?php echo  home_url('/'); ?>">
					<div class="logo">
						<img src="<?php echo $theme_options[ 'logo' ][ 'url' ]; ?>" alt="logo" class="img-logo img-responsive">
					</div>
				</a>
		  <?php else: ?>
	      <a href="<?php echo  home_url('/'); ?>" class="logo">Logo</a>
      <?php endif; ?>
		<div class="menu-container">
		<?php wp_nav_menu(); ?>
		</div>
		<div class="burger-menu visible-xs">
			<span class="line1"></span>
			<span class="line2"></span>
			<span class="line3"></span>
		</div>
	</header>

