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