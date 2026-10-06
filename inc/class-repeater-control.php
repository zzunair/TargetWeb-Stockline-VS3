<?php
/**
 * Add / remove / reorder repeater. Value is a JSON array of blocks.
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class SLN_Repeater_Control extends WP_Customize_Control {

	public $type = 'sln-repeater';

	public $block_fields = array();

	public $title_field = '';

	public $add_button_text = 'Add block';

	public $min_blocks = 0;

	public function enqueue() {
		wp_enqueue_media();
		wp_enqueue_script(
			'sln-customizer-controls',
			get_template_directory_uri() . '/assets/js/customizer-controls.js',
			array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
			SLN_VERSION,
			true
		);
		wp_enqueue_style(
			'sln-customizer-controls',
			get_template_directory_uri() . '/assets/css/customizer-admin.css',
			array(),
			SLN_VERSION
		);
	}

	private function resolve_options( $field ) {
		if ( empty( $field['options'] ) ) {
			return array();
		}
		if ( 'product_cat' === $field['options'] && function_exists( 'sln_product_cat_options' ) ) {
			return sln_product_cat_options();
		}
		if ( 'product_brand' === $field['options'] && function_exists( 'sln_product_brand_options' ) ) {
			return sln_product_brand_options();
		}
		return is_array( $field['options'] ) ? $field['options'] : array();
	}

	private function render_field_input( $key, $field, $value ) {
		if ( null === $value ) {
			$value = isset( $field['default'] ) ? $field['default'] : '';
		}
		switch ( $field['type'] ) {
			case 'textarea':
				echo '<textarea data-field="' . esc_attr( $key ) . '" rows="3" placeholder="' . esc_attr( $field['label'] ) . '">' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'select':
				echo '<select data-field="' . esc_attr( $key ) . '">';
				foreach ( $this->resolve_options( $field ) as $opt_value => $opt_label ) {
					echo '<option value="' . esc_attr( $opt_value ) . '" ' . selected( (string) $value, (string) $opt_value, false ) . '>' . esc_html( $opt_label ) . '</option>';
				}
				echo '</select>';
				break;
			case 'number':
				echo '<input type="number" data-field="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $field['label'] ) . '" />';
				break;
			case 'image':
				?>
				<div class="sln-repeater-image">
					<img class="sln-repeater-image-preview<?php echo $value ? '' : ' sln-hidden'; ?>" src="<?php echo esc_url( $value ); ?>" alt="" />
					<input type="hidden" data-field="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					<div class="sln-repeater-image-buttons">
						<button type="button" class="button sln-repeater-image-select"><?php echo $value ? 'Change image' : 'Select image'; ?></button>
						<button type="button" class="button-link sln-repeater-image-remove<?php echo $value ? '' : ' sln-hidden'; ?>">Remove</button>
					</div>
				</div>
				<?php
				break;
			case 'checkbox':
				echo '<input type="checkbox" data-field="' . esc_attr( $key ) . '" ' . checked( ! empty( $value ), true, false ) . ' />';
				break;
			default:
				echo '<input type="text" data-field="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $field['label'] ) . '" />';
		}
	}

	private function block_title( $block ) {
		if ( ! $this->title_field || ! isset( $block[ $this->title_field ] ) ) {
			return 'Block';
		}
		$field = isset( $this->block_fields[ $this->title_field ] ) ? $this->block_fields[ $this->title_field ] : array();
		$value = $block[ $this->title_field ];
		if ( isset( $field['type'] ) && 'select' === $field['type'] ) {
			$options = $this->resolve_options( $field );
			return isset( $options[ $value ] ) ? $options[ $value ] : ( $value ? $value : 'Block' );
		}
		return $value ? $value : 'Block';
	}

	private function render_block( $block ) {
		?>
		<li class="sln-repeater-block">
			<div class="sln-repeater-block-header">
				<span class="sln-repeater-drag-handle" aria-hidden="true">&#10022;</span>
				<button type="button" class="sln-repeater-block-toggle">
					<span class="sln-repeater-block-title"><?php echo esc_html( $this->block_title( $block ) ); ?></span>
				</button>
				<button type="button" class="sln-repeater-remove" aria-label="Remove">&#10005;</button>
			</div>
			<div class="sln-repeater-block-body">
				<?php foreach ( $this->block_fields as $key => $field ) : ?>
					<label class="sln-repeater-field">
						<span><?php echo esc_html( $field['label'] ); ?></span>
						<?php $this->render_field_input( $key, $field, isset( $block[ $key ] ) ? $block[ $key ] : null ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</li>
		<?php
	}

	public function render_content() {
		$value = json_decode( $this->value(), true );
		if ( ! is_array( $value ) ) {
			$value = array();
		}
		?>
		<label class="customize-control-title"><?php echo esc_html( $this->label ); ?></label>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>
		<ul class="sln-repeater" data-min="<?php echo (int) $this->min_blocks; ?>" data-title-field="<?php echo esc_attr( $this->title_field ); ?>">
			<?php foreach ( $value as $block ) : ?>
				<?php $this->render_block( is_array( $block ) ? $block : array() ); ?>
			<?php endforeach; ?>
		</ul>
		<template class="sln-repeater-template">
			<?php $this->render_block( array() ); ?>
		</template>
		<button type="button" class="button sln-repeater-add"><?php echo esc_html( $this->add_button_text ); ?></button>
		<input type="hidden" class="sln-repeater-value" value="<?php echo esc_attr( $this->value() ); ?>" />
		<?php
	}
}
