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

use Craft;
use craft\web\Response;
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
		$pdf = new Pdf($this->binary, $this->options);
		return $pdf;
	}

	/**
	 * Display a Pdf given html
	 * @param string $html
	 * @param array  $settings display inline | url
	 *
	 * @return string
	 */
	public function displayHtml($html, $settings = null)
	{
		$settingsModel = $this->populateSettings($settings);

		$response = $this->_generatePdf($html, $settingsModel);
		// download link
		return $response;
	}

	/**
	 * Display a pdf given a template
	 * @param string $template
	 * @param array $settings display inline | url
	 * @return string|Response
	**/
	public function displayTemplate($template, $settings = null)
	{
		$templatesPath = Craft::$app->getView()->getTemplatesPath();

		Craft::$app->getView()->setTemplatesPath($templatesPath);

		$variables = $settings['variables'] ?? [];

		$html = Craft::$app->getView()->renderTemplate($template, $variables);

		return $this->displayHtml($html, $settings);
	}

	/**
	 * Display a pdf given a url
	 * @param string|array $url
	 * @param array $settings display inline | url | etc
	**/
	public function displayUrl($url, $settings = null)
	{
		$urls = $this->sanitizeUrl($url);

		$settingsModel = $this->populateSettings($settings);

		$response = $this->_generatePdf($urls, $settingsModel, false);

		return $response;
	}

	public function getDefaultOptions($options = [])
	{
		$templatesPath = Craft::$app->getView()->getTemplatesPath();
		Craft::$app->getView()->setTemplatesPath($templatesPath);

		$defaultOptions = [
			'dpi' =>  '96',
			'load-error-handling' => 'ignore',
			'zoom' => '1.33',
			'disable-smart-shrinking' => null
		];

		if (isset($options['header-html']))
		{
			$variables = $settings['variables'] ?? [];

			$html = Craft::$app->getView()->renderTemplate($options['header-html'], $variables);

			$options['header-html'] = $html;
		}

		if (isset($options['footer-html']))
		{
			$variables = $settings['variables'] ?? [];

			$html = Craft::$app->getView()->renderTemplate($options['footer-html'], $variables);

			$options['footer-html'] = $html;
		}

		return array_merge($defaultOptions, $options);
	}

	/**
	 * Generate pdf from html
	 * @param string $source Html or Urls
	 * @param SnappySettings $settingsModel
	 * @return string|Response
	*/
	private function _generatePdf($source, SnappySettings $settingsModel, $sourceIsHtml = true)
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
				Snapshot::error(Snapshot::t("Unable to find the PDF file: ".$settingsModel->path));
				return Snapshot::t("Unable to display PDF file on browser");
			}
			// Display inline
			if ($settingsModel->inline)
			{
				$this->_displayInline($settingsModel);
			}
		} catch (\RuntimeException $e)
		{
			Snapshot::error(Snapshot::t("Something went wrong when creating the PDF file: ".$e->getMessage()));
			return Snapshot::t("Something went wrong when creating the PDF file, please check your logs");
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
		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename="'.$settingsModel->filename.'"');

		echo file_get_contents($settingsModel->path);
		exit();
	}
}