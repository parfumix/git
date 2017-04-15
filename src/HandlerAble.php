<?php

namespace Git;

interface HandlerAble {

	/**
	 * Return if handler is valid .
	 *
	 * @return boolean
	 */
	public function isValid();

	/**
	 * Creates a tag.
	 * @param  string
	 */
	public function createTag($name);
	
	/**
	 * Removes tag.
	 * @param  string
	 */
	public function removeTag($name);

	/**
	 * Returns list of tags in repo.
	 * @return string[]|NULL  NULL => no tags
	 */
	public function getTags();

	/**
	 * Gets name of current branch
	 * @return string
	 */
	public function getCurrentBranchName();

	/**
	 * Returns list of branches in repo.
	 * @return string[]|NULL  NULL => no branches
	 */
	public function getBranches();

	/**
	 * Adds file(s).
	 * @param  string|string[]
	 * @throws 
	 */
	public function addFile($file);

	/**
	 * Commits changes
	 * @param  string
	 * @param  string[]  param => value
	 * @throws 
	 */
	public function commit($message, $params = NULL);

	/**
	 * Exists changes?
	 * @return bool
	 */
	public function hasChanges();

	/**
	 * Pull changes from a remote
	 * @param  string|NULL
	 * @param  array|NULL
	 * @return self
	 * @throws GitException
	 */
	public function pull();

	/**
	 * Push changes to a remote
	 * @param  string|NULL
	 * @param  array|NULL
	 * @return self
	 * @throws GitException
	 */
	public function push();

}