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
	cart,
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
	cart()
})(jQuery)
