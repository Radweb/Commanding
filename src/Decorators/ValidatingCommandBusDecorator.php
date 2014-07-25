<?php namespace Radweb\Commanding\Decorators;

use Illuminate\Container\Container;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
use Radweb\Commanding\Command;
use Radweb\Commanding\CommandBus;
use Radweb\Commanding\CommandTranslator;
use Radweb\Commanding\Exceptions\InvalidCommandException;
use Radweb\Commanding\Exceptions;

class ValidatingCommandBusDecorator implements CommandBus {

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
	 * @throws Exceptions\InvalidCommandException
	 * @return mixed
	 */
	public function execute(Command $command)
	{
		$validator = $this->translator->toValidator($command);

		if (class_exists($validator))
		{
			$result = $this->container->make($validator)->validate($command);

			if ($result !== true && $result !== null)
			{
				throw new InvalidCommandException($command, $this->coerceError($result));
			}
		}

		return $this->bus->execute($command);
	}

	/**
	 * @param $result
	 * @return MessageBag
	 */
	private function coerceError($result)
	{
		if (! $result instanceof MessageProviderInterface)
		{
			$result = new MessageBag(['error' => 'Command Validation Error: "' . print_r($result, true) . '"']);
		}

		return $result;
	}

}