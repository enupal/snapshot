<?php

namespace enupal\snapshot\contracts;

use Craft;
use enupal\snapshot\Snapshot;
use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf;

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
	 * @param array  $settings display inline | url
	 *
	 * @return string
	 */
	public function displayHtml($html, $settings = null)
	{
		$settingsModel = new SnappySettings();
		$settingsModel->setAttributes($settings, false);
		$settingsModel = $this->getSettings($settingsModel);

		if ($settings['inline'])
		{
			$this->generateFromHtml($html, $settingsModel->path);

			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="'.$settingsModel->filename.'"');

			if (file_exists($settingsModel->path))
			{
				echo file_get_contents($settingsModel->path);
				exit();
			}

			return Snapshot::t("Unable to display PDF file on browser");
		}

		$this->generateFromHtml($html, $settingsModel->path);
		// download link
		return $this->getPublicUrl($settingsModel->filename);
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