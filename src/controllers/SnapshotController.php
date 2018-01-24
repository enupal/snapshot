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
		/*$file = Craft::$app->path->getTempPath().DIRECTORY_SEPARATOR.'snapshot'.DIRECTORY_SEPARATOR.'assassa.pdf';

		if (file_exists($file))
		{
			unlink($file);
		}

		Snapshot::$app->pdf->generate('http://example.com', $file);

		return Craft::$app->response->sendFile($file, 'oli.pdf', ['inline'=>true]);
		*/

		return Snapshot::$app->pdf->displayHtml("<html><h1>Hello world</h1></html>");

		#echo '<a href="'.$url.'" download>Download</a>';
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
