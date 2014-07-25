<?php namespace Radweb\Commanding\Exceptions;

use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
use Radweb\Commanding\Command;

class InvalidCommandException extends CommandBusException implements MessageProviderInterface {

	/**
	 * @var \Illuminate\Support\MessageBag
	 */
	protected $errors;

	/**
	 * @param \Radweb\Commanding\Command $command
	 * @param MessageProviderInterface $messageProvider
	 */
	public function __construct(Command $command, MessageProviderInterface $messageProvider)
	{
		$this->errors = $messageProvider->getMessageBag();

		parent::__construct('Invalid Command "'.get_class($command).'"');
	}

	/**
	 * @return MessageBag
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Get the messages for the instance.
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getMessageBag()
	{
		return $this->getErrors();
	}
}
