<?php namespace Radweb\Commanding;

interface CommandAuthenticator {

	/**
	 * @param Command $command
	 * @return boolean
	 */
	public function authenticate(Command $command);

} 