<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria\World;

use Lemuria\Exception\LemuriaException;
use Lemuria\Model\Coordinates;
use Lemuria\Model\Lemuria\Region;

/**
 * A map represents the regions of the world as islands.
 */
class Map implements \Countable, \Iterator
{
	/**
	 * @var Island[]
	 */
	protected array $islands = [];

	/**
	 * @var array(int=>array)
	 */
	protected array $longitude = [];

	/**
	 * @var array(int=>array)
	 */
	protected array $latitude = [];

	/**
	 * @var int
	 */
	private int $current = 0;

	/**
	 * @var int
	 */
	private int $count = 0;

	/**
	 * Get number of islands on the map.
	 *
	 * @return int
	 */
	public function count(): int {
		return $this->count;
	}

	/**
	 * @return Island|null
	 */
	public function current(): ?Island {
		return $this->islands[$this->current] ?? null;
	}

	/**
	 * @return int
	 */
	public function key(): int {
		return $this->current;
	}

	/**
	 * Iterate to next island.
	 */
	public function next(): void {
		$this->current++;
	}

	/**
	 * Reset iterator.
	 */
	public function rewind(): void {
		$this->current = 0;
	}

	/**
	 * @return bool
	 */
	public function valid(): bool {
		return $this->current < $this->count;
	}

	/**
	 * Find island that contains a region.
	 *
	 * @param Region $region
	 * @return Island|null
	 */
	public function search(Region $region): ?Island {
		foreach ($this->islands as $island) {
			if ($island->contains($region)) {
				return $island;
			}
		}
		return null;
	}

	/**
	 * @param Coordinates $coordinates
	 * @param Region $region
	 * @return Island
	 * @throws LemuriaException
	 */
	public function add(Coordinates $coordinates, Region $region): Island {
		foreach ($this->findIslands($coordinates) as $island) {
			try {
				$island->add($coordinates, $region);
				return $this->merge()->search($region);
			} catch (LemuriaException $e) {
			}
		}

		$island          = new Island($coordinates, $region);
		$index           = ++$this->count;
		$this->islands[] = $island;
		if (!isset($this->longitude[$coordinates->X()])) {
			$this->longitude[$coordinates->X()] = [];
		}
		if (!isset($this->latitude[$coordinates->Y()])) {
			$this->latitude[$coordinates->Y()] = [];
		}
		$this->longitude[$coordinates->X()][] = $index;
		$this->latitude[$coordinates->Y()][]  = $index;
		return $this->merge()->search($region);
	}

	/**
	 * @param Coordinates $coordinates
	 * @return Island[]
	 */
	protected function findIslands(Coordinates $coordinates): array {
		$longitude = $this->longitude[$coordinates->X()] ?? [];
		$latitude  = $this->latitude[$coordinates->Y()] ?? [];
		$islands   = [];
		foreach ($longitude as $w) {
			foreach ($latitude as $h) {
				if ($h === $w) {
					$islands[] = $this->islands[$h];
				}
			}
		}
		return $islands;
	}

	/**
	 * @return Map
	 */
	protected function merge(): Map {
		do {
			$merged = null;
			$last   = $this->count - 1;
			$f      = 0;
			while ($f < $last) {
				$first = $this->islands[$f];
				$s     = $f + 1;
				while ($s <= $last) {
					$second = $this->islands[$s];
					if ($first->hasIntersection($second) || $first->hasNeighbour($second)) {
						try {
							$merged = $first->merge($second);
							unset($this->islands[$s]);
							$this->count--;
							$this->islands = array_values($this->islands);
							$this->updatePointers($f, $s);
							break;
						} catch (LemuriaException $e) {
						}
					}
					$s++;
				}
				if ($merged) {
					break;
				}
				$f++;
			}
		} while ($merged);
		return $this;
	}

	/**
	 * @param int $first
	 * @param int $second
	 */
	protected function updatePointers(int $first, int $second): void {
		foreach (array_keys($this->longitude) as $w) {
			$pointers = $this->longitude[$w];
			$indices  = [];
			foreach ($pointers as $index) {
				if ($index > $second) {
					$indices[$index - 1] = true;
				} elseif ($index === $second) {
					$indices[$first] = true;
				} else {
					$indices[$index] = true;
				}
			}
			$this->longitude[$w] = array_keys($indices);
		}

		foreach (array_keys($this->latitude) as $h) {
			$pointers = $this->latitude[$h];
			$indices  = [];
			foreach ($pointers as $index) {
				if ($index > $second) {
					$indices[$index - 1] = true;
				} elseif ($index === $second) {
					$indices[$first] = true;
				} else {
					$indices[$index] = true;
				}
			}
			$this->latitude[$h] = array_keys($indices);
		}
	}
}
