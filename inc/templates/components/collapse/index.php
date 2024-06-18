<?php
/**
 * Collapse
 */

declare(strict_types=1);

namespace J7\PowerCourse\Templates\Components;

use J7\PowerCourse\Resources\Chapter\RegisterCPT;

/**
 * Class FrontEnd
 */
abstract class Collapse {


	/**
	 * Chapter Collaspe
	 *
	 * @param array $props props.
	 * - product : \WC_Product Course Product
	 * @return string
	 * @throws \Exception Invalid Product
	 */
	public static function chapter( ?array $props = array() ): string {

		$product = $props['product'];

		if ( ! ( $product instanceof \WC_Product ) ) {
			throw new \Exception( 'Invalid Product' );
		}

		$args = array(
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'post_parent'    => $product->get_id(),
			'post_status'    => 'publish',
			'post_type'      => RegisterCPT::POST_TYPE,
		);

		$chapters = \get_children( $args );
		ob_start();
		foreach ( $chapters as $chapter_id => $chapter ) :
			$args = array(
				'posts_per_page' => -1,
				'order'          => 'ASC',
				'post_parent'    => $chapter_id,
				'post_status'    => 'publish',
				'post_type'      => RegisterCPT::POST_TYPE,
			);

			$sub_chapters = \get_children( $args );

			?>
	<div class="pc-collapse pc-collapse-arrow rounded-none mb-1">
		<input type="checkbox" />
		<div class="pc-collapse-title text-sm font-semibold bg-gray-200 py-3 flex items-center justify-between">
			<span><?php echo $chapter->post_title; ?></span>
			<span class="text-xs text-gray-400">共 <?php echo count( $sub_chapters ); ?> 個單元</span>
		</div>
			<div class="pc-collapse-content bg-gray-100 p-0">
			<?php foreach ( $sub_chapters as $sub_chapter ) : ?>
				<div class="text-sm border-t-0 border-x-0 border-b border-gray-200 border-solid py-3 flex pl-8 pr-4">
					<div class="w-8 flex justify-center items-start">•</div>
					<div class="flex-1"><?php echo $sub_chapter->post_title; ?></div>
				</div>
				<?php endforeach; ?>
			</div>
	</div>
			<?php
		endforeach;

		$html = ob_get_clean();

		return $html;
	}


	/**
	 * Chapter Collaspe
	 *
	 * @param array $props props.
	 * - product : \WC_Product Course Product
	 * @return string
	 * @throws \Exception Invalid Product.
	 */
	public static function qa( ?array $props = array() ): string {

		$product = $props['product'];

		if ( ! ( $product instanceof \WC_Product ) ) {
			throw new \Exception( 'Invalid Product' );
		}

		$product_id = $product->get_id();

		$qa_list = \get_post_meta( $product_id, 'qa_list', true );

		if ( ! is_array( $qa_list ) ) {
			$qa_list = array();
		}

		ob_start();
		foreach ( $qa_list as $qa ) :
			?>
	<div class="pc-collapse pc-collapse-arrow rounded-none mb-1">
		<input type="checkbox" />
		<div class="pc-collapse-title text-sm font-semibold bg-gray-200 py-3 flex items-center justify-between">
			<span><?php echo $qa['question']; ?></span>
		</div>
			<div class="pc-collapse-content bg-gray-100 p-0">
				<div class="text-xs border-t-0 border-x-0 border-b border-gray-200 border-solid py-6 flex px-8 leading-8">
			<?php echo \wpautop( $qa['answer'] ); ?>
				</div>
			</div>
	</div>
			<?php
		endforeach;

		$html = ob_get_clean();

		return $html;
	}
}
