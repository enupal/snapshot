<?php

namespace enupal\snapshot\contracts;

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
		$plugin   = Craft::$app->getPlugins()->getPlugin('enupal-snapshot');
		$settings = $plugin->getSettings();

		$this->binary = $settings->snapshotBinPath;

		return $this->binary ?? null;
	}

	/**
	 * @return SnappySnapshot
	 */
	protected function getGenerator(): GeneratorInterface
	{
		return new SnappyPdf($this->binary, $this->options);
	}
}