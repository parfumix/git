<?php

namespace Git\Filters;

class ShowFilter implements \Git\FilterAble {

	/**
	 * Filter output .
	 *
	 * @param $output
	 * @return mixed
	 */
	public function filter($output) {
		$parts = explode('|', $output);

		$out = array();

		foreach ($parts as $key => $part) {
			$data = explode('::', $part);
			$out[$data[0]] = $data[1];
		}

		return $out;
	}
}