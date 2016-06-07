<?php
if (!defined('ABSPATH')) {
	die('Access denied.');
}

if (class_exists('Core_Validator')) {
	return;
}

/**
 * The Core_Validator class uses custom validation rules to allow validation.
 *
 * @author Tobias Hellmann <the@neos-it.de>
 * @author Sebastian Weinert <swe@neos-it.de>
 * @author Danny Meißner <dme@neos-it.de>
 *
 * @access public
 */
class Core_Validator
{
	/**
	 * An array of all validation rules registered.
	 *
	 * @var array
	 */
	private $validationRules = array();

	/**
	 * Validate the given data and return a new {@see Core_Validator_Result}.
	 *
	 * @param array $data
	 *
	 * @return Core_Validator_Result
	 */
	public function validate($data)
	{
		$result = new Core_Validator_Result();

		foreach ($this->validationRules as $name => $rules) {
			if (!isset($data[$name])) {
				continue; //TODO Revisit
			}

			$value = $data[$name];

			//TODO Find a better option
			if (is_array($value) && isset($value['option_value'])) {
				$value = $value['option_value'];
			}

			foreach ($rules as $rule) {
				/** @var Core_Validator_Rule $rule */
				$validationResult = $rule->validate($value, $data);

				if (true !== $validationResult) {
					$result->addValidationResult($name, $validationResult);
				}
			}
		}

		return $result;
	}

	/**
	 * Add a new rule to our registered rules.
	 *
	 * @param string $name
	 * @param Core_Validator_Rule $rule
	 */
	public function addRule($name, Core_Validator_Rule $rule)
	{
		if (!isset($this->validationRules[$name])) {
			$this->validationRules[$name] = array();
		}

		$this->validationRules[$name][] = $rule;
	}

	/**
	 * Return the current validation rules.
	 *
	 * @return array
	 */
	public function getValidationRules()
	{
		return $this->validationRules;
	}
}