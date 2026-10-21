<?php
$hours = sln_hour_rows( sln_field( 'home', 'visit', 'hours' ) );
$link  = sln_field( 'home', 'visit', 'directions_link' );
?>
<section class="sl-section sl-pad" id="visit">
	<div class="sl-wrap">
		<?php if ( sln_field( 'home', 'visit', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'home', 'visit', 'eyebrow' ) ); ?></div><?php endif; ?>
		<h2><?php echo esc_html( sln_field( 'home', 'visit', 'heading' ) ); ?></h2>
		<div class="sl-split">
			<div class="sl-panel">
				<?php if ( sln_field( 'home', 'visit', 'name' ) ) : ?><h3><?php echo esc_html( sln_field( 'home', 'visit', 'name' ) ); ?></h3><?php endif; ?>
				<?php foreach ( sln_paragraphs( sln_field( 'home', 'visit', 'address' ) ) as $line ) : ?>
					<p><?php echo wp_kses_post( nl2br( esc_html( $line ) ) ); ?></p>
				<?php endforeach; ?>
				<?php if ( $hours ) : ?>
					<table class="sl-hours"><tbody>
						<?php foreach ( $hours as $row ) : ?>
							<tr><td><?php echo esc_html( $row['day'] ); ?></td><td><?php echo esc_html( $row['hours'] ); ?></td></tr>
						<?php endforeach; ?>
					</tbody></table>
				<?php endif; ?>
				<?php if ( sln_field( 'home', 'visit', 'directions_text' ) ) : ?>
					<a class="sl-btn" href="<?php echo esc_url( $link ? $link : home_url( '/contact/' ) ); ?>"><?php echo esc_html( sln_field( 'home', 'visit', 'directions_text' ) ); ?></a>
				<?php endif; ?>
			</div>
			<div class="sl-frame sl-map">
				<?php sln_map_frame( sln_field( 'home', 'visit', 'map_embed' ), sln_field( 'home', 'visit', 'map_image' ), sln_field( 'home', 'visit', 'map_label' ) ); ?>
			</div>
		</div>
	</div>
</section>
