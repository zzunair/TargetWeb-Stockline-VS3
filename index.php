<?php
/**
 * Fallback, including cart and checkout when commerce is on.
 */
get_header();
$commerce = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() );
?>
<main class="sl-page sl-pad <?php echo $commerce ? 'sl-commerce' : ''; ?>">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php if ( $commerce ) : ?>
				<?php
				if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
					$crumb = sln_mod( 'sln_received_label', 'Order received' );
				} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
					$crumb = sln_mod( 'sln_checkout_label', 'Checkout' );
				} else {
					$crumb = sln_mod( 'sln_cart_label', 'Cart' );
				}
				sln_breadcrumb( array(
					array( 'label' => sln_field( 'shop', 'breadcrumb', 'home_label' ), 'url' => home_url( '/' ) ),
					array( 'label' => $crumb, 'url' => '' ),
				) );
				?>
				<h1><?php the_title(); ?></h1>
				<div class="sl-commerce-body"><?php the_content(); ?></div>
			<?php else : ?>
				<article>
					<h1><?php the_title(); ?></h1>
					<div class="sl-prose"><?php the_content(); ?></div>
				</article>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php echo esc_html( sln_mod( 'sln_empty_label', 'Nothing found.' ) ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
