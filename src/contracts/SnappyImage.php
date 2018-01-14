<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\contracts;

use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Image;
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
		return new Image($this->binary, $this->options);
	}

	/**
	 * @param string $html
	 * @param array $settings display inline | url
	**/
	public function displayHtml($html, $settings = null)
	{
		$settingsModel = $this->populateSettings($settings, false);

		$response = $this->_generateImage($html, $settingsModel);
		// download link
		return $response;
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

	/**
	 * Generate image from html or urls
	 * @param string $source Html or Urls
	 * @param SnappySettings $settingsModel
	 * @return string|Response
	 */
	private function _generateImage($source, SnappySettings $settingsModel, $sourceIsHtml = true)
	{
		try
		{
			if ($sourceIsHtml)
			{
				$this->generateFromHtml($source, $settingsModel->path);
			}
			else
			{
				// From URL
				$this->generate($source, $settingsModel->path);
			}

			if (!file_exists($settingsModel->path))
			{
				Snapshot::error(Snapshot::t("Unable to find the Image file: ".$settingsModel->path));
				return Snapshot::t("Unable to display Image file on browser");
			}
			// Display inline
			if ($settingsModel->inline)
			{
				$this->_displayInline($settingsModel);
			}
		} catch (\RuntimeException $e)
		{
			Snapshot::error(Snapshot::t("Something went wrong when creating the Image file: ".$e->getMessage()));
			return Snapshot::t("Something went wrong when creating the Image file, please check your logs");
		}
		// return download link
		return $this->getPublicUrl($settingsModel->filename);
	}

	/**
	 * @param SnappySettings $settingsModel
	 * @return void
	 */
	private function _displayInline($settingsModel)
	{
		header('Content-Type: image/png');
		header('Content-Disposition: inline; filename="'.$settingsModel->filename.'"');

		echo file_get_contents($settingsModel->path);
		exit();
	}
}