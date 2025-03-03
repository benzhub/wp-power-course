<?php
/**
 * Single Product Card with price
 */

use J7\PowerCourse\Plugin;

$default_args = [
	'product' => $GLOBALS['course'] ?? null,
];

/**
 * @var array $args
 * @phpstan-ignore-next-line
 */
$args = wp_parse_args( $args, $default_args );

[
	'product' => $product,
] = $args;

if ( ! ( $product instanceof \WC_Product ) ) {
	throw new \Exception( 'product 不是 WC_Product' );
}

$hide_single_course = $product->get_meta( 'hide_single_course' ) ?: 'no';
if ( 'yes' === $hide_single_course ) {
	return;
}

$purchase_note = \wpautop( $product->get_purchase_note() );
$checkout_url  = \wc_get_checkout_url();
$url           = \add_query_arg(
			[
				'add-to-cart' => $product->get_id(),
			],
			$checkout_url
		);

printf(
/*html*/'
<div class="w-full bg-base-100 shadow-lg rounded p-6">
	<h6 class="text-base text-base-content font-semibold text-center">購買單堂課</h6>
	%1$s
	<div class="mt-8">%2$s</div>
	<div class="mt-2">%3$s</div>
	<div class="mt-8 mb-6 text-sm">%4$s</div>
	<div class="flex gap-3">%5$s %6$s</div>
</div>
',
Plugin::load_template( 'divider', null, false ),
Plugin::load_template(
	'price',
	[
		'product' => $product,
	],
	false
),
Plugin::load_template(
	'countdown/sales',
	[
		'product' => $product,
	],
	false
	),
$purchase_note,
Plugin::load_template(
	'button',
	[
		'type'     => 'primary',
		'children' => '立即購買',
		'class'    => 'pc-add-to-cart-link text-white flex-1',
		'href'     => $url,
	],
	false
),
Plugin::load_template(
	'button/add-to-cart',
	[
		'product'       => $product,
		'children'      => '',
		'type'          => 'primary',
		'outline'       => true,
		'icon'          => 'shopping-bag',
		'shape'         => 'square',
		'wrapper_class' => '[&_a.wc-forward]:tw-hidden',
		'class'         => '',
	],
	false
)
);
