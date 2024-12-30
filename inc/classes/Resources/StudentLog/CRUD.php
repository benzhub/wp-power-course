<?php
/**
 * 對 wp_pc_student_logs 的 Record
 */

declare ( strict_types=1 );

namespace J7\PowerCourse\Resources\StudentLog;

use J7\PowerCourse\Plugin;
use J7\WpUtils\Classes\General;
use J7\PowerCourse\Utils\Base;
use J7\PowerCourse\PowerEmail\Resources\Email\Trigger\AtHelper;

/**
 * Class CRUD
 */
final class CRUD {
	use \J7\WpUtils\Traits\SingletonTrait;

	/**
	 * 快取前綴
	 *
	 * @var string
	 */
	public $cache_key_group = 'pc_student_log';

	/**
	 * 資料表欄位
	 *
	 * @var array{id: string, user_id: string, course_id: string, chapter_id: string, title: string, content: string, log_type: string, user_ip: string, created_at: string} $schema
	 */
	public array $schema = [
		'id'         => 'int',
		'user_id'    => 'int',
		'course_id'  => 'int',
		'chapter_id' => 'int',
		'title'      => 'string',
		'content'    => 'string',
		'log_type'   => 'string',
		'user_ip'    => 'string',
		'created_at' => 'datetime',
	];

	/**
	 * 資料表名稱
	 *
	 * @var string
	 */
	private string $table_name;

	/**
	 * 建構子
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . Plugin::STUDENT_LOGS_TABLE_NAME;
	}

	/**
	 * 取得學員紀錄列表
	 *
	 * @param array<string, array<string>|string> $where 條件.
	 * @return StudentLog[] 學員紀錄列表.
	 */
	public function get_list( array $where ): array {
		$where_string = Base::get_where_sql( $where );
		$logs         = \wp_cache_get( $where_string, $this->cache_key_group );
		if ( $logs ) {
			// @phpstan-ignore-next-line
			return $logs;
		}

		return $this->db_get_list( $where );
	}

	/**
	 * 從 db 取得學員紀錄列表
	 *
	 * @param array<string, array<string>|string> $where 條件.
	 * @return StudentLog[] 學員紀錄列表.
	 */
	private function db_get_list( array $where ): array {

		$where_string = Base::get_where_sql( $where );

		global $wpdb;
		$result = $wpdb->get_results(
		\wp_unslash( // phpcs:ignore
			$wpdb->prepare(
				'SELECT * FROM %1$s %2$s',
				$this->table_name,
				$where_string,
			)
		)
		);

		$logs = array_values(array_map(fn ( $item ) => StudentLog::instance($item), $result)   );
		\wp_cache_set( $where_string, $logs, $this->cache_key_group );
		return $logs;
	}


	/**
	 * 取得學員紀錄
	 *
	 * @param int $id 紀錄 ID.
	 * @return StudentLog|null 紀錄資料.
	 */
	public function get( int $id ): StudentLog|null {
		$log = \wp_cache_get( $id, $this->cache_key_group );
		if ( $log ) {
			// @phpstan-ignore-next-line
			return $log;
		}
		return $this->db_get( $id );
	}

	/**
	 * 從 db 取得學員紀錄
	 *
	 * @param int $id 紀錄 ID.
	 * @return StudentLog|null 紀錄資料.
	 */
	private function db_get( int $id ): StudentLog|null {
		global $wpdb;
		$result = $wpdb->get_row(
		$wpdb->prepare(
			'SELECT * FROM %1$s WHERE id = %2$d',
			$this->table_name,
			$id,
		)
		);
		if ( !$result ) {
			return null;
		}

		$log = StudentLog::instance( $result );
		\wp_cache_set( $log->id, $log, $this->cache_key_group );
		return $log;
	}

	/**
	 * 新增學員紀錄
	 *
	 * @param array{user_id: numeric-string, course_id: numeric-string, chapter_id?: numeric-string, title: string, content: string, log_type: string} $args 資料.
	 * @return int|false 新增的Log ID 或 false.
	 */
	public function add( array $args ): int|false {
		global $wpdb;
		$result = $wpdb->insert(
		$this->table_name,
		self::validate_args( $args ),
		);

		if ( $result === false ) {
			return false;
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * 更新學員紀錄
	 *
	 * @param int                  $id 紀錄 ID.
	 * @param array<string, mixed> $args 資料.
	 * @return int|false Log ID 或 false.
	 */
	public function update( int $id, array $args ): int|false {
		global $wpdb;
		$result = $wpdb->update(
		$this->table_name,
		self::validate_args( $args ),
		[ 'id' => $id ],
		);

		if ( $result === false ) {
			return false;
		}

		\wp_cache_delete( $id, $this->cache_key_group );

		return $id;
	}

	/**
	 * 刪除學員紀錄
	 *
	 * @param int $id 紀錄 ID.
	 * @return bool 是否成功.
	 */
	public function delete( int $id ): bool {
		global $wpdb;
		$result = $wpdb->delete(
		$this->table_name,
		[ 'id' => $id ],
		);
		\wp_cache_delete( $id, $this->cache_key_group );
		return $result !== false;
	}


	/**
	 * 驗證資料欄位符合 schema
	 *
	 * @param array<string, mixed> $args 資料.
	 * @return array{user_id: int, course_id: int, chapter_id: int, title: string, content: string, log_type: string, user_ip: string, created_at: string} 驗證後的資料.
	 */
	private function validate_args( array $args ): array {
		$parsed_args = array_intersect_key( $args, $this->get_schema() );

		if ( isset( $args['log_type'] ) && ! in_array( $args['log_type'], AtHelper::$allowed_slugs, true ) ) {
			$parsed_args['log_type'] = 'unknown';
			\J7\WpUtils\Classes\ErrorLog::info( $args['log_type'], 'log_type 不在預期內' );
		}

		$parsed_args['created_at'] = \wp_date( 'Y-m-d H:i:s' );
		$parsed_args['user_ip']    = General::get_client_ip();
		// @phpstan-ignore-next-line
		return $parsed_args;
	}

	/**
	 * 取得 schema
	 *
	 * @return array{user_id: string, course_id: string, chapter_id: string, title: string, content: string, log_type: string} schema.
	 */
	private function get_schema(): array {
		$schema = $this->schema;
		unset( $schema['id'] );
		unset( $schema['user_ip'] );
		unset( $schema['created_at'] );
		return $schema;
	}
}
