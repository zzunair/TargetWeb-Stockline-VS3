<?php
$cols = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$label = sln_field( 'home', 'compare_teaser', 'col_' . $i );
	if ( $label ) {
		$cols[ $i ] = $label;
	}
}
$rows = sln_repeater( 'home', 'compare_teaser', 'rows' );
if ( ! $cols || ! $rows ) {
	return;
}
?>
<section class="sl-section sl-pad">
	<div class="sl-wrap">
		<?php if ( sln_field( 'home', 'compare_teaser', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'home', 'compare_teaser', 'eyebrow' ) ); ?></div><?php endif; ?>
		<h2><?php echo esc_html( sln_field( 'home', 'compare_teaser', 'heading' ) ); ?></h2>
		<?php if ( sln_field( 'home', 'compare_teaser', 'body' ) ) : ?><p class="sl-lede"><?php echo esc_html( sln_field( 'home', 'compare_teaser', 'body' ) ); ?></p><?php endif; ?>
		<div class="sl-table-wrap">
			<table class="sl-table">
				<thead><tr>
					<th><?php echo esc_html__( 'Spec', 'stockline-vs3' ); ?></th>
					<?php foreach ( $cols as $label ) : ?><th><?php echo esc_html( $label ); ?></th><?php endforeach; ?>
				</tr></thead>
				<tbody>
					<?php foreach ( $rows as $n => $row ) : ?>
						<tr class="<?php echo $n % 2 ? 'is-alt' : ''; ?>">
							<td><?php echo esc_html( $row['label'] ); ?></td>
							<?php foreach ( $cols as $index => $label ) : ?>
								<td class="<?php echo (string) $row['best'] === (string) $index ? 'is-best' : ''; ?>"><?php echo esc_html( $row[ 'v' . $index ] ); ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
