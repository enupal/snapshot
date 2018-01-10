<?php

namespace enupal\snapshot\contracts;

use Craft;
use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf;
use craft\helpers\FileHelper;

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
		return new Pdf($this->binary, $this->options);
	}

	/**
	 * @param string $html
	 * @param array $settings display inline | url
	**/
	public function displayHtml($html, $settings = null)
	{
		$settings = new SnappySettings($settings);

		$settings->inline = false;
		$settings = $this->getSettings($settings);

		$this->generateFromHtml($html, $settings->path);

		if ($settings['inline'])
		{
			return $this->displayInline($settings);
		}


		// @todo -> handle delete files?
		// We need create a local volume to display the pdf with a url for download
		#Craft::dd(FileHelper::normalizePath($settings->path));
		#FileHelper::copyDirectory($settings->path, $this->getSnapshotPath());
		#$settings->path = $this->getSnapshotPath().$settings->filename;

		// download link
		return $this->getPublicUrl($settings->filename);
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