<?php


namespace KgBot\WooCommerce\Traits;


use Carbon\CarbonInterface;
use KgBot\WooCommerce\Utils\Model;

trait ApiFiltering
{

	/** @var array */
	protected $wheres = [
		'per_page' => 10,
		'page'     => 1
	];

	/**
	 * @param int $page
	 *
	 * @return $this
	 */
	public function page( int $page = 1 ): self {
		$this->where( 'page', $page );

		return $this;
	}

	/**
	 * Search for resources that only match your criteria, this method can be used in multiple ways
	 * Example 1:
	 * ->where('name', 'Test')
	 *
	 * Example 2:
	 * ->where('name', '=', 'Test')
	 *
	 * Example 3:
	 * ->where('is_active')
	 *
	 * If you use third example it will be sent as `is_active=true`
	 *
	 * @param string      $key
	 * @param string|null $operator
	 * @param mixed       $value
	 *
	 * @return $this
	 */
	public function where( string $key, string $operator = null, $value = null ): self {
		if ( func_num_args() === 1 ) {
			$value = true;

			$operator = '=';
		}

		if ( func_num_args() === 2 ) {
			$value = $operator;

			$operator = '=';
		}

		$this->wheres[ $key ] = [ $operator, $value ];

		return $this;
	}

	/**
	 * @param CarbonInterface $after
	 *
	 * @return $this
	 */
	public function after( CarbonInterface $after ): self {
		$this->where( 'after', $after->toIso8601String() );

		return $this;
	}

	/**
	 * @param CarbonInterface $before
	 *
	 * @return $this
	 */
	public function before( CarbonInterface $before ): self {
		$this->where( 'before', $before->toIso8601String() );

		return $this;
	}

	/**
	 * @param string $context
	 *
	 * @return $this
	 */
	public function context( string $context ): self {
		$this->where( 'context', $context );

		return $this;
	}

	/**
	 * @param string $search
	 *
	 * @return $this
	 */
	public function search( string $search ): self {
		$this->where( 'search', $search );

		return $this;
	}

	/**
	 * Get first item ordered by desired field (or created_at by default)
	 *
	 * @param string $orderBy
	 *
	 * @return Model|null
	 */
	public function first( string $orderBy = 'date' ): ?Model {
		$this->limit( 1 );
		$this->orderBy( $orderBy );
		$this->orderAsc();

		return $this->get()->first();
	}

	/**
	 * How many resources we should load (max 1000, min 1)
	 *
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function limit( int $limit = 100 ): self {
		$this->where( 'per_page', $limit );

		return $this;
	}

	/**
	 * @param string $orderBy
	 *
	 * @return $this
	 */
	public function orderBy( string $orderBy ): self {
		$this->where( 'orderby', $orderBy );

		return $this;
	}

	/**
	 * @return $this
	 */
	public function orderAsc(): self {
		$this->orderDirection();

		return $this;
	}

	/**
	 * @param string $direction
	 *
	 * @return $this
	 */
	public function orderDirection( string $direction = 'asc' ): self {
		$this->where( 'order', $direction );

		return $this;
	}

	/**
	 * Get last created item ordered by desired field (or created_at by default)
	 *
	 * @param string $orderBy
	 *
	 * @return Model|null
	 */
	public function last( string $orderBy = 'date' ): ?Model {
		$this->limit( 1 );
		$this->orderBy( $orderBy );
		$this->orderDesc();

		return $this->get()->first();
	}

	/**
	 * @return $this
	 */
	public function orderDesc(): self {
		$this->orderDirection( 'desc' );

		return $this;
	}

	/**
	 * @param array $filters
	 *
	 * @return string
	 */
	protected function parseFilters( array $filters = [] ): string {

		foreach ( $filters as $filter ) {
			call_user_func_array( [ $this, 'where' ], array_values( $filter ) );
		}


		$urlFilters = '';

		if ( count( $this->wheres ) > 0 ) {
			$i = 1;

			$urlFilters .= '?';

			foreach ( $this->wheres as $key => $filter ) {

				if ( ! is_array( $filter ) ) {
					$sign  = '=';
					$value = $filter;
				} else {
					[ $sign, $value ] = $filter;
				}

				$urlFilters .= $key . $sign . urlencode( $value );

				if ( count( $this->wheres ) > $i ) {

					$urlFilters .= '&';
				}

				$i++;
			}
		}

		return $urlFilters;
	}
}