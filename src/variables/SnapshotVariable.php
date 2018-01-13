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
use yii\web\Response;

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
	 * @param string $html
	 * @param array $settings
	 *
	 * @return string|Response
	 */
	public function displayHtml($html, $settings = null)
	{
		if (isset($settings['asImage']) && $settings['asImage'])
		{
			return Snapshot::$app->image->displayHtml($html, $settings);
		}

		return Snapshot::$app->pdf->displayHtml($html, $settings);
	}

	/**
	 * @param string $template
	 * @param array $settings
	 *
	 * @return string|Response
	 */
	public function displayTemplate($template, $settings = null)
	{
		if (isset($settings['asImage']) && $settings['asImage'])
		{
			return Snapshot::$app->image->displayHtml($template, $settings);
		}

		return Snapshot::$app->pdf->displayTemplate($template, $settings);
	}

	/**
	 * @param string $url
	 * @param array $settings
	 *
	 * @return string|Response
	 */
	public function displayUrl($url, $settings = null)
	{
		if (isset($settings['asImage']) && $settings['asImage'])
		{
			return Snapshot::$app->image->displayUrl($url, $settings);
		}

		return Snapshot::$app->pdf->displayUrl($url, $settings);
	}
}
