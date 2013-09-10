<?php 
$args = array( 
    'post__in' => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
    'posts_per_page' => 5
);
$slider_posts = new WP_Query( $args );
if( $slider_posts->have_posts() ) : ?>
<section id="homeslider">
    <div id="nivslider" class="wrap">
        <?php while( $slider_posts->have_posts() ) : $slider_posts->the_post();?>
        
            <a href="<?php the_permalink(); ?>"><?php woo_image( 'key=image&width=978&height=180&class=slide-image&link=img' ); ?></a>
        <?php endwhile; ?>
    </div>
</section>
<script type="text/javascript">
jQuery(window).load(function() {
    jQuery('#nivslider').nivoSlider({
        effect:'slideInRight',
        boxCols: 24,
        animSpeed:500,
        pauseTime:8000,
        directionNav:false,
        controlNav:false
    });
});
</script>
<?php endif; ?>