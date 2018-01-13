<?php

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
		return new Pdf($this->binary, $this->options);
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
		$settingsModel = new SnappySettings();
		$settingsModel->setAttributes($settings, false);
		$settingsModel = $this->getSettings($settingsModel);

		$response = $this->_generateFromHtml($html, $settingsModel);
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
	 * @param string $url
	 * @param array $settings display inline | url
	**/
	public function displayUrl($url, $settings = null)
	{

	}
	/**
	 * Generate pdf from html
	 * @param string $html
	 * @param SnappySettings $settingsModel
	 * @return string|Response
	*/
	private function _generateFromHtml($html, SnappySettings $settingsModel)
	{
		try
		{
			$this->generateFromHtml($html, $settingsModel->path);

			if (!file_exists($settingsModel->path))
			{
				Snapshot::error(Snapshot::t("Unable to find the PDF file: ".$settingsModel->path));
				return Snapshot::t("Unable to display PDF file on browser");
			}
			// Display inline
			if ($settingsModel->inline)
			{
				header('Content-Type: application/pdf');
				header('Content-Disposition: inline; filename="'.$settingsModel->filename.'"');

				echo file_get_contents($settingsModel->path);
				exit();
			}
		} catch (Exception $e)
		{
			Snapshot::error(Snapshot::t("Something went wrong when creating PDF: ".$e->getMessage()));
			return Snapshot::t("Something went wrong when creating PDF, please check your logs");
		}
		// return download link
		return $this->getPublicUrl($settingsModel->filename);
	}
}