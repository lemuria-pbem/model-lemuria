<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria;

use Lemuria\Entity;
use Lemuria\EntitySet;
use Lemuria\Id;

/**
 * The estate in a region is the list of constructions that have been build there.
 */
class Estate extends EntitySet
{
	/**
	 * Add a construction.
	 *
	 * @param Construction $construction
	 * @return Estate
	 */
	public function add(Construction $construction): self {
		parent::addEntity($construction->Id());
		if ($this->hasCollector()) {
			$construction->addCollector($this->collector());
		}
		return $this;
	}

	/**
	 * Remove a construction.
	 *
	 * @param Construction $construction
	 * @return Estate
	 */
	public function remove(Construction $construction): self {
		parent::removeEntity($construction->Id());
		return $this;
	}

	/**
	 * Get an Entity by ID.
	 *
	 * @param Id $id
	 * @return Entity
	 */
	protected function get(Id $id): Entity {
		return Construction::get($id);
	}
}