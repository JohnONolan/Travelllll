<?php 

$t5_sponsored_image = get_post_custom_values( 'sponsored_image' );
$t5_sponsored_text = get_post_custom_values( 'sponsored_text' );
$t5_sponsored_background = get_post_custom_values( 'sponsored_background' );

if( $t5_sponsored_background[0] != '' ) {
	t5_sponsored_background();
	add_action( 'wp_head', 't5_sponsored_background' );
}

if( $t5_sponsored_image[0] !== '' && isset( $t5_sponsored_text[0] ) ) : ?>

	<aside class="sponsorbox">
		<?php woo_image( 'key=sponsored_image&width=160&height=80&class=sponsor-image&link=img' ); ?>
		<p><?php echo $t5_sponsored_text[0]; ?></p>
		<div class="clearfix"></div>
	</aside>
	
<?php endif; ?>