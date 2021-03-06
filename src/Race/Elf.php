<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria\Race;

use JetBrains\PhpStorm\Pure;

use Lemuria\Model\Lemuria\Talent\Archery;
use Lemuria\Model\Lemuria\Talent\Armory;
use Lemuria\Model\Lemuria\Talent\Bowmaking;
use Lemuria\Model\Lemuria\Talent\Camouflage;
use Lemuria\Model\Lemuria\Talent\Catapulting;
use Lemuria\Model\Lemuria\Talent\Constructing;
use Lemuria\Model\Lemuria\Talent\Horsetaming;
use Lemuria\Model\Lemuria\Talent\Magic;
use Lemuria\Model\Lemuria\Talent\Mining;
use Lemuria\Model\Lemuria\Talent\Navigation;
use Lemuria\Model\Lemuria\Talent\Perception;
use Lemuria\Model\Lemuria\Talent\Quarrying;
use Lemuria\Model\Lemuria\Talent\Roadmaking;
use Lemuria\Model\Lemuria\Talent\Shipbuilding;

/**
 * Elves live in the woods.
 */
final class Elf extends AbstractRace
{
	private const HITPOINTS = 22;

	private const PAYLOAD = 5 * 100;

	private const RECRUITING = 130;

	private const WEIGHT = 10 * 100;

	#[Pure] public function Hitpoints(): int {
		return self::HITPOINTS;
	}

	#[Pure] public function Payload(): int {
		return self::PAYLOAD;
	}

	#[Pure] public function Recruiting(): int {
		return self::RECRUITING;
	}

	#[Pure] public function Weight(): int {
		return self::WEIGHT;
	}

	#[Pure] protected function mods(): array {
		return [
			Archery::class     =>  2, Armory::class       => -1, Bowmaking::class    =>  2,
			Camouflage::class  =>  1, Catapulting::class  => -2, Constructing::class => -1,
			Horsetaming::class =>  1, Magic::class        =>  1, Mining::class       => -2,
			Navigation::class  => -1, Perception::class   =>  1, Quarrying::class    => -1,
			Roadmaking::class  => -1, Shipbuilding::class => -1
		];
	}
}
