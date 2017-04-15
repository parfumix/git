<?php

namespace Git\Filters;

class StatusFilter implements \Git\FilterAble {

	/**
	 * Filter output .
	 *
	 * @param $output
	 * @return mixed
	 */
	public function filter($output) {
		$out = array();

		foreach ($output as $item) {
			preg_match("/(.+)\\s+(.+)/i", $item, $matches);

			$out[] = array(
				'mode' => trim($matches[1]),
				'file' => trim($matches[2])
			);
		}

		return $out;
	}
}