<?php
/**
 * Page sections. This table drives the Customizer panels, the section
 * order control, and which template part renders.
 *
 * Only regions that exist in this design are listed.
 */

function sln_product_cat_options() {
	$options = array( '' => '— choose a category —' );
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return $options;
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	if ( is_wp_error( $terms ) ) {
		return $options;
	}
	foreach ( $terms as $term ) {
		$options[ $term->slug ] = $term->name;
	}
	return $options;
}

function sln_product_brand_options() {
	$options = array( '' => '— choose a brand —' );
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return $options;
	}
	$terms = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return $options;
	}
	foreach ( $terms as $term ) {
		$options[ $term->slug ] = $term->name;
	}
	return $options;
}

function sln_section_defs() {
	$defs = array(
		'home' => array(
			'label'    => 'Home Page',
			'sections' => array(
				'hero' => array(
					'label'  => 'Hero',
					'fields' => array(
						'highlights' => array(
							'type'            => 'repeater',
							'label'           => 'Live inventory rows (shown on the highlight card)',
							'title_field'     => 'name',
							'add_button_text' => 'Add row',
							'min'             => 0,
							'block_fields'    => array(
								'name'    => array( 'type' => 'text', 'label' => 'Name', 'default' => 'Machine' ),
								'qty'     => array( 'type' => 'text', 'label' => 'Quantity label', 'default' => '0' ),
								'percent' => array( 'type' => 'number', 'label' => 'Bar percent (0–100)', 'default' => 40 ),
								'tone'    => array( 'type' => 'select', 'label' => 'Bar color', 'default' => 'ok', 'options' => array( 'ok' => 'In stock', 'low' => 'Low stock', 'out' => 'Out of stock' ) ),
							),
							'default_blocks'  => array(
								array( 'name' => 'Zero-turn', 'qty' => '12', 'percent' => 60, 'tone' => 'ok' ),
								array( 'name' => 'Chainsaw', 'qty' => '3', 'percent' => 22, 'tone' => 'low' ),
								array( 'name' => 'Walk-behind', 'qty' => '18', 'percent' => 78, 'tone' => 'ok' ),
							),
						),
						'slides' => array(
							'type'            => 'repeater',
							'label'           => 'Slides',
							'title_field'     => 'heading',
							'add_button_text' => 'Add slide',
							'min'             => 1,
							'block_fields'    => array(
								'layout'           => array( 'type' => 'select', 'label' => 'Layout', 'default' => 'split', 'options' => array( 'split' => 'Split (copy left, image right)', 'banner' => 'Full-width banner' ) ),
								'text_align'       => array( 'type' => 'select', 'label' => 'Text alignment (banner layout)', 'default' => 'left', 'options' => array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ) ),
								'eyebrow'          => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Catalog · Updated live from the floor' ),
								'heading'          => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'New slide' ),
								'body'             => array( 'type' => 'textarea', 'label' => 'Body', 'default' => '' ),
								'button_text'      => array( 'type' => 'text', 'label' => 'Primary button text', 'default' => 'Browse Products' ),
								'button_link'      => array( 'type' => 'url', 'label' => 'Primary button link', 'default' => '/shop/' ),
								'button2_text'     => array( 'type' => 'text', 'label' => 'Secondary button text', 'default' => 'Contact the Store' ),
								'button2_link'     => array( 'type' => 'url', 'label' => 'Secondary button link', 'default' => '/contact/' ),
								'image'            => array( 'type' => 'image', 'label' => 'Image', 'default' => '' ),
								'image_label'      => array( 'type' => 'text', 'label' => 'Image placeholder label', 'default' => 'Hero image' ),
								'highlight_label'  => array( 'type' => 'text', 'label' => 'Highlight card label', 'default' => 'Live inventory' ),
								'highlight_badge'  => array( 'type' => 'text', 'label' => 'Highlight card badge', 'default' => 'live' ),
							),
							'default_blocks'  => array(
								array(
									'layout'          => 'split',
									'text_align'      => 'left',
									'eyebrow'         => 'Catalog · Updated live from the floor',
									'heading'         => 'Every machine. Every spec. What is on the floor right now.',
									'body'            => 'Browse outdoor power equipment. Read the spec sheet, check live stock, then call or stop by.',
									'button_text'     => 'Browse Products',
									'button_link'     => '/shop/',
									'button2_text'    => 'Contact the Store',
									'button2_link'    => '/contact/',
									'image'           => '',
									'image_label'     => 'Hero image',
									'highlight_label' => 'Live inventory',
									'highlight_badge' => 'live',
								),
							),
						),
					),
				),
				'finder' => array(
					'label'  => 'Filter Strip',
					'fields' => array(
						'category_label' => array( 'type' => 'text', 'label' => 'Category placeholder', 'default' => 'All categories' ),
						'brand_label'    => array( 'type' => 'text', 'label' => 'Brand placeholder', 'default' => 'All brands' ),
						'stock_label'    => array( 'type' => 'text', 'label' => 'In-stock toggle label', 'default' => 'In stock only' ),
						'button_text'    => array( 'type' => 'text', 'label' => 'Button text', 'default' => 'Show results' ),
						'button_link'    => array( 'type' => 'url', 'label' => 'Button link', 'default' => '/shop/' ),
					),
				),
				'categories' => array(
					'label'  => 'Category List',
					'fields' => array(
						'eyebrow'       => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Shop by category' ),
						'heading'       => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Find the right machine' ),
						'limit'         => array( 'type' => 'number', 'label' => 'Number of categories (0 = all)', 'default' => 8, 'input_attrs' => array( 'min' => 0, 'max' => 60 ) ),
						'count_suffix'  => array( 'type' => 'text', 'label' => 'Count suffix', 'default' => ' products' ),
						'view_all_text' => array( 'type' => 'text', 'label' => 'View-all text (empty hides it)', 'default' => '' ),
						'view_all_link' => array( 'type' => 'url', 'label' => 'View-all link', 'default' => '/shop/' ),
						'collections'   => array(
							'type'            => 'repeater',
							'label'           => 'Categories (empty = every real category)',
							'title_field'     => 'category',
							'add_button_text' => 'Add category',
							'min'             => 0,
							'block_fields'    => array(
								'category' => array( 'type' => 'select', 'label' => 'Category', 'default' => '', 'options' => 'product_cat' ),
								'label'    => array( 'type' => 'text', 'label' => 'Custom label', 'default' => '' ),
								'code'     => array( 'type' => 'text', 'label' => 'Short code (optional)', 'default' => '' ),
								'image'    => array( 'type' => 'image', 'label' => 'Image (optional)', 'default' => '' ),
								'link'     => array( 'type' => 'url', 'label' => 'Custom link (optional)', 'default' => '' ),
							),
							'default_blocks'  => array(),
						),
					),
				),
				'brands' => array(
					'label'  => 'Brand Logos',
					'fields' => array(
						'logos' => array(
							'type'            => 'repeater',
							'label'           => 'Brands (empty = every real brand as text)',
							'title_field'     => 'brand',
							'add_button_text' => 'Add brand',
							'min'             => 0,
							'block_fields'    => array(
								'brand' => array( 'type' => 'select', 'label' => 'Brand', 'default' => '', 'options' => 'product_brand' ),
								'label' => array( 'type' => 'text', 'label' => 'Custom label', 'default' => '' ),
								'image' => array( 'type' => 'image', 'label' => 'Logo image (optional)', 'default' => '' ),
								'link'  => array( 'type' => 'url', 'label' => 'Custom link (optional)', 'default' => '' ),
							),
							'default_blocks'  => array(),
						),
					),
				),
				'featured_products' => array(
					'label'  => 'Featured Products',
					'fields' => array(
						'eyebrow'       => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Featured products' ),
						'heading'       => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'On the floor this week' ),
						'view_all_text' => array( 'type' => 'text', 'label' => 'View-all text', 'default' => 'View all products' ),
						'view_all_link' => array( 'type' => 'url', 'label' => 'View-all link', 'default' => '/shop/' ),
						'count'         => array( 'type' => 'number', 'label' => 'Number of products', 'default' => 4, 'input_attrs' => array( 'min' => 1, 'max' => 24 ) ),
						'link_text'     => array( 'type' => 'text', 'label' => 'Card link text', 'default' => 'View Specs' ),
						'price_note'    => array( 'type' => 'text', 'label' => 'Price note when the site is not selling online', 'default' => 'Price on request' ),
					),
				),
				'compare_teaser' => array(
					'label'  => 'Compare Specs',
					'fields' => array(
						'eyebrow' => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Compare specs' ),
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Side by side, nothing hidden' ),
						'body'    => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Pick up to four machines and see where each one wins. The best value in each row is highlighted.' ),
						'col_1'   => array( 'type' => 'text', 'label' => 'Column 1 heading', 'default' => 'Walk-behind 21"' ),
						'col_2'   => array( 'type' => 'text', 'label' => 'Column 2 heading', 'default' => 'Zero-turn 42"' ),
						'col_3'   => array( 'type' => 'text', 'label' => 'Column 3 heading', 'default' => 'Zero-turn 52"' ),
						'col_4'   => array( 'type' => 'text', 'label' => 'Column 4 heading (empty hides it)', 'default' => '' ),
						'rows'    => array(
							'type'            => 'repeater',
							'label'           => 'Spec rows',
							'title_field'     => 'label',
							'add_button_text' => 'Add row',
							'min'             => 0,
							'block_fields'    => array(
								'label' => array( 'type' => 'text', 'label' => 'Spec', 'default' => 'Spec' ),
								'v1'    => array( 'type' => 'text', 'label' => 'Column 1', 'default' => '' ),
								'v2'    => array( 'type' => 'text', 'label' => 'Column 2', 'default' => '' ),
								'v3'    => array( 'type' => 'text', 'label' => 'Column 3', 'default' => '' ),
								'v4'    => array( 'type' => 'text', 'label' => 'Column 4', 'default' => '' ),
								'best'  => array( 'type' => 'select', 'label' => 'Highlighted column', 'default' => '0', 'options' => array( '0' => 'None', '1' => 'Column 1', '2' => 'Column 2', '3' => 'Column 3', '4' => 'Column 4' ) ),
							),
							'default_blocks'  => array(
								array( 'label' => 'Cutting deck', 'v1' => '21"', 'v2' => '42"', 'v3' => '52"', 'v4' => '', 'best' => '3' ),
								array( 'label' => 'Engine power', 'v1' => '7.5 HP', 'v2' => '22 HP', 'v3' => '25 HP', 'v4' => '', 'best' => '3' ),
								array( 'label' => 'Engine type', 'v1' => 'Gas', 'v2' => 'Gas V-twin', 'v3' => 'Gas V-twin', 'v4' => '', 'best' => '0' ),
								array( 'label' => 'Top speed', 'v1' => '3.5 mph', 'v2' => '7 mph', 'v3' => '8 mph', 'v4' => '', 'best' => '3' ),
								array( 'label' => 'In stock', 'v1' => '18 units', 'v2' => '12 units', 'v3' => '2 units', 'v4' => '', 'best' => '1' ),
							),
						),
					),
				),
				'stats' => array(
					'label'  => 'Inventory Snapshot',
					'fields' => array(
						'items' => array(
							'type'            => 'repeater',
							'label'           => 'Stats',
							'title_field'     => 'label',
							'add_button_text' => 'Add stat',
							'min'             => 0,
							'block_fields'    => array(
								'value' => array( 'type' => 'text', 'label' => 'Value', 'default' => '0' ),
								'label' => array( 'type' => 'text', 'label' => 'Label', 'default' => 'Label' ),
							),
							'default_blocks'  => array(
								array( 'value' => '500+', 'label' => 'units in stock' ),
								array( 'value' => '46', 'label' => 'categories' ),
								array( 'value' => '12', 'label' => 'brands' ),
							),
						),
					),
				),
				'callouts' => array(
					'label'  => 'Service Callouts',
					'fields' => array(
						'eyebrow' => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Service & support' ),
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'After the sale, we are still here' ),
						'items'   => array(
							'type'            => 'repeater',
							'label'           => 'Cards',
							'title_field'     => 'title',
							'add_button_text' => 'Add card',
							'min'             => 0,
							'block_fields'    => array(
								'number' => array( 'type' => 'text', 'label' => 'Number', 'default' => '01' ),
								'title'  => array( 'type' => 'text', 'label' => 'Title', 'default' => 'New card' ),
								'desc'   => array( 'type' => 'textarea', 'label' => 'Description', 'default' => '' ),
								'cta'    => array( 'type' => 'text', 'label' => 'Link text', 'default' => 'Learn more' ),
								'link'   => array( 'type' => 'url', 'label' => 'Link', 'default' => '/contact/' ),
							),
							'default_blocks'  => array(
								array( 'number' => '01', 'title' => 'Service & Repair', 'desc' => 'Factory-trained techs, seasonal tune-ups, and warranty work.', 'cta' => 'Contact service', 'link' => '/service/' ),
								array( 'number' => '02', 'title' => 'Financing Inquiries', 'desc' => 'Ask about dealer financing programs for larger equipment.', 'cta' => 'Ask about financing', 'link' => '/financing/' ),
								array( 'number' => '03', 'title' => 'Delivery & Pickup', 'desc' => 'Local delivery or curbside pickup, arranged with the store.', 'cta' => 'Arrange with us', 'link' => '/contact/' ),
							),
						),
					),
				),
				'about_teaser' => array(
					'label'  => 'About Teaser',
					'fields' => array(
						'eyebrow'     => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'About the store' ),
						'heading'     => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Local dealer, not a warehouse' ),
						'body'        => array( 'type' => 'textarea', 'label' => 'Body (blank line starts a new paragraph)', 'default' => "Family-owned and still on the same floor. Techs are factory-trained and the counter staff know the machines they sell.\n\nThe catalog here mirrors the floor, so what you see is what can go in the truck." ),
						'link_text'   => array( 'type' => 'text', 'label' => 'Link text', 'default' => 'Our story' ),
						'link_href'   => array( 'type' => 'url', 'label' => 'Link', 'default' => '/about/' ),
						'image'       => array( 'type' => 'image', 'label' => 'Photo', 'default' => '' ),
						'image_label' => array( 'type' => 'text', 'label' => 'Placeholder label', 'default' => 'Storefront photo' ),
					),
				),
				'visit' => array(
					'label'  => 'Visit',
					'fields' => array(
						'eyebrow'         => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Visit us' ),
						'heading'         => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Come see it in person' ),
						'name'            => array( 'type' => 'text', 'label' => 'Location name', 'default' => 'Your Store' ),
						'address'         => array( 'type' => 'textarea', 'label' => 'Address', 'default' => "123 Main Street\nYour City, ST 00000" ),
						'hours'           => array( 'type' => 'textarea', 'label' => 'Hours (one per line, Day|Hours)', 'default' => "Monday–Friday|8:00–18:00\nSaturday|8:00–16:00\nSunday|Closed" ),
						'directions_text' => array( 'type' => 'text', 'label' => 'Directions button', 'default' => 'Get Directions' ),
						'directions_link' => array( 'type' => 'url', 'label' => 'Directions link', 'default' => '' ),
						'map_image'       => array( 'type' => 'image', 'label' => 'Map image', 'default' => '' ),
						'map_embed'       => array( 'type' => 'textarea', 'label' => 'Google Maps embed (iframe or embed URL, used instead of the image)', 'default' => '' ),
						'map_label'       => array( 'type' => 'text', 'label' => 'Map placeholder', 'default' => 'Map' ),
					),
				),
				'inquiry' => array(
					'label'  => 'Contact Form',
					'fields' => array(
						'eyebrow'     => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Contact the store' ),
						'heading'     => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Questions about a machine?' ),
						'body'        => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Ask about availability, delivery, or financing. We reply within one business day.' ),
						'name_ph'     => array( 'type' => 'text', 'label' => 'Name placeholder', 'default' => 'Your name' ),
						'contact_ph'  => array( 'type' => 'text', 'label' => 'Email or phone placeholder', 'default' => 'Email or phone' ),
						'message_ph'  => array( 'type' => 'text', 'label' => 'Message placeholder', 'default' => 'Which product or model?' ),
						'button_text' => array( 'type' => 'text', 'label' => 'Button text', 'default' => 'Send message' ),
						'success'     => array( 'type' => 'text', 'label' => 'Success message', 'default' => 'Message received. We will reply within one business day.' ),
					),
				),
			),
			'default_order' => array( 'hero', 'finder', 'categories', 'brands', 'featured_products', 'compare_teaser', 'stats', 'callouts', 'about_teaser', 'visit', 'inquiry' ),
		),

		'shop' => array(
			'label'    => 'Catalog',
			'sections' => array(
				'breadcrumb' => array(
					'label'  => 'Breadcrumb',
					'fields' => array(
						'home_label'    => array( 'type' => 'text', 'label' => 'Home label', 'default' => 'Home' ),
						'current_label' => array( 'type' => 'text', 'label' => 'Current page label', 'default' => 'Products' ),
					),
				),
				'filters' => array(
					'label'  => 'Filters',
					'fields' => array(
						'heading'            => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Filters' ),
						'clear_text'         => array( 'type' => 'text', 'label' => 'Clear text', 'default' => 'Clear all' ),
						'apply_text'         => array( 'type' => 'text', 'label' => 'Mobile apply text (use {count})', 'default' => 'Show {count} results' ),
						'mobile_open'        => array( 'type' => 'text', 'label' => 'Mobile open button', 'default' => 'Filters' ),
						'show_category'      => array( 'type' => 'checkbox', 'label' => 'Show category filter', 'default' => true ),
						'show_brand'         => array( 'type' => 'checkbox', 'label' => 'Show brand filter', 'default' => true ),
						'show_availability'  => array( 'type' => 'checkbox', 'label' => 'Show availability filter', 'default' => true ),
						'show_attributes'    => array( 'type' => 'checkbox', 'label' => 'Show product attribute filters (power, size, and similar)', 'default' => true ),
						'category_label'     => array( 'type' => 'text', 'label' => 'Category label', 'default' => 'Category' ),
						'brand_label'        => array( 'type' => 'text', 'label' => 'Brand label', 'default' => 'Brand' ),
						'availability_label' => array( 'type' => 'text', 'label' => 'Availability label', 'default' => 'Availability' ),
						'instock_label'      => array( 'type' => 'text', 'label' => 'In-stock label', 'default' => 'In stock' ),
						'lowstock_label'     => array( 'type' => 'text', 'label' => 'Low-stock label', 'default' => 'Low stock' ),
						'outofstock_label'   => array( 'type' => 'text', 'label' => 'Out-of-stock label', 'default' => 'Out of stock' ),
					),
				),
				'products_grid' => array(
					'label'  => 'Product Grid',
					'fields' => array(
						'show_second_image'    => array( 'type' => 'checkbox', 'label' => 'Show second image on hover', 'default' => true ),
						'show_exact_inventory' => array( 'type' => 'checkbox', 'label' => 'Show exact inventory', 'default' => true ),
						'heading'           => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'All Products' ),
						'per_page'          => array( 'type' => 'number', 'label' => 'Products per page', 'default' => 12, 'input_attrs' => array( 'min' => 1, 'max' => 48 ) ),
						'count_suffix'      => array( 'type' => 'text', 'label' => 'Count suffix', 'default' => ' results' ),
						'empty_search_text' => array( 'type' => 'textarea', 'label' => 'Empty search text (use {term})', 'default' => 'Nothing matched "{term}". Try a different search or clear the filters.' ),
						'empty_filter_text' => array( 'type' => 'textarea', 'label' => 'Empty filter text', 'default' => 'No products match those filters.' ),
						'sort_name'         => array( 'type' => 'text', 'label' => 'Sort: name', 'default' => 'Sort: Name A–Z' ),
						'sort_brand'        => array( 'type' => 'text', 'label' => 'Sort: brand', 'default' => 'Sort: Brand' ),
						'sort_stock'        => array( 'type' => 'text', 'label' => 'Sort: stock', 'default' => 'Sort: Stock level' ),
						'grid_label'        => array( 'type' => 'text', 'label' => 'Grid label', 'default' => 'Grid' ),
						'list_label'        => array( 'type' => 'text', 'label' => 'List label', 'default' => 'List' ),
						'link_text'         => array( 'type' => 'text', 'label' => 'Card link text', 'default' => 'View Specs' ),
						'price_note'        => array( 'type' => 'text', 'label' => 'Price note when the site is not selling online', 'default' => 'Price on request' ),
						'load_more'         => array( 'type' => 'text', 'label' => 'Load more text', 'default' => 'Load more' ),
					),
				),
			),
			'default_order' => array( 'breadcrumb', 'filters', 'products_grid' ),
		),

		'product' => array(
			'label'    => 'Product Page',
			'sections' => array(
				'breadcrumb' => array(
					'label'  => 'Breadcrumb',
					'fields' => array(
						'home_label' => array( 'type' => 'text', 'label' => 'Home label', 'default' => 'Home' ),
						'shop_label' => array( 'type' => 'text', 'label' => 'Shop label', 'default' => 'Products' ),
					),
				),
				'product_main' => array(
					'label'  => 'Gallery, Inventory & Reserve Form',
					'fields' => array(
						'zoom_label'            => array( 'type' => 'text', 'label' => 'Zoom label', 'default' => 'Zoom' ),
						'main_photo_placeholder' => array( 'type' => 'text', 'label' => 'Photo placeholder', 'default' => 'Product photo' ),
						'stock_location_suffix' => array( 'type' => 'text', 'label' => 'Stock quantity suffix', 'default' => ' units in stock' ),
						'inventory_label'       => array( 'type' => 'text', 'label' => 'Inventory label', 'default' => 'Inventory' ),
						'instock_label'         => array( 'type' => 'text', 'label' => 'In-stock badge', 'default' => 'In stock' ),
						'low_label'             => array( 'type' => 'text', 'label' => 'Low-stock badge', 'default' => 'Low stock' ),
						'out_label'             => array( 'type' => 'text', 'label' => 'Out-of-stock badge', 'default' => 'Out of stock' ),
						'healthy_label'         => array( 'type' => 'text', 'label' => 'Healthy supply label', 'default' => 'Healthy supply' ),
						'limited_label'         => array( 'type' => 'text', 'label' => 'Limited supply label', 'default' => 'Limited supply' ),
						'updated_label'         => array( 'type' => 'text', 'label' => 'Updated label', 'default' => 'Last updated' ),
						'call_text'             => array( 'type' => 'text', 'label' => 'Call button', 'default' => 'Call the Store' ),
						'directions_text'       => array( 'type' => 'text', 'label' => 'Directions button', 'default' => 'Get Directions' ),
						'directions_link'       => array( 'type' => 'url', 'label' => 'Directions link', 'default' => '/contact/#visit' ),
						'price_note'            => array( 'type' => 'text', 'label' => 'Price note when the site is not selling online', 'default' => 'Price on request · MSRP shown in store' ),
						'request_text'          => array( 'type' => 'text', 'label' => 'Request information button', 'default' => 'Request information' ),
						'reserve_heading'       => array( 'type' => 'text', 'label' => 'Request form heading', 'default' => 'Request information' ),
						'reserve_intro'         => array( 'type' => 'textarea', 'label' => 'Request form intro', 'default' => 'Tell us how to reach you and our team will get back to you shortly.' ),
						'outofstock_heading'    => array( 'type' => 'text', 'label' => 'Out-of-stock heading', 'default' => 'Currently unavailable, contact us.' ),
						'outofstock_text'       => array( 'type' => 'textarea', 'label' => 'Out-of-stock text', 'default' => 'This machine is not on the floor right now. Call or write and we will check incoming stock.' ),
						'close_label'           => array( 'type' => 'text', 'label' => 'Close button label', 'default' => 'Close' ),
						'model_label'           => array( 'type' => 'text', 'label' => 'Model label', 'default' => 'Model' ),
						'sku_label'             => array( 'type' => 'text', 'label' => 'SKU label', 'default' => 'SKU' ),
					),
				),
				'details' => array(
					'label'  => 'Description & Specifications',
					'fields' => array(
						'overview_label'      => array( 'type' => 'text', 'label' => 'Overview tab', 'default' => 'Overview' ),
						'description_heading' => array( 'type' => 'text', 'label' => 'Description heading', 'default' => 'Overview' ),
						'specs_label'         => array( 'type' => 'text', 'label' => 'Specifications tab', 'default' => 'Full Specifications' ),
						'specs_heading'       => array( 'type' => 'text', 'label' => 'Specifications heading', 'default' => 'Full Specifications' ),
						'download_text'       => array( 'type' => 'text', 'label' => 'Download spec sheet text', 'default' => 'Download spec sheet' ),
						'features_label'      => array( 'type' => 'text', 'label' => 'Features tab', 'default' => 'Features' ),
						'features_heading'    => array( 'type' => 'text', 'label' => 'Features heading', 'default' => 'Features' ),
						'docs_label'          => array( 'type' => 'text', 'label' => 'Documents tab', 'default' => 'Documents' ),
						'docs_heading'        => array( 'type' => 'text', 'label' => 'Documents heading', 'default' => 'Documents' ),
						'weight_label'        => array( 'type' => 'text', 'label' => 'Weight label', 'default' => 'Weight' ),
					),
				),
				'related' => array(
					'label'  => 'Related Products',
					'fields' => array(
						'heading'   => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Related Products' ),
						'count'     => array( 'type' => 'number', 'label' => 'Number of products', 'default' => 3, 'input_attrs' => array( 'min' => 1, 'max' => 8 ) ),
						'link_text' => array( 'type' => 'text', 'label' => 'Card link text', 'default' => 'View Specs' ),
					),
				),
				'recently_viewed' => array(
					'label'  => 'Recently Viewed',
					'fields' => array(
						'heading'   => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Recently Viewed' ),
						'count'     => array( 'type' => 'number', 'label' => 'Number of products', 'default' => 4, 'input_attrs' => array( 'min' => 1, 'max' => 8 ) ),
						'link_text' => array( 'type' => 'text', 'label' => 'Card link text', 'default' => 'View Specs' ),
					),
				),
			),
			'default_order' => array( 'breadcrumb', 'product_main', 'details', 'related', 'recently_viewed' ),
		),

		'service' => array(
			'label'    => 'Service Page',
			'sections' => array(
				'hero' => array(
					'label'  => 'Hero',
					'fields' => array(
						'home_label'   => array( 'type' => 'text', 'label' => 'Home label', 'default' => 'Home' ),
						'crumb'        => array( 'type' => 'text', 'label' => 'Breadcrumb label', 'default' => 'Service' ),
						'eyebrow'      => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Service & repair' ),
						'heading'      => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Factory-trained techs. Parts on the shelf.' ),
						'body'         => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Warranty and out-of-warranty repair for what we sell, and most of what we do not. Typical turnaround is 5–7 business days in season.' ),
						'button_text'  => array( 'type' => 'text', 'label' => 'Primary button', 'default' => 'Request service' ),
						'button_link'  => array( 'type' => 'url', 'label' => 'Primary link', 'default' => '#sl-service-form' ),
						'button2_text' => array( 'type' => 'text', 'label' => 'Secondary button', 'default' => 'Call the shop' ),
						'button2_link' => array( 'type' => 'url', 'label' => 'Secondary link (empty uses the footer phone)', 'default' => '' ),
						'image'        => array( 'type' => 'image', 'label' => 'Photo', 'default' => '' ),
						'image_label'  => array( 'type' => 'text', 'label' => 'Placeholder', 'default' => 'Service bay photo' ),
					),
				),
				'service_cards' => array(
					'label'  => 'Service Cards',
					'fields' => array(
						'cards' => array(
							'type'            => 'repeater',
							'label'           => 'Cards',
							'title_field'     => 'title',
							'add_button_text' => 'Add card',
							'min'             => 0,
							'block_fields'    => array(
								'number' => array( 'type' => 'text', 'label' => 'Number', 'default' => '01' ),
								'title'  => array( 'type' => 'text', 'label' => 'Title', 'default' => 'New card' ),
								'desc'   => array( 'type' => 'textarea', 'label' => 'Description', 'default' => '' ),
							),
							'default_blocks'  => array(
								array( 'number' => '01', 'title' => 'Seasonal tune-up', 'desc' => 'Oil, filters, blades, plugs, and a full safety check.' ),
								array( 'number' => '02', 'title' => 'Warranty repair', 'desc' => 'Factory-authorized work on the brands we carry.' ),
								array( 'number' => '03', 'title' => 'Parts & accessories', 'desc' => 'Blades, belts, chains, and bars in stock or ordered in.' ),
								array( 'number' => '04', 'title' => 'Pickup & delivery', 'desc' => 'Local pickup and drop-off for larger machines.' ),
							),
						),
					),
				),
				'steps' => array(
					'label'  => 'How a Repair Works',
					'fields' => array(
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'How a repair works' ),
						'steps'   => array(
							'type'            => 'repeater',
							'label'           => 'Steps',
							'title_field'     => 'title',
							'add_button_text' => 'Add step',
							'min'             => 0,
							'block_fields'    => array(
								'number' => array( 'type' => 'text', 'label' => 'Number', 'default' => '1' ),
								'title'  => array( 'type' => 'text', 'label' => 'Title', 'default' => 'New step' ),
								'desc'   => array( 'type' => 'textarea', 'label' => 'Description', 'default' => '' ),
							),
							'default_blocks'  => array(
								array( 'number' => '1', 'title' => 'Contact us', 'desc' => 'Call or send the request form.' ),
								array( 'number' => '2', 'title' => 'Drop off', 'desc' => 'Bring it in or arrange pickup.' ),
								array( 'number' => '3', 'title' => 'Diagnose & quote', 'desc' => 'We confirm the work before starting.' ),
								array( 'number' => '4', 'title' => 'Ready', 'desc' => 'We call when it is ready to collect.' ),
							),
						),
					),
				),
				'request' => array(
					'label'  => 'Request Form',
					'fields' => array(
						'heading'      => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Request service' ),
						'body'         => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Tell us what is wrong. We will confirm a drop-off time by phone or email. No payment is taken online.' ),
						'name_ph'      => array( 'type' => 'text', 'label' => 'Name placeholder', 'default' => 'Name' ),
						'contact_ph'   => array( 'type' => 'text', 'label' => 'Phone or email placeholder', 'default' => 'Phone or email' ),
						'model_ph'     => array( 'type' => 'text', 'label' => 'Model placeholder', 'default' => 'Equipment brand and model' ),
						'message_ph'   => array( 'type' => 'text', 'label' => 'Issue placeholder', 'default' => 'What is the issue?' ),
						'button_text'  => array( 'type' => 'text', 'label' => 'Button text', 'default' => 'Send request' ),
						'success'      => array( 'type' => 'text', 'label' => 'Success message', 'default' => 'Request received. We will confirm a drop-off time.' ),
					),
				),
			),
			'default_order' => array( 'hero', 'service_cards', 'steps', 'request' ),
		),

		'financing' => array(
			'label'    => 'Financing Page',
			'sections' => array(
				'hero' => array(
					'label'  => 'Hero',
					'fields' => array(
						'home_label'   => array( 'type' => 'text', 'label' => 'Home label', 'default' => 'Home' ),
						'crumb'        => array( 'type' => 'text', 'label' => 'Breadcrumb label', 'default' => 'Financing' ),
						'eyebrow'      => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Financing' ),
						'heading'      => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Programs arranged at the store.' ),
						'body'         => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'This page explains the financing options we can start in person. Nothing is applied for or paid on this website.' ),
						'button_text'  => array( 'type' => 'text', 'label' => 'Primary button', 'default' => 'Talk to the store' ),
						'button_link'  => array( 'type' => 'url', 'label' => 'Primary link', 'default' => '/contact/' ),
						'button2_text' => array( 'type' => 'text', 'label' => 'Secondary button', 'default' => 'See the catalog' ),
						'button2_link' => array( 'type' => 'url', 'label' => 'Secondary link (empty uses the shop)', 'default' => '' ),
					),
				),
				'programs' => array(
					'label'  => 'Programs',
					'fields' => array(
						'heading'    => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'What we can walk through' ),
						'disclaimer' => array( 'type' => 'textarea', 'label' => 'Disclaimer', 'default' => 'Programs, rates, and approval are set by the lender and confirmed at the store. This page is informational only.' ),
						'cards'      => array(
							'type'            => 'repeater',
							'label'           => 'Programs',
							'title_field'     => 'title',
							'add_button_text' => 'Add program',
							'min'             => 0,
							'block_fields'    => array(
								'number' => array( 'type' => 'text', 'label' => 'Number', 'default' => '01' ),
								'title'  => array( 'type' => 'text', 'label' => 'Title', 'default' => 'New program' ),
								'desc'   => array( 'type' => 'textarea', 'label' => 'Description', 'default' => '' ),
							),
							'default_blocks'  => array(
								array( 'number' => '01', 'title' => 'In-store financing', 'desc' => 'Applications are completed with a salesperson. Credit decisions are not made on this site.' ),
								array( 'number' => '02', 'title' => 'Seasonal programs', 'desc' => 'Manufacturer offers change through the year. Ask which ones are open before you visit.' ),
								array( 'number' => '03', 'title' => 'Commercial accounts', 'desc' => 'Fleet and commercial buyers can set up terms with the store directly.' ),
							),
						),
					),
				),
				'steps' => array(
					'label'  => 'How It Works',
					'fields' => array(
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'How an inquiry works' ),
						'steps'   => array(
							'type'            => 'repeater',
							'label'           => 'Steps',
							'title_field'     => 'title',
							'add_button_text' => 'Add step',
							'min'             => 0,
							'block_fields'    => array(
								'number' => array( 'type' => 'text', 'label' => 'Number', 'default' => '1' ),
								'title'  => array( 'type' => 'text', 'label' => 'Title', 'default' => 'New step' ),
								'desc'   => array( 'type' => 'textarea', 'label' => 'Description', 'default' => '' ),
							),
							'default_blocks'  => array(
								array( 'number' => '1', 'title' => 'Pick a machine', 'desc' => 'Check the catalog, or tell us the model you have in mind.' ),
								array( 'number' => '2', 'title' => 'Talk to the store', 'desc' => 'Call or send a message. We confirm what is currently offered.' ),
								array( 'number' => '3', 'title' => 'Apply in person', 'desc' => 'Bring identification. The lender reviews the application at the counter.' ),
								array( 'number' => '4', 'title' => 'Take it home', 'desc' => 'Once approved, pickup or delivery is arranged with the store.' ),
							),
						),
					),
				),
				'cta' => array(
					'label'  => 'Closing Note',
					'fields' => array(
						'heading'     => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Have a model in mind?' ),
						'body'        => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Tell us what you are looking at and we will say which programs apply before you drive over.' ),
						'button_text' => array( 'type' => 'text', 'label' => 'Button text', 'default' => 'Contact the store' ),
						'button_link' => array( 'type' => 'url', 'label' => 'Button link', 'default' => '/contact/' ),
					),
				),
			),
			'default_order' => array( 'hero', 'programs', 'steps', 'cta' ),
		),

		'contact' => array(
			'label'    => 'Contact Page',
			'sections' => array(
				'intro' => array(
					'label'  => 'Intro',
					'fields' => array(
						'home_label' => array( 'type' => 'text', 'label' => 'Home label', 'default' => 'Home' ),
						'crumb'      => array( 'type' => 'text', 'label' => 'Breadcrumb label', 'default' => 'Contact' ),
						'eyebrow' => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Contact the store' ),
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Call, write, or stop by.' ),
					),
				),
				'form_info' => array(
					'label'  => 'Form',
					'fields' => array(
						'heading'        => array( 'type' => 'text', 'label' => 'Form heading', 'default' => 'Send a message' ),
						'name_ph'        => array( 'type' => 'text', 'label' => 'Name placeholder', 'default' => 'Name' ),
						'email_ph'       => array( 'type' => 'text', 'label' => 'Email placeholder', 'default' => 'Email' ),
						'topic_label'    => array( 'type' => 'text', 'label' => 'Topic placeholder', 'default' => 'Choose a topic' ),
						'topics'         => array( 'type' => 'textarea', 'label' => 'Topics (one per line)', 'default' => "Product question\nService & repair\nFinancing inquiry\nDelivery & pickup\nOther" ),
						'model_ph'       => array( 'type' => 'text', 'label' => 'Model placeholder', 'default' => 'Model number (optional)' ),
						'message_ph'     => array( 'type' => 'text', 'label' => 'Message placeholder', 'default' => 'How can we help?' ),
						'button_text'    => array( 'type' => 'text', 'label' => 'Button text', 'default' => 'Send message' ),
						'success'        => array( 'type' => 'text', 'label' => 'Success message', 'default' => 'Message received. We will reply within one business day.' ),
						'recipient_email' => array( 'type' => 'text', 'label' => 'Recipient email (defaults to the site admin email)', 'default' => '' ),
						'sender_name'     => array( 'type' => 'text', 'label' => 'Sender name (defaults to the site name)', 'default' => '' ),
						'sender_email'    => array( 'type' => 'text', 'label' => 'Sender email (defaults to the site admin email)', 'default' => '' ),
						'subject'         => array( 'type' => 'text', 'label' => 'Email subject', 'default' => 'New contact message' ),
						'error_text'      => array( 'type' => 'text', 'label' => 'Error text', 'default' => 'The message could not be sent. Please call or email us instead.' ),
					),
				),
				'locations' => array(
					'label'  => 'Locations',
					'fields' => array(
						'phone_label'     => array( 'type' => 'text', 'label' => 'Phone card label', 'default' => 'Phone' ),
						'email_label'     => array( 'type' => 'text', 'label' => 'Email card label', 'default' => 'Email' ),
						'address_label'   => array( 'type' => 'text', 'label' => 'Address card label', 'default' => 'Address' ),
						'phone'           => array( 'type' => 'text', 'label' => 'Phone', 'default' => '(123) 456-7890' ),
						'email'           => array( 'type' => 'text', 'label' => 'Email', 'default' => 'hello@yourstore.com' ),
						'address_card'    => array( 'type' => 'text', 'label' => 'Address card text', 'default' => '123 Main Street, Your City' ),
						'directions_text' => array( 'type' => 'text', 'label' => 'Directions text', 'default' => 'Get Directions' ),
						'map_image'       => array( 'type' => 'image', 'label' => 'Map image', 'default' => '' ),
						'map_embed'       => array( 'type' => 'textarea', 'label' => 'Google Maps embed (iframe or embed URL, used instead of the image)', 'default' => '' ),
						'map_label'       => array( 'type' => 'text', 'label' => 'Map placeholder', 'default' => 'Map' ),
						'items'           => array(
							'type'            => 'repeater',
							'label'           => 'Locations',
							'title_field'     => 'name',
							'add_button_text' => 'Add location',
							'min'             => 0,
							'block_fields'    => array(
								'name'            => array( 'type' => 'text', 'label' => 'Name', 'default' => 'Main Store' ),
								'address'         => array( 'type' => 'textarea', 'label' => 'Address', 'default' => "123 Main Street\nYour City, ST 00000" ),
								'hours'           => array( 'type' => 'textarea', 'label' => 'Hours (Day|Hours per line)', 'default' => "Mon–Fri|8:00–18:00\nSaturday|8:00–16:00\nSunday|Closed" ),
								'directions_link' => array( 'type' => 'url', 'label' => 'Directions link', 'default' => '' ),
							),
							'default_blocks'  => array(
								array(
									'name'            => 'Your Store',
									'address'         => "123 Main Street\nYour City, ST 00000",
									'hours'           => "Mon–Fri|8:00–18:00\nSaturday|8:00–16:00\nSunday|Closed",
									'directions_link' => '',
								),
							),
						),
					),
				),
			),
			'default_order' => array( 'intro', 'form_info', 'locations' ),
		),

		'about' => array(
			'label'    => 'About Page',
			'sections' => array(
				'intro' => array(
					'label'  => 'Intro',
					'fields' => array(
						'home_label'  => array( 'type' => 'text', 'label' => 'Home label', 'default' => 'Home' ),
						'crumb'       => array( 'type' => 'text', 'label' => 'Breadcrumb label', 'default' => 'About' ),
						'eyebrow'     => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'About the store' ),
						'heading'     => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'A local dealer that knows every machine it sells.' ),
						'image'       => array( 'type' => 'image', 'label' => 'Panorama', 'default' => '' ),
						'image_label' => array( 'type' => 'text', 'label' => 'Placeholder', 'default' => 'Showroom photo' ),
					),
				),
				'story' => array(
					'label'  => 'Story',
					'fields' => array(
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Our story' ),
						'body1'   => array( 'type' => 'textarea', 'label' => 'Paragraph 1', 'default' => 'The shop opened as a small repair bay. Customers kept asking us to order the machines too, so we became a dealer.' ),
						'body2'   => array( 'type' => 'textarea', 'label' => 'Paragraph 2', 'default' => 'We are still independent, and we still fix what we sell. This catalog exists so you can check specs and stock before you drive over.' ),
						'stats'   => array(
							'type'            => 'repeater',
							'label'           => 'Stats',
							'title_field'     => 'label',
							'add_button_text' => 'Add stat',
							'min'             => 0,
							'block_fields'    => array(
								'value' => array( 'type' => 'text', 'label' => 'Value', 'default' => '0' ),
								'label' => array( 'type' => 'text', 'label' => 'Label', 'default' => 'Label' ),
							),
							'default_blocks'  => array(
								array( 'value' => '1987', 'label' => 'Year opened' ),
								array( 'value' => '4', 'label' => 'Brands, authorized' ),
								array( 'value' => '9', 'label' => 'Certified techs' ),
								array( 'value' => '500+', 'label' => 'Units on the floor' ),
							),
						),
					),
				),
				'features' => array(
					'label'  => 'Feature Cards',
					'fields' => array(
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'What we stand for' ),
						'cards'   => array(
							'type'            => 'repeater',
							'label'           => 'Cards',
							'title_field'     => 'title',
							'add_button_text' => 'Add card',
							'min'             => 0,
							'block_fields'    => array(
								'title' => array( 'type' => 'text', 'label' => 'Title', 'default' => 'New card' ),
								'desc'  => array( 'type' => 'textarea', 'label' => 'Description', 'default' => '' ),
							),
							'default_blocks'  => array(
								array( 'title' => 'Honest specs', 'desc' => 'Every number in this catalog comes from the manufacturer sheet.' ),
								array( 'title' => 'Real stock', 'desc' => 'Inventory is synced from the floor, not an estimate.' ),
								array( 'title' => 'Fix what we sell', 'desc' => 'Sales and service work out of the same building.' ),
							),
						),
					),
				),
				'team' => array(
					'label'  => 'Team',
					'fields' => array(
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'The team' ),
						'members' => array(
							'type'            => 'repeater',
							'label'           => 'People',
							'title_field'     => 'role',
							'add_button_text' => 'Add person',
							'min'             => 0,
							'block_fields'    => array(
								'name'  => array( 'type' => 'text', 'label' => 'Name', 'default' => 'Team member' ),
								'role'  => array( 'type' => 'text', 'label' => 'Role', 'default' => 'Role' ),
								'image' => array( 'type' => 'image', 'label' => 'Photo', 'default' => '' ),
							),
							'default_blocks'  => array(
								array( 'name' => 'Store owner', 'role' => 'Owner', 'image' => '' ),
								array( 'name' => 'Service lead', 'role' => 'Service manager', 'image' => '' ),
								array( 'name' => 'Parts lead', 'role' => 'Parts counter', 'image' => '' ),
								array( 'name' => 'Sales lead', 'role' => 'Sales', 'image' => '' ),
							),
						),
					),
				),
			),
			'default_order' => array( 'intro', 'story', 'features', 'team' ),
		),

		'brands' => array(
			'label'    => 'Brands Page',
			'sections' => array(
				'intro' => array(
					'label'  => 'Intro',
					'fields' => array(
						'home_label' => array( 'type' => 'text', 'label' => 'Home label', 'default' => 'Home' ),
						'crumb'      => array( 'type' => 'text', 'label' => 'Breadcrumb label', 'default' => 'Brands' ),
						'eyebrow' => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Brands we carry' ),
						'heading' => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Authorized dealer for the names that last' ),
						'body'    => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Factory-authorized sales and warranty service, plus a short list of accessories brands.' ),
					),
				),
				'brand_cards' => array(
					'label'  => 'Brand Cards',
					'fields' => array(
						'products_label' => array( 'type' => 'text', 'label' => 'Products label', 'default' => 'products' ),
						'stock_label'    => array( 'type' => 'text', 'label' => 'Stock label', 'default' => 'units in stock' ),
						'link_text'      => array( 'type' => 'text', 'label' => 'Link text (use {name})', 'default' => 'Browse {name}' ),
						'cards'          => array(
							'type'            => 'repeater',
							'label'           => 'Overrides (empty = every real brand)',
							'title_field'     => 'brand',
							'add_button_text' => 'Add brand',
							'min'             => 0,
							'block_fields'    => array(
								'brand' => array( 'type' => 'select', 'label' => 'Brand', 'default' => '', 'options' => 'product_brand' ),
								'body'  => array( 'type' => 'textarea', 'label' => 'Description', 'default' => '' ),
								'image' => array( 'type' => 'image', 'label' => 'Logo', 'default' => '' ),
							),
							'default_blocks'  => array(),
						),
					),
				),
				'note' => array(
					'label'  => 'Accessories Note',
					'fields' => array(
						'text'  => array( 'type' => 'text', 'label' => 'Note', 'default' => 'Also stocking accessories. Ask us about special orders.' ),
						'aside' => array( 'type' => 'text', 'label' => 'Aside', 'default' => 'Ask us about special orders' ),
					),
				),
			),
			'default_order' => array( 'intro', 'brand_cards', 'note' ),
		),
	);

	foreach ( $defs['home']['sections'] as $section_id => $section ) {
		$fields = isset( $section['fields'] ) ? $section['fields'] : array();
		$defs['home']['sections'][ $section_id ]['fields'] = array_merge(
			array(
				'show' => array(
					'type'    => 'checkbox',
					'label'   => 'Show this section',
					'default' => true,
				),
			),
			$fields
		);
	}

	return $defs;
}
