<?php
declare (strict_types = 1);
namespace Lemuria\Model\Lemuria;

/**
 * Describes the types of ships in Lemuria.
 */
interface Ship extends Artifact, Transport
{
	/**
	 * Get the minimum sailing talent for the captain to navigate.
	 *
	 * @return int
	 */
	public function Captain(): int;

	/**
	 * Get the minimum total sailing ability to sail the ship.
	 *
	 * @return int
	 */
	public function Crew(): int;
}