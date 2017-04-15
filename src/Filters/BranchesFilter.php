<?php

namespace Git\Filters;

class BranchesFilter implements \Git\FilterAble {

	/**
	 * Filter output .
	 *
	 * @param $output
	 * @return mixed
	 */
	public function filter($output) {
		foreach ($output as $key => $branch)
			$output[$key] = trim(preg_replace('/\*/', '', $branch));

		return $output;
	}
}