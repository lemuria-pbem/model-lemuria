<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria;

use Lemuria\Id;
use Lemuria\Serializable;
use Lemuria\SerializableTrait;

/**
 * A party's diplomacy consists of relations to other parties.
 */
final class Diplomacy implements \ArrayAccess, \Countable, \Iterator, Serializable
{
	use SerializableTrait;

	private Party $party;

	/**
	 * @var array(string=>Relation)
	 */
	private array $relations = [];

	/**
	 * @var array(int=>int)
	 */
	private array $indices = [];

	private int $index = 0;

	private int $count = 0;

	/**
	 * @var array(int=>Party)
	 */
	private array $acquaintances = [];

	/**
	 * @var array(int=>Unit)
	 */
	private array $contacts = [];

	/**
	 * Create the diplomacy of given party.
	 *
	 * @param Party $party
	 */
	public function __construct(Party $party) {
		$this->party = $party;
	}

	/**
	 * Check if a relation is set.
	 *
	 * @param Relation|string $relation
	 * @return bool
	 */
	public function offsetExists($relation): bool {
		$id = $this->getId($relation);
		return isset($this->relations[$id]);
	}

	/**
	 * Get a relation.
	 *
	 * @param Relation|string $relation
	 * @return Relation|null
	 * @throws \InvalidArgumentException
	 */
	public function offsetGet($relation): ?Relation {
		$id = $this->getId($relation);
		return $this->relations[$id] ?? null;
	}

	/**
	 * Set a relation.
	 *
	 * @param Relation|string $offset
	 * @param Relation $value
	 */
	public function offsetSet($offset, $value): void {
		$this->add($value);
	}

	/**
	 * Remove a relation.
	 *
	 * @param Relation|string $relation
	 */
	public function offsetUnset($relation) {
		$id = $this->getId($relation);
		if (isset($this->relations[$id])) {
			unset($this->relations[$id]);
		}
	}

	/**
	 * Get number of relations.
	 *
	 * @return int
	 */
	public function count(): int {
		return $this->count;
	}

	/**
	 * Get current iterator.
	 *
	 * @return Relation|null
	 */
	public function current(): ?Relation {
		$id = $this->key();
		return $id ? $this->relations[$id] : null;
	}

	/**
	 * Get current key.
	 *
	 * @return string|null
	 */
	public function key(): ?string {
		if ($this->valid()) {
			return $this->indices[$this->index];
		}
		return null;
	}

	/**
	 * Get next iterator.
	 */
	public function next(): void {
		$this->index++;
	}

	/**
	 * Reset iterator.
	 */
	public function rewind(): void {
		$this->index = 0;
	}

	/**
	 * Check if iterator is valid.
	 *
	 * @return bool
	 */
	public function valid(): bool {
		return $this->index < $this->count;
	}

	/**
	 * Get a plain data array of the model's data.
	 *
	 * @return array
	 */
	public function serialize(): array {
		$data      = ['acquaintances' => array_keys($this->acquaintances)];
		$relations = [];
		foreach ($this->relations as $relation/* @var Relation $relation */) {
			$relations[] = [
				'party'     => $relation->Party()->Id()->Id(),
				'region'    => $relation->Region() ? $relation->Region()->Id()->Id() : null,
				'agreement' => $relation->Agreement()
			];
		}
		$data['relations'] = $relations;
		return $data;
	}

	/**
	 * Restore the model's data from serialized data.
	 *
	 * @param array $data
	 * @return Serializable
	 */
	public function unserialize(array $data): Serializable {
		if (!empty($this->acquaintances)) {
			$this->acquaintances = [];
		}
		foreach ($data['acquaintances'] as $id) {
			$this->acquaintances[$id] = new Id($id);
		}

		if ($this->count > 0) {
			$this->clear();
		}
		foreach ($data['relations'] as $row) {
			$this->validateSerializedRelation($row);
			$partyId   = $row['party'];
			$party     = Party::get(new Id($partyId));
			$regionId  = $row['region'];
			$region    = $regionId ? Region::get(new Id($regionId)) : null;
			$agreement = $row['agreement'];
			$relation  = new Relation($party, $region);
			$this->add($relation->set($agreement));
		}

		return $this;
	}

	/**
	 * Check if there is a specific agreement with a unit or the party of a unit.
	 *
	 * @param int $agreement
	 * @param Unit $unit
	 * @return bool
	 */
	public function has(int $agreement, Unit $unit): bool {
		// Check contacts first.
		if (isset($this->contacts[$unit->Id()->Id()])) {
			if (Relation::isContactAgreement($agreement)) {
				return true;
			}
		}

		$party  = $unit->Party();
		$region = $unit->Region();

		// Check relations for party.
		$relation = $this->offsetGet(new Relation($party, $region));
		if ($relation) {
			return $relation->has($agreement);
		}
		$relation = $this->offsetGet(new Relation($party));
		if ($relation) {
			return $relation->has($agreement);
		}

		// Check general relations.
		$relation = $this->offsetGet(new Relation($this->party, $region));
		if ($relation) {
			return $relation->has($agreement);
		}
		$relation = $this->offsetGet(new Relation($this->party));
		if ($relation) {
			return $relation->has($agreement);
		}

		// No relations found.
		return $agreement === Relation::NONE;
	}

	/**
	 * Check if given party is an acquaintance.
	 *
	 * @param Party $party
	 * @return bool
	 */
	public function isKnown(Party $party): bool {
		return isset($this->acquaintances[$party->Id()->Id()]);
	}

	/**
	 * Add a relation.
	 * If a relation for the same party and region exists, it will be replaced.
	 *
	 * @param Relation $relation
	 * @return Diplomacy
	 */
	public function add(Relation $relation): Diplomacy {
		$id = (string)$relation;
		if (isset($this->relations[$id])) {
			$oldRelation = $this->relations[$id];
			/* @var Relation $oldRelation */
			$oldRelation->set($relation->Agreement());
		} else {
			$this->relations[$id] = $relation;
			$this->indices[]      = $id;
			$this->count++;
		}

		$this->knows($relation->Party());
		return $this;
	}

	/**
	 * Remove a relation.
	 *
	 * @param Relation $relation
	 * @return Diplomacy
	 */
	public function remove(Relation $relation): Diplomacy {
		$id = (string)$relation;
		if (isset($this->relations[$id])) {
			unset($this->relations[$id]);
			$this->indices = array_keys($this->relations);
			$this->count--;
		}
		return $this;
	}

	/**
	 * Clear all relations.
	 *
	 * @return Diplomacy
	 */
	public function clear(): Diplomacy {
		$this->relations = [];
		$this->indices   = [];
		$this->count     = 0;
		$this->index     = 0;
		return $this;
	}

	/**
	 * Add a temporary contact relation to a unit.
	 *
	 * @param Unit $unit
	 * @return Diplomacy
	 */
	public function contact(Unit $unit): Diplomacy {
		$this->contacts[$unit->Id()->Id()] = $unit;
		$this->knows($unit->Party());
		return $this;
	}

	/**
	 * Add a known party.
	 *
	 * @param Party $party
	 * @return Diplomacy
	 */
	public function knows(Party $party): Diplomacy {
		if ($party !== $this->party) {
			$partyId = $party->Id();
			$id      = $partyId->Id();
			if (!isset($this->acquaintances[$id])) {
				$this->acquaintances[$id] = $partyId;
			}
		}
		return $this;
	}

	/**
	 * Get a relation ID.
	 *
	 * @param $relation
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	private function getId($relation): string {
		if ($relation instanceof Relation) {
			return (string)$relation;
		}
		if (is_string($relation)) {
			return $relation;
		}
		throw new \InvalidArgumentException('Invalid relation ID: ' . $relation);
	}

	/**
	 * Check that a serialized data array is valid.
	 *
	 * @param array (string=>mixed) &$data
	 */
	protected function validateSerializedData(&$data): void {
		$this->validate($data, 'acquaintances', 'array');
		$this->validate($data, 'relations', 'array');
	}

	/**
	 * Check that serialized relation is valid.
	 *
	 * @param array(string=>mixed) &$data
	 */
	protected function validateSerializedRelation(&$data): void {
		$this->validate($data, 'party', 'int');
		$this->validate($data, 'region', '?int');
		$this->validate($data, 'agreement', 'int');
	}
}
