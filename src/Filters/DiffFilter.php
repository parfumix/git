<?php

namespace Git\Filters;

class DiffFilter implements \Git\FilterAble {

	/**
	 * Filter output .
	 *
	 * @param $output
	 * @return mixed
	 */
	public function filter($output) {
		$files = array();

		foreach ($output as $key => $file) {
			preg_match('/^(\w)\s+(.+)$/i', $file, $matches);

			if( isset($matches[1]) || $matches[2] )
				$files[] = array(
					'mode' => $matches[1],
					'file' => $matches[2]
				);
		}

		return $files;
	}
}