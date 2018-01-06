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
	public function init()
	{
		parent::init();

		$plugin   = Craft::$app->getPlugins()->getPlugin('enupal-pdf');
		$settings = $plugin->getSettings();

		$this->binary = $settings->imageBinPath;
	}

	/**
	 * @return SnayppyImage
	 */
	protected function getGenerator(): GeneratorInterface
	{
		return new SnayppyImage($this->binary, $this->options);
	}
}