<?php
/**
 * Section order, field readers, fonts, stock, and compare helpers.
 */

function sln_get_section_order( $page ) {
	$defs = sln_section_defs();
	if ( ! isset( $defs[ $page ] ) ) {
		return array();
	}

	$default_order = $defs[ $page ]['default_order'];
	$raw           = get_theme_mod( 'sln_' . $page . '_section_order', '' );
	$saved         = array();

	if ( $raw ) {
		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) ) {
			$saved = $decoded;
		}
	}

	if ( empty( $saved ) ) {
		$saved = array();
		foreach ( $default_order as $id ) {
			$saved[] = array( 'id' => $id, 'enabled' => true );
		}
	}

	$known_ids = wp_list_pluck( $saved, 'id' );
	foreach ( $default_order as $id ) {
		if ( ! in_array( $id, $known_ids, true ) ) {
			$saved[] = array( 'id' => $id, 'enabled' => true );
		}
	}

	$enabled = array();
	foreach ( $saved as $row ) {
		if ( ! empty( $row['enabled'] ) && isset( $defs[ $page ]['sections'][ $row['id'] ] ) ) {
			$enabled[] = $row['id'];
		}
	}
	return $enabled;
}

function sln_field( $page, $section, $field ) {
	$defs = sln_section_defs();
	if ( ! isset( $defs[ $page ]['sections'][ $section ]['fields'][ $field ] ) ) {
		return '';
	}
	$def = $defs[ $page ]['sections'][ $section ]['fields'][ $field ];
	if ( 'repeater' === $def['type'] ) {
		return '';
	}
	$value = get_theme_mod( 'sln_' . $page . '_' . $section . '_' . $field, $def['default'] );
	if ( 'checkbox' === $def['type'] ) {
		return (bool) $value;
	}
	return $value;
}

function sln_repeater( $page, $section, $field ) {
	$defs = sln_section_defs();
	if ( ! isset( $defs[ $page ]['sections'][ $section ]['fields'][ $field ] ) ) {
		return array();
	}
	$def          = $defs[ $page ]['sections'][ $section ]['fields'][ $field ];
	$block_fields = isset( $def['block_fields'] ) ? $def['block_fields'] : array();
	$default_json = wp_json_encode( isset( $def['default_blocks'] ) ? $def['default_blocks'] : array() );
	$raw          = get_theme_mod( 'sln_' . $page . '_' . $section . '_' . $field, $default_json );
	$blocks       = json_decode( $raw, true );
	if ( ! is_array( $blocks ) ) {
		$blocks = array();
	}
	foreach ( $blocks as $i => $block ) {
		if ( ! is_array( $block ) ) {
			$blocks[ $i ] = array();
			$block        = array();
		}
		foreach ( $block_fields as $key => $field_def ) {
			if ( ! array_key_exists( $key, $block ) ) {
				$blocks[ $i ][ $key ] = isset( $field_def['default'] ) ? $field_def['default'] : '';
			}
		}
	}
	return $blocks;
}

function sln_render_sections( $page ) {
	foreach ( sln_get_section_order( $page ) as $section_id ) {
		get_template_part( 'template-parts/sections/' . $page . '-' . $section_id );
	}
}

function sln_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines, 'strlen' ) );
}

function sln_paragraphs( $text ) {
	$parts = preg_split( '/\r\n\r\n|\n\n|\r\r/', trim( (string) $text ) );
	return array_values( array_filter( array_map( 'trim', $parts ), 'strlen' ) );
}

function sln_hour_rows( $text ) {
	$rows = array();
	foreach ( sln_lines( $text ) as $line ) {
		$parts  = array_map( 'trim', explode( '|', $line, 2 ) );
		$rows[] = array(
			'day'   => $parts[0],
			'hours' => isset( $parts[1] ) ? $parts[1] : '',
		);
	}
	return $rows;
}

function sln_font_choices() {
	return array(
		'Inter'             => 'Inter (default)',
		'Space Grotesk'     => 'Space Grotesk',
		'Manrope'           => 'Manrope',
		'Poppins'           => 'Poppins',
		'Work Sans'         => 'Work Sans',
		'Roboto'            => 'Roboto',
		'Open Sans'         => 'Open Sans',
		'Lato'              => 'Lato',
		'Montserrat'        => 'Montserrat',
		'Nunito'            => 'Nunito',
		'Nunito Sans'       => 'Nunito Sans',
		'Raleway'           => 'Raleway',
		'Rubik'             => 'Rubik',
		'Karla'             => 'Karla',
		'DM Sans'           => 'DM Sans',
		'Source Sans 3'     => 'Source Sans 3',
		'PT Sans'           => 'PT Sans',
		'Noto Sans'         => 'Noto Sans',
		'Ubuntu'            => 'Ubuntu',
		'Oswald'            => 'Oswald',
		'Barlow'            => 'Barlow',
		'Josefin Sans'      => 'Josefin Sans',
		'Quicksand'         => 'Quicksand',
		'Cabin'             => 'Cabin',
		'Fira Sans'         => 'Fira Sans',
		'Archivo'           => 'Archivo',
		'Outfit'            => 'Outfit',
		'Sora'              => 'Sora',
		'Plus Jakarta Sans' => 'Plus Jakarta Sans',
		'Figtree'           => 'Figtree',
		'IBM Plex Sans'     => 'IBM Plex Sans',
		'Lora'              => 'Lora',
		'Playfair Display'  => 'Playfair Display',
		'Merriweather'      => 'Merriweather',
		'PT Serif'          => 'PT Serif',
		'Crimson Text'      => 'Crimson Text',
		'Libre Baskerville' => 'Libre Baskerville',
		'EB Garamond'       => 'EB Garamond',
		'Cormorant'         => 'Cormorant',
		'Bitter'            => 'Bitter',
		'Zilla Slab'        => 'Zilla Slab',
	);
}

function sln_font_scale_choices() {
	return array(
		'90'  => '90%',
		'100' => '100%',
		'110' => '110%',
		'125' => '125%',
	);
}

function sln_google_fonts_url( $family ) {
	$choices = sln_font_choices();
	if ( ! isset( $choices[ $family ] ) ) {
		$family = 'Inter';
	}
	$families = array(
		'family=' . rawurlencode( $family ) . ':wght@400;500;600;700',
		'family=' . rawurlencode( 'Space Grotesk' ) . ':wght@500;600;700',
		'family=' . rawurlencode( 'JetBrains Mono' ) . ':wght@400;500',
	);
	return 'https://fonts.googleapis.com/css2?' . implode( '&', array_unique( $families ) ) . '&display=swap';
}

function sln_color_vars() {
	return array(
		'--sl-accent' => get_theme_mod( 'sln_color_accent', '#4F8CC9' ),
		'--sl-link'   => get_theme_mod( 'sln_color_link', '#4F8CC9' ),
		'--sl-text'   => get_theme_mod( 'sln_color_text', '#E8EDF5' ),
		'--sl-muted'  => get_theme_mod( 'sln_color_muted', '#98A4B8' ),
		'--sl-bg'     => get_theme_mod( 'sln_color_bg', '#0B0F17' ),
		'--sl-soft'   => get_theme_mod( 'sln_color_soft', '#121926' ),
		'--sl-border' => get_theme_mod( 'sln_color_border', '#243049' ),
	);
}

function sln_mod( $key, $default = '' ) {
	return get_theme_mod( $key, $default );
}

function sln_phone() {
	foreach ( sln_lines( sln_mod( 'sln_footer_phone_email', "(123) 456-7890\nhello@yourstore.com" ) ) as $line ) {
		if ( preg_match( '/\d{3}.*\d{4}/', $line ) ) {
			return $line;
		}
	}
	return '';
}

function sln_email() {
	foreach ( sln_lines( sln_mod( 'sln_footer_phone_email', "(123) 456-7890\nhello@yourstore.com" ) ) as $line ) {
		if ( is_email( $line ) ) {
			return $line;
		}
	}
	return '';
}

function sln_tel_href( $phone ) {
	$digits = preg_replace( '/[^\d+]/', '', (string) $phone );
	return $digits ? 'tel:' . $digits : '';
}

function sln_category_code( $label ) {
	$words = preg_split( '/\s+/', trim( (string) $label ) );
	$code  = '';
	foreach ( $words as $word ) {
		if ( '' === $word ) {
			continue;
		}
		$code .= strtoupper( substr( $word, 0, 1 ) );
		if ( strlen( $code ) >= 2 ) {
			break;
		}
	}
	return $code ? $code : 'CT';
}

function sln_term_live_count( $taxonomy, $slug ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return 0;
	}
	$query_key = 'product_cat' === $taxonomy ? 'category' : $taxonomy;
	$result    = wc_get_products(
		array(
			'status'   => 'publish',
			$query_key => array( $slug ),
			'paginate' => true,
			'limit'    => 1,
		)
	);
	return is_object( $result ) ? (int) $result->total : 0;
}

function sln_product_categories( $product_id, $fields = 'names' ) {
	if ( ! function_exists( 'wc_get_product_terms' ) ) {
		return array();
	}
	$default_id = (int) get_option( 'default_product_cat' );
	$terms      = wc_get_product_terms( $product_id, 'product_cat', array( 'fields' => 'all' ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	$terms = array_values(
		array_filter(
			$terms,
			function ( $term ) use ( $default_id ) {
				return (int) $term->term_id !== $default_id;
			}
		)
	);
	if ( 'names' === $fields ) {
		return wp_list_pluck( $terms, 'name' );
	}
	return $terms;
}

function sln_render_empty_state( $heading, $message ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	?>
	<section class="sl-section">
		<div class="sl-wrap">
			<div class="sl-empty">
				<?php if ( $heading ) : ?><h2><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
				<p><?php echo wp_kses_post( $message ); ?></p>
				<p class="sl-empty-note">Only you can see this. Visitors will not see the section until it has content.</p>
			</div>
		</div>
	</section>
	<?php
}

function sln_stock_state( $product ) {
	$state = 'ok';
	if ( $product ) {
		if ( ! $product->is_in_stock() ) {
			$state = 'out';
		} elseif ( $product->managing_stock() ) {
			$qty = $product->get_stock_quantity();
			$low = $product->get_low_stock_amount();
			if ( '' === $low || null === $low ) {
				$low = get_option( 'woocommerce_notify_low_stock_amount', 2 );
			}
			if ( null !== $qty && $qty <= (int) $low ) {
				$state = 'low';
			}
		}
	}
	return $state;
}

function sln_stock_view( $product ) {
	$state = sln_stock_state( $product );
	$qty   = ( $product && $product->managing_stock() ) ? $product->get_stock_quantity() : null;
	$low   = 2;
	if ( $product && $product->managing_stock() ) {
		$amount = $product->get_low_stock_amount();
		$low    = ( '' === $amount || null === $amount ) ? (int) get_option( 'woocommerce_notify_low_stock_amount', 2 ) : (int) $amount;
	}
	if ( null === $qty ) {
		$percent = 'out' === $state ? 0 : ( 'low' === $state ? 28 : 70 );
		$text    = '';
	} else {
		$max     = max( (int) $qty, max( 1, $low ) * 4, 8 );
		$percent = $qty > 0 ? max( 4, (int) round( $qty / $max * 100 ) ) : 0;
		$text    = (string) (int) $qty;
	}
	$labels = array(
		'ok'  => array( 'class' => 'is-ok', 'key' => 'instock' ),
		'low' => array( 'class' => 'is-low', 'key' => 'low' ),
		'out' => array( 'class' => 'is-out', 'key' => 'out' ),
	);
	return array(
		'state'   => $state,
		'class'   => $labels[ $state ]['class'],
		'percent' => $percent,
		'qty'     => $qty,
		'text'    => $text,
	);
}

function sln_product_chips( $product, $limit = 3 ) {
	$chips = array();
	if ( ! $product ) {
		return $chips;
	}
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
			continue;
		}
		$label = wc_attribute_label( $attribute->get_name() );
		if ( 'brand' === strtolower( $label ) ) {
			continue;
		}
		if ( $attribute->is_taxonomy() ) {
			$names = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
			$value = is_wp_error( $names ) ? '' : implode( ', ', $names );
		} else {
			$value = implode( ', ', $attribute->get_options() );
		}
		if ( '' === $value ) {
			continue;
		}
		$chips[] = $value;
		if ( count( $chips ) >= $limit ) {
			break;
		}
	}
	return $chips;
}

function sln_product_specs( $product, $limit = 6 ) {
	$specs = array();
	if ( ! $product ) {
		return $specs;
	}
	$sku = $product->get_sku();
	if ( $sku ) {
		$specs[] = array( 'label' => sln_field( 'product', 'product_main', 'sku_label' ), 'value' => $sku );
	}
	$cats = sln_product_categories( $product->get_id() );
	if ( $cats ) {
		$specs[] = array( 'label' => sln_field( 'shop', 'filters', 'category_label' ), 'value' => $cats[0] );
	}
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
			continue;
		}
		$label = wc_attribute_label( $attribute->get_name() );
		if ( '' === $label || 'brand' === strtolower( $label ) ) {
			continue;
		}
		if ( $attribute->is_taxonomy() ) {
			$names = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
			$value = is_wp_error( $names ) ? '' : implode( ', ', $names );
		} else {
			$value = implode( ', ', $attribute->get_options() );
		}
		if ( '' === $value ) {
			continue;
		}
		$specs[] = array( 'label' => $label, 'value' => $value );
		if ( count( $specs ) >= $limit ) {
			break;
		}
	}
	return $specs;
}

function sln_compare_details( $product ) {
	$blurb = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
	$blurb = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $blurb ) ) );
	if ( $blurb ) {
		$blurb = wp_trim_words( $blurb, 26, '…' );
	}
	return array(
		'description' => $blurb,
		'specs'       => sln_product_specs( $product, 8 ),
	);
}

function sln_compare_checkbox_attrs( $product ) {
	$brand_terms = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
	if ( is_wp_error( $brand_terms ) ) {
		$brand_terms = array();
	}
	$stock    = sln_stock_view( $product );
	$image_id = $product->get_image_id();
	$details  = sln_compare_details( $product );
	$price    = '';
	if ( function_exists( 'sln_ecommerce_enabled' ) && sln_ecommerce_enabled() && '' !== $product->get_price() ) {
		$price = wp_strip_all_tags( wc_price( $product->get_price() ) );
	}
	$attrs = array(
		'data-id'          => $product->get_id(),
		'data-name'        => $product->get_name(),
		'data-brand'       => $brand_terms ? $brand_terms[0] : '',
		'data-model'       => sln_product_model( $product ),
		'data-price'       => $price,
		'data-image'       => $image_id ? (string) wp_get_attachment_image_url( $image_id, 'medium' ) : '',
		'data-url'         => get_permalink( $product->get_id() ),
		'data-badge'       => $stock['class'],
		'data-qty'         => null === $stock['qty'] ? '' : (string) (int) $stock['qty'],
		'data-percent'     => (string) $stock['percent'],
		'data-description' => $details['description'],
		'data-specs'       => wp_json_encode( $details['specs'] ),
	);
	$html = '';
	foreach ( $attrs as $name => $value ) {
		$html .= sprintf( ' %s="%s"', $name, esc_attr( $value ) );
	}
	return $html;
}

function sln_image_attrs( $attachment_id, $fallback, $attrs = array() ) {
	$alt = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
	if ( '' === $alt ) {
		$attrs['alt'] = $fallback;
	}
	return $attrs;
}

function sln_placeholder( $label ) {
	echo '<div class="sl-ph">' . esc_html( $label ) . '</div>';
}

function sln_product_model( $product ) {
	if ( ! $product ) {
		return '';
	}
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
			continue;
		}
		$label = wc_attribute_label( $attribute->get_name() );
		if ( false === stripos( $label, 'model' ) ) {
			continue;
		}
		if ( $attribute->is_taxonomy() ) {
			$names = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
			$value = is_wp_error( $names ) ? '' : implode( ', ', $names );
		} else {
			$value = implode( ', ', $attribute->get_options() );
		}
		if ( '' !== $value && $value !== $product->get_sku() ) {
			return $value;
		}
	}
	return '';
}

function sln_remove_query_value( $key, $value ) {
	$current = isset( $_GET[ $key ] ) ? (array) wp_unslash( $_GET[ $key ] ) : array();
	$current = array_values(
		array_filter(
			$current,
			static function ( $item ) use ( $value ) {
				return (string) $item !== (string) $value;
			}
		)
	);
	if ( ! $current ) {
		return remove_query_arg( $key );
	}
	return add_query_arg( $key, array_map( 'sanitize_title', $current ) );
}

function sln_link_or_shop( $url ) {
	if ( $url ) {
		return $url;
	}
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
}
