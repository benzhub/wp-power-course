<?php
/**
 * Classroom > header
 */

use J7\PowerCourse\Utils\Course as CourseUtils;
use J7\PowerCourse\Templates\Templates;
use J7\PowerCourse\Utils\AVLCourseMeta;

/**
 * @var WC_Product $product
 */
global $product;
if ( ! ( $product instanceof \WC_Product ) ) {
	throw new \Exception( 'product 不是 WC_Product' );
}

$name               = $product->get_name();
$product_id         = $product->get_id();
$current_chapter_id = (int) \get_query_var( Templates::CHAPTER_ID );

// finish button html
$user_id                    = \get_current_user_id();
$finished_chapter_ids       = AVLCourseMeta::get($product_id, $user_id, 'finished_chapter_ids');
$is_this_chapter_finished   = in_array( (string) $current_chapter_id, $finished_chapter_ids, true);
$finish_chapter_button_html = '';
if (!$is_this_chapter_finished) {
	$finish_chapter_button_html = sprintf(
		'<button id="finish-chapter__button" data-course-id="%1$s" data-chapter-id="%2$s" class="pc-btn pc-btn-secondary pc-btn-sm px-4">
			我已完成此單元
		</button>
		<dialog id="finish-chapter__dialog" class="pc-modal">
			<div class="pc-modal-box">
				<h3 id="finish-chapter__dialog__title" class="text-lg font-bold"></h3>
				<p id="finish-chapter__dialog__message" class="py-4"></p>
				<div class="pc-modal-action">
					<form method="dialog">
						<button class="pc-btn pc-btn-sm pc-btn-primary text-white px-4">關閉</button>
					</form>
				</div>
			</div>
			<form method="dialog" class="pc-modal-backdrop">
				<button class="opacity-0">close</button>
			</form>
		</dialog>
		',
		$product_id,
		$current_chapter_id
	);


}

// next chapter button html
$chapter_ids     = CourseUtils::get_sub_chapters($product_id, return_ids :true);
$index           = array_search($current_chapter_id, $chapter_ids, true);
$next_chapter_id = $chapter_ids[ $index + 1 ] ?? false;

$next_chapter_button_html = '';
if (count($chapter_ids) > 0) {
	if (false === $next_chapter_id) {
		$next_chapter_button_html = '<button class="pc-btn pc-btn-sm pc-btn-primary px-4  text-white cursor-not-allowed opacity-70" tabindex="-1" role="button" aria-disabled="true">沒有更多單元</button>';
	} else {
		$next_chapter_button_html = sprintf(
			'
		<a href="%1$s">
				<button class="pc-btn pc-btn-primary pc-btn-sm px-4 text-white">
					前往下一單元
					<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
						<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
						<g id="SVGRepo_iconCarrier">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M5.60439 4.23093C4.94586 3.73136 4 4.20105 4 5.02762V18.9724C4 19.799 4.94586 20.2686 5.60439 19.7691L14.7952 12.7967C15.3227 12.3965 15.3227 11.6035 14.7952 11.2033L5.60439 4.23093ZM2 5.02762C2 2.54789 4.83758 1.13883 6.81316 2.63755L16.004 9.60993C17.5865 10.8104 17.5865 13.1896 16.004 14.3901L6.81316 21.3625C4.83758 22.8612 2 21.4521 2 18.9724V5.02762Z" fill="#ffffff"></path>
							<path d="M20 3C20 2.44772 20.4477 2 21 2C21.5523 2 22 2.44772 22 3V21C22 21.5523 21.5523 22 21 22C20.4477 22 20 21.5523 20 21V3Z" fill="#ffffff"></path>
						</g>
					</svg>
				</button>
		</a>
',
			site_url( 'classroom' ) . sprintf(
				'/%1$s/%2$s',
				$product->get_slug(),
				$next_chapter_id,
			)
		);
	}
}

// render
printf(
	'
<div class="py-4 px-6 flex justify-between items-center">
	<h2 class="text-base text-bold tracking-wide my-0">%1$s</h2>
	<div class="flex gap-4">
		%2$s
		%3$s
	</div>
</div>
',
	$name,
	$finish_chapter_button_html,
	$next_chapter_button_html
);
