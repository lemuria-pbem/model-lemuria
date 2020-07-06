<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria\Building;

/**
 * The second level of a castle.
 */
final class Fort extends AbstractCastle
{
	public const MAX_SIZE = 9;

	private const DEFENSE = 0;

	private const MIN_SIZE = 2;

	private const TALENT = 2;

	private const WAGE = 12;

	private const CRAFT = 2;

	/**
	 * Get the defense value.
	 *
	 * @return int
	 */
	public function Defense(): int {
		return self::DEFENSE;
	}

	/**
	 * Get the maximum size for this kind of castle.
	 *
	 * @return int
	 */
	public function MaxSize(): int {
		return self::MAX_SIZE;
	}

	/**
	 * Get the minimum size for this kind of castle.
	 *
	 * @return int
	 */
	public function MinSize(): int {
		return self::MIN_SIZE;
	}

	/**
	 * Get the talent level needed to create the building.
	 *
	 * @return int
	 */
	public function Talent(): int {
		return self::TALENT;
	}

	/**
	 * Get the kind of castle when this castle is upgraded.
	 *
	 * @return Castle
	 */
	public function Upgrade(): Castle {
		return self::createCastle(Tower::class);
	}

	/**
	 * Get the wage for peasants and working units.
	 *
	 * @return int
	 */
	public function Wage(): int {
		return self::WAGE;
	}

	/**
	 * Get the minimum skill in Construction to build this building.
	 *
	 * @return int
	 */
	protected function constructionLevel(): int {
		return self::CRAFT;
	}
}
