<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria\Race;

use Lemuria\Model\Lemuria\Talent\Armory;
use Lemuria\Model\Lemuria\Talent\Bladefighting;
use Lemuria\Model\Lemuria\Talent\Carriagemaking;
use Lemuria\Model\Lemuria\Talent\Catapulting;
use Lemuria\Model\Lemuria\Talent\Crossbowing;
use Lemuria\Model\Lemuria\Talent\Horsetaming;
use Lemuria\Model\Lemuria\Talent\Mining;
use Lemuria\Model\Lemuria\Talent\Navigation;
use Lemuria\Model\Lemuria\Talent\Riding;
use Lemuria\Model\Lemuria\Talent\Roadmaking;
use Lemuria\Model\Lemuria\Talent\Shipbuilding;
use Lemuria\Model\Lemuria\Talent\Spearfighting;
use Lemuria\Model\Lemuria\Talent\Trading;
use Lemuria\Model\Lemuria\Talent\Woodchopping;

/**
 * Aquans are humans of the sea.
 */
final class Aquan extends AbstractRace
{
	private const HITPOINTS = 25;

	private const PAYLOAD = 5 * 100;

	private const RECRUITING = 80;

	private const WEIGHT = 10 * 100;

	/**
	 * Get the hitpoints of a person.
	 *
	 * @return int
	 */
	public function Hitpoints(): int {
		return self::HITPOINTS;
	}

	/**
	 * Get the maximum weight of payload.
	 *
	 * @return int
	 */
	public function Payload(): int {
		return self::PAYLOAD;
	}

	/**
	 * Get the recruiting cost for one person.
	 *
	 * @return int
	 */
	public function Recruiting(): int {
		return self::RECRUITING;
	}

	/**
	 * Get the weight of a person.
	 *
	 * @return int
	 */
	public function Weight(): int {
		return self::WEIGHT;
	}

	/**
	 * Get the modifications.
	 *
	 * @return array(string=>int)
	 */
	protected function mods(): array {
		return [
			Armory::class      => -2, Bladefighting::class => -1, Carriagemaking::class => -1,
			Catapulting::class => -2, Crossbowing::class   => -1, Horsetaming::class    => -1,
			Mining::class      => -1, Navigation::class    =>  2, Riding::class         => -1,
			Roadmaking::class  => -2, Shipbuilding::class  =>  2, Spearfighting::class  => 1,
			Trading::class     =>  2,  Woodchopping::class =>  1
		];
	}
}
