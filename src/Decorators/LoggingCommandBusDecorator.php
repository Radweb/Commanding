<?php namespace Radweb\Commanding\Decorators;

use Psr\Log\LoggerInterface;
use Radweb\Commanding\Command;
use Radweb\Commanding\CommandBus;
use Radweb\Commanding\Exceptions\CommandBusException;

class LoggingCommandBusDecorator implements CommandBus {

	/**
	 * @var CommandBus
	 */
	private $bus;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	private $logger;

	public function __construct(CommandBus $bus, LoggerInterface $logger)
	{
		$this->bus = $bus;
		$this->logger = $logger;
	}

	/**
	 * Execute the command!
	 *
	 * @param Command $command
	 * @throws CommandBusException
	 * @return mixed
	 */
	public function execute(Command $command)
	{
		$this->logger->info('Command Bus: Dispatching "'.get_class($command).'"');

		try
		{
			return $this->bus->execute($command);
		}
		catch (CommandBusException $e)
		{
			$this->logger->info('Command Bus: Raised Exception "'.get_class($e).'"');

			throw $e;
		}
	}

}