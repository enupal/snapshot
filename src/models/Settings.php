<?php
/**
 * Pdf plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\pdf\models;

use enupal\pdf\Pdf;

use Craft;
use craft\base\Model;

/**
 * @author    Enupal
 * @package   Pdf
 * @since     1.0.0
 */
class Settings extends Model
{
	// Public Properties
	// =========================================================================

	/**
	 * @var string
	 */
	public $pdfBin = '';

	/**
	 * @var string
	 */
	public $imageBin = '';


	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['pdfBin', 'imageBin'], 'required']
		];
	}
}
