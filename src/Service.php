<?php

namespace Git;

use Git\Errors\GitError;
use ZipArchive;

class Service {

	/**
	 * @var
	 */
	protected $handler;

	public function __construct($handler) {
		$this->setHandler($handler);
	}

	/**
	 * Set handler
	 *
	 * @param $handler
	 * @return $this
	 */
	public function setHandler($handler) {
		$this->handler = $handler;

		return $this;
	}

	/**
	 * Return handler
	 *
	 * @return mixed
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * Check if is valid repo .
	 *
	 * @return mixed
	 */
	public function isValid() {
		return $this->getHandler()->isValid();
	}

	/**
	 * Create patch from sha1 to sha2 .
	 *
	 * @param $from_sha
	 * @param $to_sha
	 * @param $filename
	 * @param array $filter
	 * @return array
	 * @throws GitError
	 */
	public function createPatch($from_sha, $to_sha, $filename, array $filter = array()) {
		$diff_files = $this->getHandler()->getDiff(
			$from_sha, $to_sha
		);

		if(! $filter) {
			if( file_exists( STORAGE . DIRECTORY_SEPARATOR . 'filter_patch.json' ) )
				$filter = json_decode( file_get_contents( STORAGE . DIRECTORY_SEPARATOR . 'filter_patch.json' ), true );
		}

		foreach ($diff_files as $key => $diff_file) {

			foreach ($filter['files'] as $wildcard) {
				if( wildcard_match($wildcard, $diff_file['file']) )
					unset($diff_files[$key]);
			}
		}

		if(! $diff_files)
			throw new GitError('There is no difference detected between last version and current state!');

		$out_files = array();

		$zip = new ZipArchive();

		if (! $zip->open($filename, ZipArchive::CREATE) )
			throw new GitError('Cannot create zip archive');

		foreach ($diff_files as $diff_file) {

			$full = CORE . DIRECTORY_SEPARATOR . $diff_file['file'];

			$out_files[] = array(
				'file' => $diff_file['file'],
				'mode' => $diff_file['mode'] == 'D' ? 'delete' : 'copy'
			);

			if( $diff_file['mode'] == 'D' )
				continue;

			if(! file_exists( $full ))
				continue;

			$zip->addFile( $full, $diff_file['file'] );
		}

		$zip->close();

		return $out_files;
	}

	/**
	 * Call git command .
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->getHandler(), $name), $arguments);
	}

}
