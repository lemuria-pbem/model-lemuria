<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria;

use function Lemuria\getClass;
use Lemuria\Collector;
use Lemuria\CollectorTrait;
use Lemuria\Entity;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Exception\NotRegisteredException;
use Lemuria\Model\Lemuria\Factory\BuilderTrait;
use Lemuria\Serializable;

/**
 * A party is the representation of a Lemuria player.
 */
class Party extends Entity implements Collector
{
	use BuilderTrait;
	use CollectorTrait;

	private Id $origin;

	private Race $race;

	private People $people;

	private Diplomacy $diplomacy;

	private ?array $serializedDiplomacy = null;

	/**
	 * Get a Party.
	 *
	 * @param Id $id
	 * @return Party
	 * @throws NotRegisteredException
	 */
	public static function get(Id $id): self {
		/* @var Party $party */
		$party = Lemuria::Catalog()->get($id, Catalog::PARTIES);
		return $party;
	}

	/**
	 * Create an empty party.
	 */
	public function __construct() {
		$this->people    = new People($this);
		$this->diplomacy = new Diplomacy($this);
	}

	/**
	 * Get a plain data array of the model's data.
	 *
	 * @return array
	 */
	public function serialize(): array {
		$data              = parent::serialize();
		$data['origin']    = $this->origin->Id();
		$data['race']      = getClass($this->Race());
		$data['diplomacy'] = $this->Diplomacy()->serialize();
		$data['people']    = $this->People()->serialize();
		return $data;
	}

	/**
	 * Restore the model's data from serialized data.
	 *
	 * @param array $data
	 * @return Serializable
	 */
	public function unserialize(array $data): Serializable {
		parent::unserialize($data);
		$this->origin = new Id($data['origin']);
		$this->setRace(self::createRace($data['race']));
		$this->People()->unserialize($data['people']);
		$this->serializedDiplomacy = $data['diplomacy'];
		return $this;
	}

	/**
	 * Get the catalog namespace.
	 *
	 * @return int
	 */
	public function Catalog(): int {
		return Catalog::PARTIES;
	}

	/**
	 * This method will be called by the Catalog after loading is finished; the Collector can initialize its collections
	 * then.
	 *
	 * @return Collector
	 */
	public function collectAll(): Collector {
		$this->People()->addCollectorsToAll();
		return $this;
	}

	/**
	 * Get the region in Lemuria where the party came into play.
	 *
	 * @return Region
	 */
	public function Origin(): Region {
		return Region::get($this->origin);
	}

	/**
	 * Get the party's race.
	 *
	 * @return Race
	 */
	public function Race(): Race {
		return $this->race;
	}

	/**
	 * Get all units.
	 *
	 * @return People
	 */
	public function People(): People {
		return $this->people;
	}

	/**
	 * Get all diplomatic relations.
	 *
	 * @return Diplomacy
	 */
	public function Diplomacy(): Diplomacy {
		if (is_array($this->serializedDiplomacy)) {
			$this->diplomacy->clear()->unserialize($this->serializedDiplomacy);
			$this->serializedDiplomacy = null;
		}
		return $this->diplomacy;
	}

	/**
	 * Set the region in Lemuria where the party came into play.
	 *
	 * @param Region $origin
	 * @return Party
	 */
	public function setOrigin(Region $origin): self {
		$this->origin = $origin->Id();
		return $this;
	}

	/**
	 * Set the party's race.
	 *
	 * @param Race $race
	 * @return Party
	 */
	public function setRace(Race $race): self {
		$this->race = $race;
		return $this;
	}

	/**
	 * Check that a serialized data array is valid.
	 *
	 * @param array(string=>mixed) &$data
	 */
	protected function validateSerializedData(&$data): void {
		parent::validateSerializedData($data);
		$this->validate($data, 'origin', 'int');
		$this->validate($data, 'race', 'string');
		$this->validate($data, 'people', 'array');
		$this->validate($data, 'diplomacy', 'array');
	}
}