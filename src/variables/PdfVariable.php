<?php
/**
 * Pdf plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\pdf\variables;

use enupal\pdf\Pdf;

use Craft;

/**
 * @author    Enupal
 * @package   Pdf
 * @since     1.0.0
 */
class PdfVariable
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
