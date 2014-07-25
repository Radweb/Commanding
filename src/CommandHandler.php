<?php namespace Radweb\Commanding;

interface CommandHandler {

	/**
	 * Execute the given command
	 *
	 * @param Command $command
	 * @return mixed
	 */
	public function handle(Command $command);

} 