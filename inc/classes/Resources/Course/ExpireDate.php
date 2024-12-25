<?php
/**
 * 課程的觀看期限 ExpireDate
 * 由 Limit 的 get_expire_date 傳入後初始化
 */

declare ( strict_types=1 );

namespace J7\PowerCourse\Resources\Course;

use J7\PowerCourse\Resources\Course\MetaCRUD as AVLCourseMeta;

/**
 * Class ExpireDate
 */
class ExpireDate {

	/**
	 * 是否為"跟隨訂閱"
	 *
	 * @var bool $is_subscription 是否為訂閱
	 */
	public bool $is_subscription = false;

	/**
	 * 訂閱ID
	 *
	 * @var int|null $subscription_id 如果是"跟隨訂閱"，就會有訂閱ID
	 */
	public int|null $subscription_id = null;

	/**
	 * Constructor
	 *
	 * @param int|string $expire_date 到期日 timestamp | subscription_{訂閱id}
	 */
	public function __construct( public int|string $expire_date ) {
		if (\is_numeric($expire_date)) {
			$this->expire_date = (int) $expire_date;
		}

		if (class_exists('WC_Subscription')) {
			$this->set_subscription();
		}
	}

	/**
	 * 取得 ExpireDate 實例
	 *
	 * @param int $course_id 課程ID
	 * @param int $user_id 用戶ID
	 * @return self
	 * @throws \Exception 如果用戶沒有觀看此課程權限
	 */
	public static function instance( int $course_id, int $user_id ): self {
		$expire_date = AVLCourseMeta::get( $course_id, $user_id, 'expire_date', true);

		// $expire_date = "" 如果用戶沒有觀看此課程權限
		if ('' === $expire_date) {
			new self(404); // 秒數 404 課程會顯示已到期
			// throw new \Exception('User does not have permission to view this course');
		}

		return new self($expire_date);
	}

	/**
	 * 是否過期
	 *
	 * @return bool
	 */
	public function is_expired(): bool {
		if (!$this->is_subscription) {
			$expire_date = (int) $this->expire_date;
			// 0 = 無期限，不會過期
			return $expire_date && $expire_date < time();
		}

		$subscription = \wcs_get_subscription($this->subscription_id);
		if (!$subscription) {
			return true;
		}
		return !$subscription->has_status('active');
	}


	/**
	 * 轉換成 array
	 *
	 * @return array{is_subscription: bool, subscription_id: int|null, is_expired: bool, timestamp: int|null}
	 */
	public function to_array(): array {
		return [
			'is_subscription' => $this->is_subscription,
			'subscription_id' => $this->subscription_id,
			'is_expired'      => $this->is_expired(),
			'timestamp'       => $this->is_subscription ? null : $this->expire_date,
		];
	}

	/**
	 * 初始化訂閱
	 *
	 * @return void
	 */
	private function set_subscription(): void {
		$this->is_subscription = str_starts_with( (string) $this->expire_date, 'subscription_');
		if ( $this->is_subscription ) {
			$this->subscription_id = (int) str_replace('subscription_', '', (string) $this->expire_date);
		}
	}
}
