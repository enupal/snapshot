<?php
/**
 * Pdf plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\pdf\assetbundles\Pdf;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Enupal
 * @package   Pdf
 * @since     1.0.0
 */
class PdfAsset extends AssetBundle
{
	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->sourcePath = "@enupal/pdf/assetbundles/pdf/dist";

		$this->depends = [
			CpAsset::class,
		];

		$this->js = [
			'js/Pdf.js',
		];

		$this->css = [
			'css/Pdf.css',
		];

		parent::init();
	}
}
