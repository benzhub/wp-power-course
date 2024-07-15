<?php
/**
 * Header for course product
 */

use J7\PowerCourse\Templates\Templates;

$default_args = [
	'product'   => $GLOBALS['product'] ?? null,
	'show_link' => false,
];

/**
 * @var array $args
 * @phpstan-ignore-next-line
 */
$args = wp_parse_args( $args, $default_args );

[
	'product' => $product,
	'show_link' => $show_link,
] = $args;

if ( ! ( $product instanceof \WC_Product ) ) {
	throw new \Exception( 'product 不是 WC_Product' );
}

$product_id   = $product->get_id();
$product_name = $product->get_name();
$teacher_ids  = \get_post_meta( $product_id, 'teacher_ids', false );

if ( ! is_array( $teacher_ids ) ) {
	$teacher_ids = [];
}

?>
<div class="flex gap-6 flex-col md:flex-row mb-20">
	<div id="courses-product__feature-video" class="w-full md:w-[55%] px-0 z-30">
		<?php Templates::get( 'course-product/header/feature-video' ); ?>
	</div>

	<div class="w-full md:w-[45%] px-4 md:px-0">
		<div class="mb-2 flex gap-x-4 gap-y-2 flex-wrap">
			<?php
			foreach ( $teacher_ids as $teacher_id ) {
				$teacher = \get_user_by( 'id', $teacher_id );
				Templates::get(
					'user',
					[
						'user' => $teacher,
					]
					);
			}
			?>
		</div>

		<h1 class="mb-[10px] text-xl md:text-4xl md:leading-[3rem] font-semibold">
			<?php echo $product->get_name(); ?>
		</h1>

		<?php

		Templates::get(
			'typography/paragraph/expandable',
			[
				'children' => \wpautop( $product->get_short_description() ),
			]
			);

		$rating       = $product->get_average_rating();
		$review_count = $product->get_review_count();
		Templates::get(
			'rate',
			[
				'show_before' => true,
				'value'       => $rating,
				'total'       => $review_count,
			]
		);

		if ( $show_link ) {
			echo '<div class="mt-6">';
			Templates::get(
				'button',
				[
					'href'     => site_url( 'courses' ) . '/' . $product->get_slug(),
					'children' => '查看課程',
					'class'    => 'w-full text-white',
					'type'     => 'primary',
				]
				);
			echo '</div>';
		}


		?>
	</div>
</div>
