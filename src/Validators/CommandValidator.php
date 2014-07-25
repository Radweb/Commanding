<?php namespace Radweb\Commanding\Validators;

use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
use Radweb\Commanding\Command;
use Radweb\Commanding\Exceptions\InvalidCommandException;
use Radweb\Commanding\CommandValidator as CommandValidatorInterface;

abstract class CommandValidator implements CommandValidatorInterface {

	/**
	 * @var \Illuminate\Support\MessageBag
	 */
	private $errors;

	public function __construct()
	{
		$this->errors = new MessageBag;
	}

	/**
	 * Validate the given command here, calling fail() if invalid
	 *
	 * @param $command
	 * @return MessageProviderInterface|void
	 */
	abstract public function validate(Command $command);

	/**
	 * @param $field
	 * @param $message
	 * @return $this
	 */
	protected function addError($field, $message)
	{
		$this->errors->add($field, $message);

		return $this;
	}

	/**
	 * @param $message
	 * @return $this
	 */
	protected function genericError($message)
	{
		return $this->addError('error', $message);
	}

	/**
	 * Get the messages for the instance.
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getMessageBag()
	{
		return $this->errors;
	}

}