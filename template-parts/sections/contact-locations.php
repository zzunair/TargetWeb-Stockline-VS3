<?php
$items = sln_repeater( 'contact', 'locations', 'items' );
?>
<div class="sl-contact-side" id="visit">
	<div class="sl-info-cards">
		<div><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'contact', 'locations', 'phone_label' ) ); ?></div><div><?php echo esc_html( sln_field( 'contact', 'locations', 'phone' ) ); ?></div></div>
		<div><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'contact', 'locations', 'email_label' ) ); ?></div><div><?php echo esc_html( sln_field( 'contact', 'locations', 'email' ) ); ?></div></div>
		<div><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'contact', 'locations', 'address_label' ) ); ?></div><div><?php echo esc_html( sln_field( 'contact', 'locations', 'address_card' ) ); ?></div></div>
	</div>
	<?php foreach ( $items as $item ) : ?>
		<div class="sl-panel">
			<h3><?php echo esc_html( $item['name'] ); ?></h3>
			<p><?php echo wp_kses_post( nl2br( esc_html( $item['address'] ) ) ); ?></p>
			<?php $hours = sln_hour_rows( $item['hours'] ); if ( $hours ) : ?>
				<table class="sl-hours"><tbody>
					<?php foreach ( $hours as $row ) : ?><tr><td><?php echo esc_html( $row['day'] ); ?></td><td><?php echo esc_html( $row['hours'] ); ?></td></tr><?php endforeach; ?>
				</tbody></table>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	<div class="sl-frame sl-map">
		<?php if ( sln_field( 'contact', 'locations', 'map_image' ) ) : ?>
			<img src="<?php echo esc_url( sln_field( 'contact', 'locations', 'map_image' ) ); ?>" alt="<?php echo esc_attr( sln_field( 'contact', 'locations', 'map_label' ) ); ?>" />
		<?php else : ?>
			<?php sln_placeholder( sln_field( 'contact', 'locations', 'map_label' ) ); ?>
		<?php endif; ?>
	</div>
	<?php
	$link = '';
	foreach ( $items as $item ) {
		if ( ! empty( $item['directions_link'] ) ) {
			$link = $item['directions_link'];
			break;
		}
	}
	if ( sln_field( 'contact', 'locations', 'directions_text' ) ) :
		?>
		<a class="sl-btn sl-btn-ghost" href="<?php echo esc_url( $link ? $link : home_url( '/contact/' ) ); ?>"><?php echo esc_html( sln_field( 'contact', 'locations', 'directions_text' ) ); ?></a>
	<?php endif; ?>
</div>
