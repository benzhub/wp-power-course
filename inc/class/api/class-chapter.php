<?php
/**
 * Chapter API
 */

declare(strict_types=1);

namespace J7\PowerCourse\Api;

use J7\PowerCourse\Plugin;
use J7\PowerCourse\Resources\Chapter\ChapterFactory;
use J7\WpUtils\Classes\WP;
use J7\PowerCourse\Utils\AVLCourseMeta;

/**
 * Class Course
 */
final class Chapter {
	use \J7\WpUtils\Traits\SingletonTrait;
	use \J7\WpUtils\Traits\ApiRegisterTrait;

	/**
	 * APIs
	 *
	 * @var array{endpoint:string,method:string,permission_callback: callable|null }[]
	 * - endpoint: string
	 * - method: 'get' | 'post' | 'patch' | 'delete'
	 * - permission_callback : callable
	 */
	protected $apis = [
		[
			'endpoint'            => 'chapters',
			'method'              => 'post',
			'permission_callback' => null,
		],
		[
			'endpoint'            => 'chapters/sort',
			'method'              => 'post',
			'permission_callback' => null,
		],
		[
			'endpoint'            => 'chapters/(?P<id>\d+)',
			'method'              => 'post',
			'permission_callback' => null,
		],
		[
			'endpoint'            => 'finish-chapters/(?P<id>\d+)',
			'method'              => 'post',
			'permission_callback' => 'is_user_logged_in',
		],
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'rest_api_init', [ $this, 'register_api_chapters' ] );
	}

	/**
	 * Register Course API
	 *
	 * @return void
	 */
	public function register_api_chapters(): void {
		$this->register_apis(
			apis: $this->apis,
			namespace: Plugin::$kebab,
			default_permission_callback: fn() => \current_user_can( 'manage_options' ),
		);
	}

	/**
	 * Post Chapter callback
	 * 創建章節
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 * @phpstan-ignore-next-line
	 */
	public function post_chapters_callback( $request ): \WP_REST_Response|\WP_Error {

		$body_params = $request->get_json_params();

		$body_params = array_map( [ WP::class, 'sanitize_text_field_deep' ], $body_params );

		$create_result = ChapterFactory::create_chapter( $body_params );

		if ( \is_wp_error( $create_result ) ) {
			return $create_result;
		}

		return new \WP_REST_Response(
			[
				'code'    => 'create_success',
				'message' => '新增成功',
				'data'    => [
					'id' => $create_result,
				],
			]
		);
	}


	/**
	 * Post Chapter Sort callback
	 * 處理排序
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 * @phpstan-ignore-next-line
	 */
	public function post_chapters_sort_callback( $request ): \WP_REST_Response|\WP_Error {

		$body_params = $request->get_json_params();

		$body_params = array_map( [ WP::class, 'sanitize_text_field_deep' ], $body_params );

		$sort_result = ChapterFactory::sort_chapters( $body_params );

		if ( \is_wp_error( $sort_result ) ) {
			return $sort_result;
		}

		return new \WP_REST_Response(
			[
				'code'    => 'sort_success',
				'message' => '修改排序成功',
				'data'    => null,
			]
		);
	}

	/**
	 * Patch Chapter callback
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 * @phpstan-ignore-next-line
	 */
	public function post_chapters_with_id_callback( $request ): \WP_REST_Response|\WP_Error {

		$id          = $request['id'];
		$body_params = $request->get_body_params();
		$body_params = array_map( [ WP::class, 'sanitize_text_field_deep' ], $body_params );

		$formatted_params = ChapterFactory::converter( $body_params );

		$update_result = ChapterFactory::update_chapter( $id, $formatted_params );

		if ( \is_wp_error( $update_result ) ) {
			return $update_result;
		}

		return new \WP_REST_Response(
			[
				'code'    => 'update_success',
				'message' => '更新成功',
				'data'    => [
					'id' => $id,
				],
			]
		);
	}

	/**
	 * Delete Chapter callback
	 * 刪除章節
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response
	 * @phpstan-ignore-next-line
	 */
	public function delete_chapters_with_id_callback( $request ): \WP_REST_Response {
		$id            = $request['id'];
		$delete_result = ChapterFactory::delete_chapter( $id );

		if ( ! $delete_result ) {
			return new \WP_REST_Response(
				[
					'code'    => 'delete_failed',
					'message' => '刪除失敗',
					'data'    => [
						'id' => $id,
					],
				],
				400
			);
		}
		return new \WP_REST_Response(
			[
				'code'    => 'delete_success',
				'message' => '刪除成功',
				'data'    => [
					'id' => $id,
				],
			]
		);
	}

	/**
	 * Patch Chapter callback
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 *
	 * @phpstan-ignore-next-line
	 */
	public function post_finish_chapters_with_id_callback( $request ): \WP_REST_Response|\WP_Error {

		$chapter_id  = $request['id'];
		$body_params = $request->get_json_params();
		$body_params = array_map( [ WP::class, 'sanitize_text_field_deep' ], $body_params );

		ob_start();
		var_dump($body_params);
		\J7\WpUtils\Classes\Log::info('' . ob_get_clean());

		WP::include_required_params( $body_params, [ 'course_id' ]);

		$course_id = (int) $body_params['course_id'];

		AVLCourseMeta::add(
			$course_id,
			\get_current_user_id(),
			'finished_chapter_ids',
			$chapter_id
		);

		return new \WP_REST_Response(
			[
				'code'    => '200',
				'message' => '章節已完成',
				'data'    => [
					'chapter_id' => $chapter_id,
					'course_id'  => $course_id,
				],
			]
		);
	}
}

Chapter::instance();
