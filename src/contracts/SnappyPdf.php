<?php

namespace enupal\snapshot\contracts;

use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf as SnappyPdf;
use Craft;

/**
 * PDF generator component.
 */
class SnappyPdf extends BaseSnappy
{
	protected function getBinary()
	{
		$plugin   = Craft::$app->getPlugins()->getPlugin('enupal-snapshot');
		$settings = $plugin->getSettings();

		$this->binary = $settings->pdfBinPath;

		return $this->binary ?? null;
	}

	/**
	 * @return SnappySnapshot
	 */
	protected function getGenerator(): GeneratorInterface
	{
		return new SnappyPdf($this->binary, $this->options);
	}

	/**
	 * @param string $html
	 * @param array $settings display inline | url
	**/
	public function displayHtml($html, $settings = null)
	{
		$settings = ['inline' => false];
		$settings = $this->getSettings($settings);

		$this->generateFromHtml($html, $settings['path']);

		if ($settings['inline'])
		{
			return $this->displayInline($settings['path'], $settings);
		}

		// download link
		return $settings['path'];
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