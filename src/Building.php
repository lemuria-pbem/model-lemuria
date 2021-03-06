<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria;

use JetBrains\PhpStorm\Pure;

/**
 * A building is an artifact than can be entered by units.
 */
interface Building extends Artifact
{
	const IS_FREE = 0;

	const IS_INDEPENDENT = null;

	const IS_UNLIMITED = 0;

	/**
	 * Get the building that must exist as a precondition.
	 */
	public function Dependency(): ?Building;

	/**
	 * Get the additional feed for every person of a unit that has entered the building.
	 */
	#[Pure] public function Feed(): int;

	/**
	 * Get the talent level needed to create the building.
	 */
	#[Pure] public function Talent(): int;

	/**
	 * Get the amount of silver to maintain the building's function.
	 */
	#[Pure] public function Upkeep(): int;

	/**
	 * Get the minimum size the building must have.
	 */
	#[Pure] public function UsefulSize(): int;

	/**
	 * Get the best fitting building for given size of this building.
	 */
	#[Pure] public function correctBuilding(int $size): Building;

	/**
	 * Get the best fitting size for given size of this building.
	 */
	#[Pure] public function correctSize(int $size): int;
}
