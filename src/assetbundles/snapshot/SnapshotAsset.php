<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\assetbundles\Snapshot;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 */
class SnapshotAsset extends AssetBundle
{
	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->sourcePath = "@enupal/snapshot/assetbundles/snapshot/dist";

		$this->depends = [
			CpAsset::class,
		];

		$this->js = [
			'js/Snapshot.js',
		];

		$this->css = [
			'css/Snapshot.css',
		];

		parent::init();
	}
}
