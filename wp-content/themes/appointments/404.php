<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main container" role="main">
			<section class="error-404 not-found row">
				<div class="col-md-12">
          <header class="page-header">
            <h1 class="page-title text-center"><?php _e( 'Rất tiếc! Không thể tìm thấy trang đó.', 'bvhcm' ); ?></h1>
          </header><!-- .page-header -->
          <div class="page-content text-center">
            <p><?php _e( 'Thật không may, các trang web mà bạn đang tìm kiếm có thể không được tìm thấy. <br/> 
Nó có thể là tạm thời không có, di chuyển hoặc không còn tồn tại', 'bvhcm' ); ?></p>
            <a href="<?php echo get_home_url()?>" class="btn btn-search">Trở về trang chủ</a>
          </div><!-- .page-content -->
        </div>
			</section><!-- .error-404 -->
		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
