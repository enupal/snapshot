<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\services;

use Craft;
use craft\base\Component;
use enupal\backup\Backup;

class App extends Component
{
	public $toSnapshot;
	public $toImage;

	public function init()
	{
		$this->toSnapshot = new ToSnapshot();
		$this->toImage = new ToImage();
	}
}