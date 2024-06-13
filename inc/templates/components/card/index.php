<?php

/**
 * Card
 */

declare(strict_types=1);

namespace J7\PowerCourse\Templates\Components;

use J7\PowerCourse\Templates\Components\Button;
use J7\PowerCourse\Templates\Components\Course;


/**
 * Class FrontEnd
 */
abstract class Card {

	const HR = '<hr class="border-none h-[1px] w-full bg-gray-200 my-4" />';



	/**
	 * Single product Card
	 *
	 * @param array $props props.
	 * - product \WC_Product
	 * @return string
	 */
	public static function single_product( array $props ): string {
		$product = $props['product'] ?? null;
		if ( ! ( $product instanceof \WC_Product ) ) {
			throw new \Exception( 'product 不是 WC_Product' );
		}

		$purchase_note = \wpautop( $product->get_purchase_note() );

		ob_start();
		?>
		<div class="w-full bg-white shadow-lg rounded p-6">
			<h6 class="text-base font-semibold text-center">購買單堂課</h6>
		<?php echo self::HR; ?>

			<div class="my-8">
		<?php
		echo self::price(
			array(
				'product' => $product,
			)
		);
		?>
			</div>

			<div class="mb-6 text-sm">
		<?php echo $purchase_note; ?>
			</div>

			<div class="flex gap-3">
		<?php
		echo Button::base(
			array(
				'children' => '立即購買',
				'class'    => 'w-full',
			)
		);
		?>
		<?php
		echo Button::base(
			array(
				'children' => '',
				'type'     => 'outline',
				'icon'     => 'shopping_bag',
			)
		)
		?>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}


	/**
	 * Group product Card
	 *
	 * @param array $props props.
	 * - products \WC_Product[]
	 * - title string
	 * @return string
	 */
	public static function group_product( array $props ): string {
		$products = $props['products'] ?? null;

		foreach ( $products as $product ) {
			if ( ! ( $product instanceof \WC_Product ) ) {
				throw new \Exception( 'products 不是 WC_Product' );
			}
		}

		$title = $props['title'];

		$purchase_note = \wpautop( $product->get_purchase_note() );

		ob_start();
		?>
		<div class="w-full bg-white shadow-lg rounded p-6">
			<p class="text-xs text-center mb-1 text-red-400">合購優惠</p>
			<h6 class="text-base font-semibold text-center"><?php echo $title; ?></h6>

		<?php echo self::HR; ?>

			<div class="mb-6 text-sm">
		<?php echo $purchase_note; ?>
			</div>



		<?php echo self::HR; ?>

			<div class="flex gap-3 justify-between items-end">
		<?php
		echo self::price(
			array(
				'product' => $products[0],
				'size'    => 'small',
			)
		);
		?>

		<?php
		echo Button::base(
			array(
				'children' => '加入購物車',
				'class'    => 'px-6',
			)
		);
		?>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}


	/**
	 * Price
	 *
	 * @param array|null $props props.
	 * - product \WC_Product
	 * - size 'small' | 'large'
	 *
	 * @return string
	 */
	public static function price( ?array $props = array() ): string {

		$product = $props['product'] ?? null;

		if ( ! ( $product instanceof \WC_Product ) ) {
			throw new \Exception( 'product 不是 WC_Product' );
		}

		$default_props = array(
			'size' => 'large',
		);

		$props = \array_merge( $default_props, $props );

		$regular_price = $product->get_regular_price();
		$sale_price    = $product->get_sale_price();

		$price_html  = '<del aria-hidden="true" class="text-gray-600">' . ( is_numeric( $regular_price ) ? \wc_price( $regular_price ) : $regular_price ) . '</del>';
		$price_html .= '<ins class="text-red-400 text-2xl font-semibold">' . ( is_numeric( $sale_price ) ? \wc_price( $sale_price ) : $sale_price ) . '</ins>';

		ob_start();
		?>
		<div class="flex flex-col">
		<?php echo $price_html; ?>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}
