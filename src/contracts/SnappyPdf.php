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
use enupal\stripe\elements\Order;
use enupal\stripe\Stripe;
use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf;

/**
 * PDF generator component.
 */
class SnappyPdf extends BaseSnappy
{
    /**
     * @return null|string
     */
    protected function getBinary()
    {
        $this->pluginSettings = Snapshot::$app->settings->getSettings();
        $pdfBinPath = trim(Craft::parseEnv($this->pluginSettings->pdfBinPath));

        $this->binary = '"'.$pdfBinPath.'"';

        return $this->binary ?? null;
    }

    /**
     * @return GeneratorInterface
     */
    protected function getGenerator(): GeneratorInterface
    {
        $pdf = new Pdf($this->binary, $this->options);
        if ($this->pluginSettings->timeout){
            $pdf->setTimeout($this->pluginSettings->timeout);
        }
        return $pdf;
    }

    /**
     * Display a Pdf given html
     *
     * @param string $html
     * @param array $settings display inline | url
     *
     * @return string
     * @throws \Throwable
     * @throws \yii\web\ServerErrorHttpException
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
     *
     * @param string $template
     * @param array $settings display inline | url
     *
     * @return string|Response
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function displayTemplate($template, $settings = null)
    {
        $templatesPath = Craft::$app->getView()->getTemplatesPath();

        Craft::$app->getView()->setTemplatesPath($templatesPath);

        $variables = $settings['variables'] ?? [];

        $html = Craft::$app->getView()->renderTemplate($template, $variables);

        return $this->displayHtml($html, $settings);
    }

    /**
     * @param Order $order
     * @param array $settings display inline | url
     *
     * @return string|Response
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function displayOrder(Order $order, $settings = null)
    {
        $settings['variables']['order'] = $order;
        $template = $this->getStripePaymentsOrderTemplate($settings);
        $settings['filename'] = $this->getStripePaymentsFilename($settings);

        return $this->displayTemplate($template, $settings);
    }

    /**
     * Display a pdf given a url
     *
     * @param string|array $url
     * @param array $settings display inline | url | etc
     *
     * @return Response|string
     * @throws \Throwable
     * @throws \yii\web\ServerErrorHttpException
     */
    public function displayUrl($url, $settings = null)
    {
        $urls = $this->sanitizeUrl($url);

        $settingsModel = $this->populateSettings($settings);

        $response = $this->_generatePdf($urls, $settingsModel, false);

        return $response;
    }

    /**
     * @param array $options
     *
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getDefaultOptions($options = [])
    {
        $templatesPath = Craft::$app->getView()->getTemplatesPath();
        Craft::$app->getView()->setTemplatesPath($templatesPath);

        $defaultOptions = [
            'dpi' => '96',
            'load-error-handling' => 'ignore',
            'zoom' => '1.33',
            'disable-smart-shrinking' => null
        ];

        if (isset($options['header-html'])) {
            $variables = $settings['variables'] ?? [];

            $html = Craft::$app->getView()->renderTemplate($options['header-html'], $variables);

            $options['header-html'] = $html;
        }

        if (isset($options['footer-html'])) {
            $variables = $settings['variables'] ?? [];

            $html = Craft::$app->getView()->renderTemplate($options['footer-html'], $variables);

            $options['footer-html'] = $html;
        }

        return array_merge($defaultOptions, $options);
    }

    /**
     * Generate pdf from html
     *
     * @param string $source Html or Urls
     * @param SnappySettings $settingsModel
     * @param bool $sourceIsHtml
     *
     * @return string|Response
     * @throws \Throwable
     */
    private function _generatePdf($source, SnappySettings $settingsModel, $sourceIsHtml = true)
    {
        try {
            // Display inline in browser
            if ($settingsModel->inline) {
                $this->_displayInline($source, $settingsModel, $sourceIsHtml);
            }
            //Return a download link
            if ($sourceIsHtml) {
                $this->generateFromHtml($source, $settingsModel->path);
            } else {
                // From URL
                $this->generate($source, $settingsModel->path);
            }

            if (!file_exists($settingsModel->path)) {
                Snapshot::error(Snapshot::t("Unable to find the PDF file: ".$settingsModel->path));
                return Snapshot::t("Unable to display PDF file on browser");
            }

            $asset = $this->getAsset($settingsModel);

        } catch (\Exception $e) {
            Snapshot::error(Snapshot::t("Something went wrong when creating the PDF file: ".$e->getMessage()));
            return Snapshot::t("Something went wrong when creating the PDF file, please check your logs");
        }

        if ($settingsModel->asModel){
            return $asset;
        }

        return $asset->getUrl();
    }

    /**
     * @param string         $source html or urls
     * @param SnappySettings $settingsModel
     * @param boolean        $isHtml
     *
     * @return void
     */
    private function _displayInline($source, $settingsModel, $isHtml = true)
    {
        header('Content-Disposition: inline; filename="'.$settingsModel->filename.'"');
        header('Content-Type: application/pdf');

        if ($isHtml) {
            echo $this->getOutputFromHtml($source);
        } else {
            echo $this->getOutput($source);
        }

        exit();
    }
}