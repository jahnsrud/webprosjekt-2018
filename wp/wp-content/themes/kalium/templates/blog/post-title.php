<?php
/**
 *	Post title single page
 *	
 *	Laborator.co
 *	www.laborator.co 
 *
 *	@author		Laborator
 *	@var		$heading_tag_open
 *	@var		$heading_tag_close
 *	@version	2.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}
?>
<header class="entry-header">
	
	<?php if ( is_single() ) : ?>
		<?php the_title( $heading_tag_open, $heading_tag_close ); ?>
	<?php else : ?>
		<?php the_title( sprintf( '%s<a href="%s" rel="bookmark">', $heading_tag_open, get_permalink() ), sprintf( '</a>%s', $heading_tag_close ) ); ?>
	<?php endif; ?>
		
</header>