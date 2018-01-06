<?php

namespace enupal\pdf\contracts;

use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Image as SnayppyImage;
use Craft;

/**
 * PDF generator component.
 */
class Image extends BaseSnappy
{
	protected function getBinary()
	{
		$plugin   = Craft::$app->getPlugins()->getPlugin('enupal-pdf');
		$settings = $plugin->getSettings();

		$this->binary = $settings->imageBinPath;

		return $this->binary ?? null;
	}

	/**
	 * @return SnayppyImage
	 */
	protected function getGenerator(): GeneratorInterface
	{
		return new SnayppyImage($this->binary, $this->options);
	}
}