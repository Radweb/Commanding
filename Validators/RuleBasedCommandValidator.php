<?php namespace Radweb\Commanding\Validators;

use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Validation\Factory as Validator;
use Radweb\Commanding\Command;

abstract class RuleBasedCommandValidator extends CommandValidator {

	/**
	 * @var Validator
	 */
	private $validator;

	public function __construct(Validator $validator)
	{
		$this->validator = $validator;
		parent::__construct();
	}

	/**
	 * @param $command
	 * @return MessageProviderInterface|void
	 */
	public function validate(Command $command)
	{
		$data = $this->extractDataFromCommand($command);

		$v = $this->validator->make($data, $this->rules());

		if ($v->fails()) return $this->getMessageBag();
	}

	/**
	 * Return an array of rules for the Illuminate Validator, e.g.
	 *
	 * return [
	 *     'name'  => ['required', 'min:3'],
	 *     'email' => ['required', 'email', 'unique:users'],
	 * ];
	 *
	 * @return array
	 */
	abstract protected function rules();

	/**
	 * Extract all public properties from command into key -> value array
	 *
	 * @param $command
	 * @return array
	 */
	protected function extractDataFromCommand($command)
	{
		return get_object_vars($command);
	}

}