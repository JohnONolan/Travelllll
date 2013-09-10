<?php 
$t5_source_name = get_post_custom_values( 'source_name' );
$t5_source_url = get_post_custom_values( 'source_url' );
if ( isset($t5_source_name[0] ) ) { ?>
	<?php if ( isset($t5_source_url[0] ) ) { ?>
		<span class="sourcewrap">Source: <a class="source" href="<?php echo $t5_source_url[0]; ?>"><?php echo $t5_source_name[0]; ?></a></span>
	<?php } else { ?>
		<span class="sourcewrap">Source: <span class="source"><?php echo $t5_source_name[0]; ?></span></span>
	<?php } ?>
<?php } ?>