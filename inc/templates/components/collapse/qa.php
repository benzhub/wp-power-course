<?php
/**
 * QA of the course.
 */

$default_args = [
	'product' => $GLOBALS['product'],
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

$product_id = $product->get_id();

$qa_list = \get_post_meta( $product_id, 'qa_list', true );

if ( ! is_array( $qa_list ) ) {
	$qa_list = [];
}
foreach ( $qa_list as $qa ) :
	printf(
		/*html*/'
	<div class="pc-collapse pc-collapse-arrow rounded-none mb-1">
		<input type="checkbox" checked="checked" />
		<div class="pc-collapse-title text-sm font-semibold bg-gray-100 py-3 flex items-center justify-between">
			<span>%1$s</span>
		</div>
		<div class="pc-collapse-content bg-gray-50 p-0">
			<div class="text-xs border-t-0 border-x-0 border-b border-gray-200 border-solid py-6 flex px-8 leading-7">
				%2$s
			</div>
		</div>
	</div>
',
		$qa['question'],
		\wpautop( $qa['answer'])
	);

	?>

	<?php
endforeach;
