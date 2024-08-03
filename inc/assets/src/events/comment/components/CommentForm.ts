import $, { JQuery } from 'jquery'
import { Rating, TRatingProps } from './Rating'
import { site_url } from '../../../utils/'
import { CommentApp } from '../index'

export type TCommentFormProps = {
	ratingProps: Partial<TRatingProps>
	instance: CommentApp
}

export class CommentForm {
	$element: JQuery<HTMLElement>
	props: {
		ratingProps?: Partial<TRatingProps>
		instance: CommentApp
		reply_comment_parent?: string
		reply_comment_type?: 'comment'
	}
	post_id: string // 商品 ID
	rating: Rating

	constructor(element, props) {
		this.$element = $(element)
		this.props = props
		this.post_id = props.instance.post_id
		this.render()
		this.createSubcomponents()
		this.bindEvents()
	}

	bindEvents() {
		this.$element.on('click', '.pc-comment-form__submit', () => this.add())
	}

	validate_email(value) {
		const user_id = this.props.instance.user_id
		const is_user_logged_in = user_id !== 0
		if (is_user_logged_in) {
			return true
		}

		if (!value) {
			this.$element
				.find('.pc-comment-form__message')
				.text('請輸入您的電子郵件')
				.removeClass('text-green-500')
				.addClass('text-red-500')
			return false
		}

		const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
		if (!regex.test(value)) {
			this.$element
				.find('.pc-comment-form__message')
				.text('請輸入有效的電子郵件')
				.removeClass('text-green-500')
				.addClass('text-red-500')
			return false
		}

		return true
	}

	add() {
		const rating = this.$element
			.find(`input[name="${this?.props?.ratingProps?.name}"]:checked`)
			.val()
		const comment_content = this.$element.find('textarea').val()
		const comment_post_ID = this.post_id
		const comment_type = this.props.instance.comment_type
		const comment_author_email = this.$element
			.find('input[name="comment_author_email"]')
			.val()

		// reply params
		const { reply_comment_type, reply_comment_parent } = this.props

		const is_valid_email = this.validate_email(comment_author_email)

		if (!is_valid_email) {
			return
		}

		this.setLoading(true)

		$.ajax({
			url: `${site_url}/wp-json/power-course/comments`,
			type: 'post',
			data: {
				comment_post_ID,
				rating,
				comment_content,
				comment_type: reply_comment_type ?? comment_type,
				comment_author_email,
				comment_parent: reply_comment_parent ?? 0,
			},
			headers: {
				'X-WP-Nonce': (window as any).pc_data?.nonce,
			},
			timeout: 30000,
			success: (response, textStatus, jqXHR) => {
				const { code, message } = response

				if (200 === code) {
					const post_id = this.post_id
					this.$element
						.find('.pc-comment-form__message')
						.text(message)
						.removeClass('text-red-500')
						.addClass('text-green-500')

					this.$element.find('textarea').val('')

					this.props.instance.getComments({
						post_id,
					})
				} else {
					this.$element
						.find('.pc-comment-form__message')
						.text(message)
						.removeClass('text-green-500')
						.addClass('text-red-500')
				}
			},
			error: (error) => {
				console.log('⭐  error:', error)
				const message = error?.responseJSON?.message || '發生錯誤'
				this.$element
					.find('.pc-comment-form__message')
					.text(message)
					.removeClass('text-green-500')
					.addClass('text-red-500')
			},
			complete: (xhr) => {
				this.setLoading(false)
			},
		})
	}

	setLoading(isLoading: boolean) {
		if (isLoading) {
			this.$element.find('.pc-comment-form__submit').addClass('pc-btn-disabled')
			this.$element.find('textarea').attr('disabled', 'disabled')
			this.$element
				.find('.pc-comment-form__submit .pc-loading')
				.removeClass('tw-hidden')
		} else {
			this.$element
				.find('.pc-comment-form__submit')
				.removeClass('pc-btn-disabled')
			this.$element.find('textarea').removeAttr('disabled')
			this.$element
				.find('.pc-comment-form__submit .pc-loading')
				.addClass('tw-hidden')
		}
	}

	destroy() {
		this.$element.remove()
	}

	render() {
		const { instance, reply_comment_type } = this.props
		const user_id = instance.user_id
		const is_user_logged_in = user_id !== 0

		// 未登入要留 email
		const email_field = is_user_logged_in
			? ''
			: '<input type="email" placeholder="請輸入您的電子郵件" class="mb-2 rounded h-10 bg-white focus:bg-white" name="comment_author_email" required />'
		const comment_type = reply_comment_type || instance.comment_type

		let label = 'review' === comment_type ? '評價' : '留言'
		if (reply_comment_type) {
			label = '回覆'
		}

		this.$element.html(/*html*/ `
			<div class="mb-2 rounded ${reply_comment_type ? 'py-4' : 'p-6 bg-gray-100'}">
				<p class="text-gray-800 text-base font-bold mb-0">新增${label}</p>
				<div class="pc-comment-item__rating mb-2"></div>
				${email_field}
				<textarea placeholder="請輸入您的想法" class="mb-2 rounded h-24 bg-white" name="comment_content" rows="4"></textarea>
				<div class="flex justify-end gap-4 items-center">
					<p class="pc-comment-form__message text-sm m-0"></p>
					<button type="button" class="pc-comment-form__submit pc-btn px-4 pc-btn-primary text-white pc-btn-sm"><span class="pc-loading pc-loading-spinner h-4 w-4 tw-hidden"></span>送出</button>
				</div>
			</div>
		`)
	}

	createSubcomponents() {
		const comment_type = this.props.instance.comment_type
		if ('review' === comment_type && this.props.ratingProps) {
			this.rating = new Rating(this.$element.find('.pc-comment-item__rating'), {
				value: 5,
				disabled: false,
				name: 'rating',
				...this.props.ratingProps,
			})
		}
	}
}
