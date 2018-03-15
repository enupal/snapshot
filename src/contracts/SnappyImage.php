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
use enupal\snapshot\Snapshot;
use Craft;

/**
 * PDF generator component.
 */
class SnappyImage extends BaseSnappy
{
    protected function getBinary()
    {
        $plugin = Craft::$app->getPlugins()->getPlugin('enupal-snapshot');
        $settings = $plugin->getSettings();

        $this->binary = '"'.$settings->imageBinPath.'"';

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
     * @param array  $settings display inline | url
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
     * @param array  $settings display inline | url
     *
     * @return string
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
     * @param string $url
     * @param array  $settings display inline | url
     *
     * @return string
     */
    public function displayUrl($url, $settings = null)
    {
        $urls = $this->sanitizeUrl($url);

        $settingsModel = $this->populateSettings($settings, false);

        $response = $this->_generateImage($urls, $settingsModel, false);

        return $response;
    }

    public function getDefaultOptions($options = [])
    {
        $defaultOptions = [
            'zoom' => '1.33'
        ];

        return array_merge($defaultOptions, $options);
    }

    /**
     * Generate image from html or urls
     *
     * @param string         $source Html or Urls
     * @param SnappySettings $settingsModel
     *
     * @return string|Response
     */
    private function _generateImage($source, SnappySettings $settingsModel, $sourceIsHtml = true)
    {
        try {
            if ($sourceIsHtml) {
                $this->generateFromHtml($source, $settingsModel->path);
            } else {
                // From URL
                $this->generate($source, $settingsModel->path);
            }

            if (!file_exists($settingsModel->path)) {
                Snapshot::error(Snapshot::t("Unable to find the Image file: ".$settingsModel->path));
                return Snapshot::t("Unable to display Image file on browser");
            }
        } catch (\RuntimeException $e) {
            Snapshot::error(Snapshot::t("Something went wrong when creating the Image file: ".$e->getMessage()));
            return Snapshot::t("Something went wrong when creating the Image file, please check your logs");
        }
        // return download link always for images
        return $this->getPublicUrl($settingsModel->filename);
    }
}