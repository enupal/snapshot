<?php

namespace enupal\pdf\contracts;

use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf as SnappyPdf;
use Craft;

/**
 * PDF generator component.
 */
class Pdf extends BaseSnappy
{
	public function init()
	{
		parent::init();

		$plugin   = Craft::$app->getPlugins()->getPlugin('enupal-backup');
		$settings = $plugin->getSettings();

		$this->binary = $settings->pdfBinPath;
	}

	/**
	 * @return SnappyPdf
	 */
	protected function getGenerator(): GeneratorInterface
	{
		return new SnappyPdf($this->binary, $this->options);
	}
}