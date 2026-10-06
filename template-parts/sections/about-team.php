<?php
$members = sln_repeater( 'about', 'team', 'members' );
if ( ! $members ) {
	return;
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap">
	<h2><?php echo esc_html( sln_field( 'about', 'team', 'heading' ) ); ?></h2>
	<div class="sl-team">
		<?php foreach ( $members as $member ) : ?>
			<div>
				<div class="sl-portrait">
					<?php if ( $member['image'] ) : ?><img src="<?php echo esc_url( $member['image'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" /><?php else : ?><?php sln_placeholder( $member['role'] ); ?><?php endif; ?>
				</div>
				<strong><?php echo esc_html( $member['name'] ); ?></strong>
				<span><?php echo esc_html( $member['role'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</div></section>
