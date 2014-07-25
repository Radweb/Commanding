<?php namespace Radweb\Commanding\Exceptions;

use Radweb\Commanding\Command;

class CommandResolutionException extends CommandBusException {

	public function __construct(Command $command, $handlerName)
	{
		parent::__construct('Unable to resolve "'.$handlerName.'" for "'.get_class($command).'" Command');
	}

} 