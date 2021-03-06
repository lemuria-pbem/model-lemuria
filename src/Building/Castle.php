<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria\Building;

use JetBrains\PhpStorm\Pure;

use Lemuria\Model\Lemuria\Building;

/**
 * A castle is a fortified building where units can hide and trade on the market.
 */
interface Castle extends Building
{
	public const MARKET_SIZE = 5;

	#[Pure] public function Defense(): int;

	#[Pure] public function MaxSize(): int;

	#[Pure] public function MinSize(): int;

	public function Downgrade(): Castle;

	public function Upgrade(): Castle;

	#[Pure] public function Wage(): int;
}
