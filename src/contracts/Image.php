<?php

namespace enupal\snapshot\contracts;

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
		$plugin   = Craft::$app->getPlugins()->getPlugin('enupal-snapshot');
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

	/**
	 * @param string $html
	 * @param array $options display inline | url
	**/
	public function displayHtml($html, $options)
	{

	}

	/**
	 * @param string $template
	 * @param array $options display inline | url
	**/
	public function displayTemplate($template, $options)
	{

	}

	/**
	 * @param string $url
	 * @param array $options display inline | url
	**/
	public function displayUrl($url, $option)
	{

	}
}