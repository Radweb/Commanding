<?php namespace Radweb\Commanding\Buses;

use Illuminate\Container\Container;
use Radweb\Commanding\Command;
use Radweb\Commanding\CommandBus;
use Radweb\Commanding\CommandTranslator;
use Radweb\Commanding\Exceptions\UnauthorisedCommandException;
use Radweb\Commanding\Exceptions;

class AuthenticatingCommandBus implements CommandBus {

	/**
	 * @var CommandBus
	 */
	private $bus;

	/**
	 * @var \Illuminate\Container\Container
	 */
	private $container;

	/**
	 * @var CommandTranslator
	 */
	private $translator;

	public function __construct(CommandBus $bus, Container $container, CommandTranslator $translator)
	{
		$this->bus = $bus;
		$this->container = $container;
		$this->translator = $translator;
	}

	/**
	 * Execute the command!
	 *
	 * @param Command $command
	 * @throws Exceptions\UnauthorisedCommandException
	 * @return mixed
	 */
	public function execute(Command $command)
	{
		$authenticator = $this->translator->toAuthenticator($command);

		if (class_exists($authenticator))
		{
			$authenticated = $this->container->make($authenticator)->authenticate($command);

			if ($authenticated === false)
			{
				throw new UnauthorisedCommandException($command);
			}
		}

		return $this->bus->execute($command);
	}

}