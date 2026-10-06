<?php
/**
 * Site footer, compare bar, and the mobile contact bar.
 */
$address = sln_lines( sln_mod( 'sln_footer_address', "123 Main Street\nYour City, ST 00000" ) );
$hours   = sln_lines( sln_mod( 'sln_footer_hours', "Mon–Fri 8–6\nSat 8–4\nSun closed" ) );
$shop_id = (int) sln_mod( 'sln_footer_shop_menu', '' );
$co_id   = (int) sln_mod( 'sln_footer_company_menu', '' );
$shop    = function_exists( 'sln_shop_url' ) ? sln_shop_url() : home_url( '/shop/' );
$phone   = sln_phone();
$email   = sln_email();

$shop_terms = array();
if ( taxonomy_exists( 'product_cat' ) ) {
	$shop_terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => array( (int) get_option( 'default_product_cat' ) ), 'number' => 8 ) );
	if ( is_wp_error( $shop_terms ) ) {
		$shop_terms = array();
	}
}
$brand_terms = array();
if ( taxonomy_exists( 'product_brand' ) ) {
	$brand_terms = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false, 'number' => 8 ) );
	if ( is_wp_error( $brand_terms ) ) {
		$brand_terms = array();
	}
}

$company_links = array(
	array( 'Service', home_url( '/service/' ) ),
	array( 'About', home_url( '/about/' ) ),
	array( 'Contact', home_url( '/contact/' ) ),
	array( 'Brands', home_url( '/brands/' ) ),
);
$compare_on = (bool) sln_mod( 'sln_compare_enabled', true );
?>
<footer class="sl-footer">
	<div class="sl-wrap sl-footer-grid">
		<div>
			<div class="sl-footer-brand"><?php echo esc_html( sln_mod( 'sln_footer_company_name', get_bloginfo( 'name' ) ) ); ?></div>
			<?php if ( $address ) : ?><p><?php echo wp_kses_post( implode( '<br>', array_map( 'esc_html', $address ) ) ); ?></p><?php endif; ?>
			<?php if ( $hours ) : ?>
				<p><span class="screen-reader-text"><?php echo esc_html( sln_mod( 'sln_footer_hours_heading', 'Hours' ) ); ?>: </span><?php echo wp_kses_post( implode( '<br>', array_map( 'esc_html', $hours ) ) ); ?></p>
			<?php endif; ?>
		</div>
		<div>
			<div class="sl-footer-title"><?php echo esc_html( sln_mod( 'sln_footer_shop_heading', 'Categories' ) ); ?></div>
			<?php if ( $shop_id || has_nav_menu( 'footer_shop' ) ) : ?>
				<?php
				wp_nav_menu(
					array_merge(
						array( 'container' => false, 'menu_class' => 'sl-footer-links', 'depth' => 1 ),
						$shop_id ? array( 'menu' => $shop_id ) : array( 'theme_location' => 'footer_shop' )
					)
				);
				?>
			<?php else : ?>
				<ul class="sl-footer-links">
					<?php if ( $shop_terms ) : ?>
						<?php foreach ( $shop_terms as $term ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
						<?php endforeach; ?>
					<?php else : ?>
						<li><a href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html( sln_mod( 'sln_footer_shop_heading', 'Categories' ) ); ?></a></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div>
			<div class="sl-footer-title"><?php echo esc_html( sln_mod( 'sln_footer_company_heading', 'Brands' ) ); ?></div>
			<?php if ( $co_id || has_nav_menu( 'footer_company' ) ) : ?>
				<?php
				wp_nav_menu(
					array_merge(
						array( 'container' => false, 'menu_class' => 'sl-footer-links', 'depth' => 1 ),
						$co_id ? array( 'menu' => $co_id ) : array( 'theme_location' => 'footer_company' )
					)
				);
				?>
			<?php elseif ( $brand_terms ) : ?>
				<ul class="sl-footer-links">
					<?php foreach ( $brand_terms as $term ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<ul class="sl-footer-links">
					<?php foreach ( $company_links as $link ) : ?>
						<li><a href="<?php echo esc_url( $link[1] ); ?>"><?php echo esc_html( $link[0] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php if ( sln_mod( 'sln_newsletter_enabled', true ) ) : ?>
			<div>
				<div class="sl-footer-title"><?php echo esc_html( sln_mod( 'sln_newsletter_heading', 'Stock alerts' ) ); ?></div>
				<?php if ( sln_mod( 'sln_newsletter_body', '' ) ) : ?><p><?php echo esc_html( sln_mod( 'sln_newsletter_body', '' ) ); ?></p><?php endif; ?>
				<form class="sl-news" action="#" method="post">
					<label class="screen-reader-text" for="sl-news-email"><?php echo esc_html( sln_mod( 'sln_newsletter_placeholder', 'Email address' ) ); ?></label>
					<input id="sl-news-email" type="email" name="email" placeholder="<?php echo esc_attr( sln_mod( 'sln_newsletter_placeholder', 'Email address' ) ); ?>" />
					<button type="submit" class="sl-btn"><?php echo esc_html( sln_mod( 'sln_newsletter_button_text', 'Join' ) ); ?></button>
				</form>
			</div>
		<?php endif; ?>
	</div>
	<div class="sl-wrap sl-footer-legal">
		<span><?php echo esc_html( sln_mod( 'sln_footer_note', 'Catalog only — no online sales.' ) ); ?></span>
		<span><?php echo esc_html( sln_mod( 'sln_footer_legal_right', 'Specs and stock subject to change. Pricing on request.' ) ); ?></span>
	</div>
</footer>

<?php if ( $compare_on ) : ?>
<div class="sl-compare-bar" id="sl-compare-bar" hidden
	data-max="<?php echo (int) max( 2, min( 4, (int) sln_mod( 'sln_compare_max_items', 4 ) ) ); ?>"
	data-bar-text="<?php echo esc_attr( sln_mod( 'sln_compare_bar_text', '{count} selected' ) ); ?>"
	data-max-text="<?php echo esc_attr( sln_mod( 'sln_compare_max_text', 'You can compare up to {max} items at a time.' ) ); ?>"
	data-view-text="<?php echo esc_attr( sln_mod( 'sln_compare_view_text', 'View Specs' ) ); ?>">
	<span id="sl-compare-bar-text"></span>
	<span class="sl-compare-actions">
		<button type="button" id="sl-compare-clear"><?php echo esc_html( sln_mod( 'sln_compare_clear_text', 'Clear' ) ); ?></button>
		<button type="button" class="sl-btn" id="sl-compare-open"><?php echo esc_html( sln_mod( 'sln_compare_button_text', 'Compare Now' ) ); ?></button>
	</span>
</div>
<div class="sl-modal" id="sl-compare-modal" hidden>
	<div class="sl-modal-backdrop" id="sl-compare-modal-overlay"></div>
	<div class="sl-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="sl-compare-title">
		<div class="sl-modal-head">
			<h2 id="sl-compare-title"><?php echo esc_html( sln_mod( 'sln_compare_modal_heading', 'Compare specs' ) ); ?></h2>
			<button type="button" id="sl-compare-modal-close" aria-label="<?php echo esc_attr( sln_field( 'product', 'product_main', 'close_label' ) ); ?>">×</button>
		</div>
		<div id="sl-compare-modal-body"></div>
	</div>
</div>
<?php endif; ?>

<?php if ( $phone || $email ) : ?>
<div class="sl-mbar">
	<?php if ( $phone ) : ?><a href="<?php echo esc_url( sln_tel_href( $phone ) ); ?>"><?php echo esc_html( sln_mod( 'sln_header_call_label', 'Call the Store' ) ); ?></a><?php endif; ?>
	<a href="<?php echo esc_url( sln_mod( 'sln_header_reserve_button_link', '/contact/' ) ); ?>"><?php echo esc_html( sln_mod( 'sln_header_email_label', 'Email' ) ); ?></a>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
