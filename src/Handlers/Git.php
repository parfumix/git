<?php

namespace Git\Handlers;

use Git\Errors\GitError;
use Git\Filters\BranchesFilter;
use Git\Filters\BranchFilter;
use Git\Filters\DiffFilter;
use Git\Filters\PullFilter;
use Git\Filters\ShowFilter;
use Git\Filters\StatusFilter;
use Git\HandlerAble;

class Git implements HandlerAble {

	private $path;

	public function __construct($path) {
		$this->path = $path;
	}

	/**
	 * Check handler status .
	 *
	 * @return mixed
	 */
	public function isValid() {
		try {
			$this->status();

			return true;
		} catch (GitError $e) {

			return false;
		}
	}

	/**
	 * Get status .
	 *
	 * @return mixed
	 * @throws GitError
	 */
	public function status() {
		if(! $output = $this->exec('status', array_merge(func_get_args(), array('--short'))))
			throw new GitError('Cannot get status');

		return (new StatusFilter())
			->filter($output);
	}

	/**
	 * Creates a tag.
	 * @param $name
	 * @return $this
	 * @throws GitError
	 */
	public function createTag($name) {
		if(! $this->tag($name))
			throw new GitError('Cannot create tag');

		return $this;
	}

	/**
	 * Removes tag.
	 * @param $name
	 * @return $this
	 * @throws GitError
	 */
	public function removeTag($name) {
		if(! $this->tag($name, '-d'))
			throw new GitError('Cannot remove tag');

		return $this;
	}

	/**
	 * Returns list of tags in repo.
	 * @return string[]|NULL  NULL => no tags
	 */
	public function getTags() {
		$tags = $this->tag();

		if( ! is_array($tags) )
			$tags = array();

		return $tags;
	}

	/**
	 * Push tags to server .
	 *
	 * @return $this
	 * @throws GitError
	 */
	public function pushTags() {
		if(! $this->push('origin', '--tags'))
			throw new GitError('Failed push tags');

		return $this;
	}

	/**
	 * Get tag hash .
	 *
	 * @param $tag_name
	 * @return $this
	 * @throws GitError
	 */
	public function getTagHash($tag_name) {
		if(! $output = $this->exec('rev-list', array('-n', 1, $tag_name)))
			throw new GitError('Failed get tag hash');

		return array_pop($output);
	}

	/**
	 * Get commit show info .
	 *
	 * @param $sha
	 * @return mixed
	 * @throws GitError
	 */
	public function getCommitShow( $sha ) {
		if(! $output = $this->show('--no-patch', '--pretty=email\::%ae\|name\::%cn\|date\::%ci', $sha))
			throw new GitError('Failed get sha info');

		$output = array_pop($output);

		return (new ShowFilter())
			->filter($output);
	}

	/**
	 * Gets name of current branch
	 * @return string
	 * @throws GitError
	 */
	public function getCurrentBranchName() {
		if(! $output = $this->branch())
			throw new GitError('Internal error');

		return (new BranchFilter)
			->filter($output);
	}

	/**
	 * Returns list of branches in repo.
	 */
	public function getBranches() {
		if(! $output = $this->branch())
			throw new GitError('Internal error');

		return (new BranchesFilter)
			->filter($output);
	}

	/**
	 * Get git remote .
	 *
	 * @return mixed
	 * @throws GitError
	 */
	public function getRemote() {
		if(! $output = $this->remote())
			throw new GitError('Internal error');

		return array_pop($output);
	}


	/**
	 * Get log ..
	 *
	 * @param string $format
	 * @param int $limit
	 * @return mixed
	 * @throws GitError
	 */
	public function getLog($format = '%h', $limit = 20) {
		if(! $output = $this->log( sprintf( '--pretty=format:"%s"', $format ), '-' . $limit ) )
			throw new GitError('Internal error');

		return $output;
	}

	/**
	 * Diff between commits
	 *
	 * @param $sha1
	 * @param $sha2
	 * @return mixed
	 * @throws GitError
	 */
	public function getDiff($sha1, $sha2) {
		if(! $output = $this->diff('--name-status --no-renames', $sha1, $sha2 ) )
			throw new GitError('Internal error');

		return (new DiffFilter)
			->filter($output);
	}

	/**
	 * Adds file(s).
	 * @param  string|string[]
	 * @throws
	 */
	public function addFile($file) {
		// TODO: Implement addFile() method.
	}

	/**
	 * Commits changes
	 * @param  string
	 * @param  string[] param => value
	 * @throws
	 */
	public function commit($message, $params = NULL) {
		// TODO: Implement commit() method.
	}

	/**
	 * Exists changes?
	 * @return bool
	 */
	public function hasChanges() {
		// TODO: Implement hasChanges() method.
	}

	/**
	 * Pull changes from a remote
	 * @return Git
	 * @throws GitError
	 * @internal param $ string|NULL
	 * @internal param array $params
	 */
	public function pull() {
		if(! $output = $this->exec('pull', func_get_args()))
			throw new GitError('Cannot pull data from remote');

		return (new PullFilter())
			->filter($output);
	}

	/**
	 * Push changes to a remote
	 */
	public function push() {
		if(! $output = $this->exec('push', func_get_args()))
			throw new GitError('Cannot push data to remote');

		return (new PullFilter())
			->filter($output);
	}


	/**
	 * Call new function .
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 * @throws GitError
	 */
	public function __call($name, $arguments) {
		return $this->exec($name, $arguments);
	}

	/**
	 * Execute command
	 *
	 * @param $name
	 * @param $arguments
	 * @return bool
	 * @throws GitError
	 */
	public function exec($name, $arguments) {
		if(! in_array($name, $this->getAvailableCommands()))
			throw new GitError('Unavailable command');

		$current_path = getcwd();

		chdir( $this->getPath() );

		$arguments = array_merge(
			array('git', $name),
			$arguments
		);

		exec(implode(' ', $arguments), $output, $ret);

		if($ret != 0)
			return false;

		chdir( $current_path );

		return ! empty($output)
			? $output
			: true;
	}

	/**
	 * Get list of available commands, for secure .
	 *
	 * @return array
	 */
	protected function getAvailableCommands() {
		return array(
			'status',
			'tag',
			'show',
			'log',
			'diff',
			'branch',
			'commit',
			'add',
			'rev-list',
			'remote',
			'pull',
			'push'
		);
	}

	/**
	 * @return mixed
	 */
	protected function getPath() {
		return $this->path;
	}

}