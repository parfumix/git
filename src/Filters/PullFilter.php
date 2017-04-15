<?php

namespace Git\Filters;

class PullFilter implements \Git\FilterAble {

	/**
	 * Filter output .
	 *
	 * @param $output
	 * @return mixed
	 */
	public function filter($output) {
		return $output;
	}
}