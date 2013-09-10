<div class="clearfix"></div>

<?php if ( post_password_required() ) : ?>
<section id="comments">
	<p class="nopassword">This post is password protected. Enter the password to view any comments.</p>
</section>
<?php return; endif; ?>

<section id="comments" class="column-8">

	<?php if ( have_comments() ) : ?>
	
	<h3 id="comments-title"><?php
	printf( _n( 'One Comment So Far', '%1$s Comments So Far', get_comments_number(), 'twentyten' ),
	number_format_i18n( get_comments_number() ) );
	?>, what do you think?</h3>
	
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav class="navigation">
		<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'twentyten' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
	</nav>
	<?php endif; ?>
	
	<ol class="commentlist">
		<?php wp_list_comments( array('callback' => 't5_comment') ); ?>
	</ol>
	
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav class="navigation">
		<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'twentyten' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
	</nav>
	<?php endif; ?>
	
	<?php else : if ( ! comments_open() ) : ?>
	<p class="nocomments">Comments on this post are closed.</p>
	<?php endif; // end ! comments_open() ?>
	
	<?php endif; // end have_comments() ?>
	
	<?php comment_form(); ?>
	<div class="clearfix"></div>
	
</section>