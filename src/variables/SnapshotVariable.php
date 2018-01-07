<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\variables;

use enupal\snapshot\Snapshot;

use Craft;

/**
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 */
class SnapshotVariable
{
	// Public Methods
	// =========================================================================

	/**
	 * @param null $optional
	 * @return string
	 */
	public function exampleVariable($optional = null)
	{
		$result = "And away we go to the Twig template...";
		if ($optional) {
			$result = "I'm feeling optional today...";
		}
		return $result;
	}
}
