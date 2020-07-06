<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria;

/**
 * A requirement is a minimum talent level definition.
 */
class Requirement extends Ability
{
	/**
	 * Create a requirement.
	 *
	 * @param Talent $talent
	 * @param int $level
	 */
	public function __construct(Talent $talent, int $level = 1) {
		$experience = Ability::getExperience($level);
		parent::__construct($talent, $experience);
	}
}