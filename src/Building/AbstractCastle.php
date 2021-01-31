<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria\Building;

use JetBrains\PhpStorm\Pure;

use Lemuria\Exception\LemuriaException;
use Lemuria\Model\Lemuria\Building;
use Lemuria\Model\Lemuria\Commodity\Stone;

/**
 * Base class for any castle.
 */
abstract class AbstractCastle extends AbstractBuilding implements Castle
{
	/**
	 * Get the Castle for a given size.
	 */
	public static function forSize(int $size): Castle {
		if ($size <= 0) {
			throw new LemuriaException('Size must be greater than zero.');
		}
		$class = self::getClassForSize($size);
		/* @var Castle $castle */
		$castle = self::createBuilding($class);
		return $castle;
	}

	#[Pure] public function Dependency(): Building {
		return Building::IS_INDEPENDENT;
	}

	#[Pure] public function Feed(): int {
		return Building::IS_FREE;
	}

	#[Pure] public function Upkeep(): int {
		return Building::IS_FREE;
	}

	#[Pure] public function UsefulSize(): int {
		return Building::IS_UNLIMITED;
	}

	/**
	 * Get the best fitting building for given size of this building.
	 */
	#[Pure] public function correctBuilding(int $size): Building {
		$this->validateSize($size);
		if ($size < $this->MinSize()) {
			return $this->Downgrade()->correctBuilding($size);
		}
		if ($size > $this->MaxSize()) {
			return $this->Upgrade()->correctBuilding($size);
		}
		return $this;
	}

	/**
	 * Get the best fitting size for given size of this building.
	 */
	#[Pure] public function correctSize(int $size): int {
		$this->validateSize($size);
		if ($size < $this->MinSize()) {
			return $this->MinSize();
		}
		if ($size > $this->MaxSize()) {
			return $this->MaxSize();
		}
		return $size;
	}

	#[Pure] protected function material(): array {
		return [Stone::class => 1];
	}

	#[Pure] private static function getClassForSize(int $size): string {
		if ($size <= Site::MAX_SIZE) {
			return Site::class;
		}
		if ($size <= Fort::MAX_SIZE) {
			return Fort::class;
		}
		if ($size <= Tower::MAX_SIZE) {
			return Tower::class;
		}
		if ($size <= Palace::MAX_SIZE) {
			return Palace::class;
		}
		if ($size <= Stronghold::MAX_SIZE) {
			return Stronghold::class;
		}
		return Citadel::class;
	}
}
