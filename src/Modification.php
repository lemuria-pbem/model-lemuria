<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria;

use JetBrains\PhpStorm\Pure;

use function Lemuria\sign;
use Lemuria\Exception\LemuriaException;

/**
 * A modification describes a talent bonus or malus.
 */
class Modification extends Ability
{
	private int $sign;

	public function __construct(Talent $talent, int $modification) {
		$this->sign = sign($modification);
		$experience = Ability::getExperience(abs($modification));
		parent::__construct($talent, $experience);
	}

	#[Pure] public function Experience(): int {
		$experience = parent::Experience();
		return $this->sign * $experience;
	}

	/**
	 * Return the number of bonus or malus levels.
	 */
	public function Level(): int {
		$level = parent::Level();
		return $this->sign * $level;
	}

	/**
	 * Return the modified ability.
	 */
	public function getModified(Ability $ability): Ability {
		if ($ability->Talent() !== $this->Talent()) {
			throw new LemuriaException('Talent mismatch.');
		}
		$level = $ability->Level() + $this->Level();
		return new Ability($this->Talent(), Ability::getExperience($level));
	}
}
