<?php
/**
 * SEO used only when a dedicated SEO plugin is not active.
 */

function sln_seo_placeholders() {
	return array(
		'123 Main Street',
		'Your City, ST 00000',
		'Your City',
		'(123) 456-7890',
		'hello@yourstore.com',
		'123 Main Street, Your City',
	);
}

function sln_seo_plugin_active() {
	return (bool) (
		defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'SLIM_SEO_VER' )
		|| function_exists( 'the_seo_framework' )
	);
}

function sln_seo_trim( $text, $max = 160 ) {
	$text = html_entity_decode( (string) $text, ENT_QUOTES, 'UTF-8' );
	$text = wp_strip_all_tags( strip_shortcodes( $text ), true );
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
	if ( '' === $text || mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut   = mb_substr( $text, 0, $max - 1 );
	$space = mb_strrpos( $cut, ' ' );
	if ( false !== $space && $space > 40 ) {
		$cut = mb_substr( $cut, 0, $space );
	}
	return rtrim( $cut, " ,.;:-" ) . '…';
}

function sln_seo_description() {
	$text = '';
	if ( is_front_page() ) {
		$text = sln_mod( 'sln_seo_home_description', '' );
		if ( ! $text ) {
			$slides = sln_repeater( 'home', 'hero', 'slides' );
			$text   = ! empty( $slides[0]['body'] ) ? $slides[0]['body'] : ( ! empty( $slides[0]['heading'] ) ? $slides[0]['heading'] : '' );
		}
	} elseif ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			$text = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
			if ( ! sln_seo_trim( $text ) ) {
				$text = $product->get_name();
			}
		}
	} elseif ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		$text = sln_mod( 'sln_seo_shop_description', '' );
		if ( ! $text ) {
			$text = sprintf( 'Browse equipment in stock at %s.', get_bloginfo( 'name' ) );
		}
	} elseif ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$term = get_queried_object();
		$text = term_description();
		if ( ! sln_seo_trim( $text ) && $term ) {
			$text = sprintf( 'Shop %s at %s.', $term->name, get_bloginfo( 'name' ) );
		}
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && has_excerpt( $post ) ) {
			$text = get_the_excerpt( $post );
		} elseif ( $post && ! empty( $post->post_content ) ) {
			$text = $post->post_content;
		}
		if ( $post && ! sln_seo_trim( $text ) && 'page' === $post->post_type ) {
			$map = array(
				'service' => array( 'service', 'hero', 'body' ),
				'contact' => array( 'contact', 'intro', 'heading' ),
				'about'   => array( 'about', 'story', 'body1' ),
				'brands'  => array( 'brands', 'intro', 'body' ),
			);
			if ( isset( $map[ $post->post_name ] ) ) {
				$text = sln_field( $map[ $post->post_name ][0], $map[ $post->post_name ][1], $map[ $post->post_name ][2] );
			}
		}
	}
	if ( ! sln_seo_trim( $text ) ) {
		$text = get_bloginfo( 'description' );
	}
	return sln_seo_trim( $text );
}

function sln_seo_canonical_url() {
	$paged = max( 1, (int) get_query_var( 'paged' ), isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
	$url   = '';
	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_singular() ) {
		$url = get_permalink( get_queried_object_id() );
	} elseif ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		$url = sln_shop_url();
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$link = get_term_link( get_queried_object() );
		$url  = is_wp_error( $link ) ? '' : $link;
	}
	if ( $url && $paged > 1 && ! is_singular() ) {
		$url = add_query_arg( 'paged', $paged, $url );
	}
	return $url;
}

function sln_seo_image() {
	$attachment_id = 0;
	if ( function_exists( 'is_product' ) && is_product() ) {
		$product       = wc_get_product( get_queried_object_id() );
		$attachment_id = $product ? (int) $product->get_image_id() : 0;
	} elseif ( is_singular() && has_post_thumbnail() ) {
		$attachment_id = (int) get_post_thumbnail_id();
	}
	if ( $attachment_id ) {
		$src = wp_get_attachment_image_src( $attachment_id, 'large' );
		if ( $src ) {
			return array( $src[0], (int) $src[1], (int) $src[2] );
		}
	}
	if ( is_front_page() ) {
		$slides = sln_repeater( 'home', 'hero', 'slides' );
		if ( ! empty( $slides[0]['image'] ) ) {
			return array( $slides[0]['image'], 0, 0 );
		}
	}
	foreach ( array( sln_mod( 'sln_seo_share_image', '' ), sln_mod( 'sln_header_logo_image', '' ) ) as $fallback ) {
		if ( $fallback ) {
			return array( $fallback, 0, 0 );
		}
	}
	return null;
}

function sln_print_json_ld( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . "</script>\n";
}

function sln_seo_head() {
	if ( sln_seo_plugin_active() || is_404() || is_admin() ) {
		return;
	}
	$description = sln_seo_description();
	$canonical   = sln_seo_canonical_url();
	$image       = sln_seo_image();
	$is_product  = function_exists( 'is_product' ) && is_product();
	$type        = $is_product ? 'product' : ( is_singular( 'post' ) ? 'article' : 'website' );

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
	if ( $canonical && ! is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
	}

	$og = array(
		'og:locale'    => get_locale(),
		'og:type'      => $type,
		'og:site_name' => get_bloginfo( 'name' ),
		'og:title'     => wp_get_document_title(),
		'og:url'       => $canonical,
	);
	if ( $description ) {
		$og['og:description'] = $description;
	}
	if ( $image ) {
		$og['og:image'] = $image[0];
		if ( $image[1] && $image[2] ) {
			$og['og:image:width']  = (string) $image[1];
			$og['og:image:height'] = (string) $image[2];
		}
	}
	if ( $is_product ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product && '' !== (string) $product->get_price() ) {
			$og['product:price:amount']   = wc_format_decimal( $product->get_price(), wc_get_price_decimals() );
			$og['product:price:currency'] = get_woocommerce_currency();
			$og['product:availability']   = $product->is_in_stock() ? 'in stock' : 'out of stock';
		}
	}
	foreach ( $og as $property => $content ) {
		if ( '' !== (string) $content ) {
			echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
		}
	}
	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( wp_get_document_title() ) . '" />' . "\n";
	if ( $description ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image[0] ) . '" />' . "\n";
	}

	if ( is_front_page() ) {
		sln_print_json_ld( sln_seo_business_schema() );
		sln_print_json_ld(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'WebSite',
				'name'            => get_bloginfo( 'name' ),
				'url'             => home_url( '/' ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => home_url( '/?post_type=product&s={search_term_string}' ),
					'query-input' => 'required name=search_term_string',
				),
			)
		);
	}

	if ( $is_product ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			sln_print_json_ld( sln_seo_product_schema( $product ) );
		}
	}
}
add_action( 'wp_head', 'sln_seo_head', 2 );

function sln_seo_business_schema() {
	$placeholders = sln_seo_placeholders();
	$type         = sln_sanitize_business_type( sln_mod( 'sln_seo_business_type', 'Store' ) );
	$schema       = array(
		'@context' => 'https://schema.org',
		'@type'    => $type ? $type : 'Store',
		'@id'      => home_url( '/#business' ),
		'name'     => sln_mod( 'sln_footer_company_name', get_bloginfo( 'name' ) ),
		'url'      => home_url( '/' ),
	);
	$logo = sln_mod( 'sln_header_logo_image', '' );
	if ( $logo ) {
		$schema['logo']  = $logo;
		$schema['image'] = $logo;
	}
	foreach ( sln_lines( sln_mod( 'sln_footer_phone_email', '' ) ) as $line ) {
		if ( in_array( $line, $placeholders, true ) ) {
			continue;
		}
		if ( is_email( $line ) ) {
			$schema['email'] = $line;
		} elseif ( preg_match( '/\d{3}.*\d{4}/', $line ) ) {
			$schema['telephone'] = $line;
		}
	}
	$address = array_values( array_diff( sln_lines( sln_mod( 'sln_footer_address', '' ) ), $placeholders ) );
	if ( $address ) {
		$postal = array(
			'@type'         => 'PostalAddress',
			'streetAddress' => $address[0],
		);
		if ( isset( $address[1] ) && preg_match( '/^(.+?),\s*([A-Za-z .]+?)\s+([A-Za-z0-9 -]{3,10})$/', $address[1], $m ) ) {
			if ( ! in_array( trim( $m[1] ), $placeholders, true ) ) {
				$postal['addressLocality'] = trim( $m[1] );
				$postal['addressRegion']   = trim( $m[2] );
				$postal['postalCode']      = trim( $m[3] );
			}
		}
		$schema['address'] = $postal;
	}
	$profiles = array();
	foreach ( sln_lines( sln_mod( 'sln_seo_social_profiles', '' ) ) as $url ) {
		$url = esc_url_raw( $url );
		if ( $url ) {
			$profiles[] = $url;
		}
	}
	if ( $profiles ) {
		$schema['sameAs'] = $profiles;
	}
	return $schema;
}

function sln_seo_product_schema( $product ) {
	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $product->get_name(),
		'url'         => get_permalink( $product->get_id() ),
		'description' => sln_seo_trim( $product->get_short_description() ? $product->get_short_description() : $product->get_description(), 300 ),
		'sku'         => $product->get_sku(),
	);
	$image_id = $product->get_image_id();
	if ( $image_id ) {
		$schema['image'] = wp_get_attachment_image_url( $image_id, 'large' );
	}
	$brands = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
	if ( $brands && ! is_wp_error( $brands ) ) {
		$schema['brand'] = array( '@type' => 'Brand', 'name' => $brands[0] );
	}
	if ( '' !== (string) $product->get_price() ) {
		$schema['offers'] = array(
			'@type'         => 'Offer',
			'priceCurrency' => get_woocommerce_currency(),
			'price'         => wc_format_decimal( $product->get_price(), wc_get_price_decimals() ),
			'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'url'           => get_permalink( $product->get_id() ),
		);
	}
	return $schema;
}

function sln_seo_robots( $robots ) {
	if ( ! sln_seo_plugin_active() && is_search() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'sln_seo_robots' );

function sln_disable_wc_product_schema( $markup ) {
	if ( ! sln_seo_plugin_active() ) {
		return array();
	}
	return $markup;
}
add_filter( 'woocommerce_structured_data_product', 'sln_disable_wc_product_schema' );

function sln_breadcrumb( $items ) {
	if ( ! $items ) {
		return;
	}
	$last = count( $items ) - 1;
	echo '<nav class="sl-crumbs" aria-label="Breadcrumb"><ol>';
	foreach ( $items as $i => $item ) {
		echo '<li>';
		if ( $i ) {
			echo '<span aria-hidden="true">/</span>';
		}
		if ( $i < $last && ! empty( $item['url'] ) ) {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		} else {
			echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';

	if ( sln_seo_plugin_active() ) {
		return;
	}
	$list = array();
	foreach ( $items as $i => $item ) {
		$entry = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['label'],
		);
		$url = ! empty( $item['url'] ) ? $item['url'] : ( $i === $last ? sln_seo_canonical_url() : '' );
		if ( $url ) {
			$entry['item'] = $url;
		}
		$list[] = $entry;
	}
	sln_print_json_ld(
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list,
		)
	);
}
