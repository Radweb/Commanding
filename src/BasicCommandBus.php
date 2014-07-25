<?php namespace Radweb\Commanding;

use Illuminate\Container\Container;
use Radweb\Commanding\Exceptions\CommandResolutionException;

class BasicCommandBus implements CommandBus {

	/**
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * @var CommandTranslator
	 */
	protected $translator;

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
		$handlerName = $this->translator->toHandler($command);

		if (! class_exists($handlerName)) throw new CommandResolutionException($command, $handlerName);

		$handler = $this->container->make($handlerName);

		return $this->dispatch($command, $handler);
	}

	/**
	 * @param Command $command
	 * @param CommandHandler $handler
	 * @return mixed
	 */
	protected function dispatch(Command $command, CommandHandler $handler)
	{
		return $handler->handle($command);
	}

}