<?php

namespace Git\Filters;

class BranchFilter implements \Git\FilterAble {

	/**
	 * Filter output .
	 *
	 * @return mixed
	 */
	public function filter($output) {
		$branch = null;

		foreach ($output as $branch)
			if( preg_match('/\*/', $branch) )
				break;

		$branch = preg_replace('/\*/', '', $branch);

		return trim($branch);
	}
}