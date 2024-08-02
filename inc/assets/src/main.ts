/* eslint-disable lines-around-comment */
import jQuery from 'jquery'
import '@/assets/scss/index.scss'
import {
	finishChapter,
	dynamicWidth,
	responsive,
	tabs,
	coursesProduct,
	toggleContent,
	countdown,
	CommentApp,
} from './events'
;(function ($) {
	// 訂閱放前面
	responsive()

	// classroom 頁面，完成章節
	finishChapter()

	// 改變大小時設定 state
	dynamicWidth()

	// 添加 tabs 組件事件
	tabs()
	coursesProduct()
	toggleContent()
	countdown()

	new CommentApp('#review-app', {
		queryParams: {
			post_type: 'product',
			type: 'review',
		},
		navElement: '#tab-nav-review',
		ratingProps: {
			name: 'course-review',
		},
	})

	new CommentApp('#comment-app', {
		queryParams: {
			post_type: 'product',
			type: 'comment',
		},
		navElement: '#tab-nav-comment',
	})

	// 加入購物車樣式調整
	$(document.body).on(
		'added_to_cart',
		function (event, fragments, cart_hash, Button) {
			const isIcon = Button.hasClass('pc-btn-square')
			if (isIcon) {
				const svgClasses = Button.find('svg').attr('class')
				const svgColor = Button.find('svg').attr('fill')
				Button.addClass(
					'pc-btn-outline border-solid text-primary hover:text-white',
				)
					.removeClass('text-white')
					.html(
						/*html*/
						`<svg xmlns="http://www.w3.org/2000/svg" class="${svgClasses} [&_path]:group-hover:fill-white" viewBox="0 0 24 24" fill="none">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM16.0303 8.96967C16.3232 9.26256 16.3232 9.73744 16.0303 10.0303L11.0303 15.0303C10.7374 15.3232 10.2626 15.3232 9.96967 15.0303L7.96967 13.0303C7.67678 12.7374 7.67678 12.2626 7.96967 11.9697C8.26256 11.6768 8.73744 11.6768 9.03033 11.9697L10.5 13.4393L12.7348 11.2045L14.9697 8.96967C15.2626 8.67678 15.7374 8.67678 16.0303 8.96967Z" fill="${svgColor}"/>
						</svg>`,
					)
			} else {
				Button.addClass(
					'pc-btn-outline border-solid text-primary hover:text-white',
				)
					.removeClass('text-white')
					.html('已加入購物車')
			}
		},
	)
})(jQuery)
