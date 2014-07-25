<?php namespace Radweb\Commanding;

class CommandTranslator {

	const STRIP_COMMAND_NAME_REGEX = '/(.+)Command$/';

	/**
	 * Translate a Command to the Interactor, eg.
	 * From: InventoryBase\RegisterAccountCommand
	 *   To: InventoryBase\RegisterAccount
	 * 
	 * @param  object $command The Command to translate
	 * @return string          The Interactor class name
	 */
	public function toHandler($command)
	{
		return preg_replace(self::STRIP_COMMAND_NAME_REGEX, '${1}', get_class($command));
	}

	/**
	 * Translate a Command to the Command Validator, eg.
	 * From: InventoryBase\RegisterAccountCommand
	 *   To: InventoryBase\RegisterAccountValidator
	 * 
	 * @param  object $command The Command to translate
	 * @return string          The Command Validator class name
	 */
	public function toValidator($command)
	{
		return preg_replace(self::STRIP_COMMAND_NAME_REGEX, '${1}Validator', get_class($command));
	}

	/**
	 * Translate a Command to the Command Authenticator, eg.
	 * From: InventoryBase\RegisterAccountCommand
	 *   To: InventoryBase\RegisterAccountAuthenticator
	 *
	 * @param  object $command The Command to translate
	 * @return string          The Command Authenticator class name
	 */
	public function toAuthenticator($command)
	{
		return preg_replace(self::STRIP_COMMAND_NAME_REGEX, '${1}Authenticator', get_class($command));
	}

}
