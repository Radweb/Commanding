<?php namespace Radweb\Commanding;

use Illuminate\Support\Contracts\MessageProviderInterface;
use Radweb\Commanding\Exceptions\InvalidCommandException;

interface CommandValidator extends MessageProviderInterface {

	/**
	 * @param $command
	 * @return MessageProviderInterface|void
	 */
	public function validate(Command $command);

} 