<?php namespace Radweb\Commanding;

interface CommandBus {

	/**
	 * Execute the command!
	 *
	 * @param Command $command
	 * @return mixed
	 */
	public function execute(Command $command);

}