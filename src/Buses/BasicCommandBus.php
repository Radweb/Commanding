<?php namespace Radweb\Commanding\Buses;

use Illuminate\Container\Container;
use Radweb\Commanding\Command;
use Radweb\Commanding\CommandBus;
use Radweb\Commanding\CommandTranslator;
use Radweb\Commanding\Exceptions\CommandResolutionException;

class BasicCommandBus implements CommandBus {

	/**
	 * @var \Illuminate\Container\Container
	 */
	private $container;

	/**
	 * @var CommandTranslator
	 */
	private $translator;

	public function __construct(Container $container, CommandTranslator $translator)
	{
		$this->container = $container;
		$this->translator = $translator;
	}

	/**
	 * Execute the command!
	 *
	 * @param Command $command
	 * @throws CommandResolutionException
	 * @return mixed
	 */
	public function execute(Command $command)
	{
		$handler = $this->translator->toHandler($command);

		if (! class_exists($handler)) throw new CommandResolutionException($command, $handler);

		return $this->container->make($handler)->handle($command);
	}

}