<?php

namespace enupal\snapshot\contracts;

use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Image as SnayppyImage;
use Craft;

/**
 * PDF generator component.
 */
class SnappyImage extends BaseSnappy
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
	 * @param array $settings display inline | url
	**/
	public function displayHtml($html, $settings = null)
	{
		$settings = $this->getSettings($settings);
	}

	/**
	 * @param string $template
	 * @param array $settings display inline | url
	**/
	public function displayTemplate($template, $settings = null)
	{

	}

	/**
	 * @param string $url
	 * @param array $settings display inline | url
	**/
	public function displayUrl($url, $settings = null)
	{

	}
}