<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\controllers;

use enupal\snapshot\Snapshot;

use Craft;
use craft\web\Controller;

/**
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 */
class SnapshotController extends Controller
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
		header('Content-Type: application/snapshot');
		header('Content-Disposition: attachment; filename="file.snapshot"');
		$result = Snapshot::$app->pdf->getOutput('https://enupal.com/craft-plugins/');

		if ($result)
		{
			echo $result;
		}

	}

	/**
	 * @return mixed
	 */
	public function actionDoSomething()
	{
		$result = 'Welcome to the SnapshotController actionDoSomething() method';

		return $result;
	}
}
