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
	protected function getBinary()
	{
		$plugin   = Craft::$app->getPlugins()->getPlugin('enupal-pdf');
		$settings = $plugin->getSettings();

		$this->binary = $settings->pdfBinPath;

		return $this->binary ?? null;
	}

	/**
	 * @return SnappyPdf
	 */
	protected function getGenerator(): GeneratorInterface
	{
		return new SnappyPdf($this->binary, $this->options);
	}
}