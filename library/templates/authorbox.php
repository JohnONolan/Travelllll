<section class="authorbox vcard">
	<h3>Published by <span class="author fn"><?php the_author_meta('user_firstname'); ?> <?php the_author_meta('user_lastname'); ?></span> <a class="iconlink rss" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>feed/" rel="nofollow" title="Subscribe to <?php the_author_meta('user_firstname'); ?>'s posts via RSS">RSS</a></h3>
	<a class="more" rel="profile" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">More posts by <?php the_author_meta('user_firstname'); ?></a>
	<?php if (function_exists( 'get_avatar' )) { echo get_avatar(get_the_author_meta( 'user_email' ), '70'); } ?>
	<div class="authormeta">
		<?php 
			$nickname = get_the_author_meta( 'nickname' );
			$description = get_the_author_meta( 'description' );
			if ( !isset( $description) || $description == '' ) {
				echo '<p>' . $nickname . ' is an author for Travelllll.com. If you\'d like to read more posts by ' . $nickname . ', click on the link in the top right corner of this box!</p>';
			}
		?>
		<p><?php the_author_meta('description'); ?></p>
		<?php 
			$website = get_the_author_meta( 'user_url' );
			$twitter = get_the_author_meta( 'twitter' );
			$facebook = get_the_author_meta( 'facebook' );
			$gplus = get_the_author_meta( 'gplus' );
		?>
		<?php if ( isset( $website ) || isset( $twitter ) || isset( $facebook ) || isset( $gplus ) ) : ?>
		<div class="authorlinks">
			<ul>
				<?php if ( isset( $website ) && $website !== '' ) { ?><li><a class="iconlink website url" href="<?php the_author_meta( 'user_url' ); ?>" title="Visit <?php the_author_meta( 'nickname' ); ?>'s website">Website</a></li><?php } ?>
				<?php if ( isset( $twitter ) && $twitter !== '' ) { ?><li><a class="iconlink twitter url" href="<?php the_author_meta( 'twitter' ); ?>" title="Follow <?php the_author_meta( 'nickname' ); ?> on Twitter">Twitter</a></li><?php } ?>
				<?php if ( isset( $facebook ) && $facebook !== '' ) { ?><li><a class="iconlink facebook url" href="<?php the_author_meta( 'facebook' ); ?>" title="Find <?php the_author_meta( 'nickname' ); ?> on Facebook">Facebook</a></li><?php } ?>
				<?php if ( isset( $gplus ) && $gplus !== '' ) { ?><li><a class="iconlink gplus url" href="<?php the_author_meta( 'gplus' ); ?>?rel=author" rel="me" title="Add <?php the_author_meta( 'nickname' ); ?> on Google Plus">Google Plus</a></li><?php } ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
</section>