<?php
/**
 * Pdf plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\pdf\controllers;

use enupal\pdf\Pdf;

use Craft;
use craft\web\Controller;

/**
 * @author    Enupal
 * @package   Pdf
 * @since     1.0.0
 */
class SettingsController extends Controller
{

	// Protected Properties
	// =========================================================================

	/**
	 * @var    bool|array Allows anonymous access to this controller's actions.
	 *         The actions must be in 'kebab-case'
	 * @access protected
	 */
	protected $allowAnonymous = ['index', 'do-something'];

	// Public Methods
	// =========================================================================

	/**
	 * @return mixed
	 */
	public function actionIndex()
	{
		$result = 'Welcome to the SettingsController actionIndex() method';

		return $result;
	}

	/**
	 * @return mixed
	 */
	public function actionDoSomething()
	{
		$result = 'Welcome to the SettingsController actionDoSomething() method';

		return $result;
	}
}
