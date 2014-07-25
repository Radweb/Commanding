<?php namespace Radweb\Commanding\Exceptions;

use Radweb\Commanding\Command;

class UnauthorisedCommandException extends CommandBusException {

	public function __construct(Command $command)
	{
		parent::__construct('Unauthorised Command Execution "'.get_class($command).'"');
	}

} 