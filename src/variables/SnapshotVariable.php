<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\variables;

use enupal\snapshot\services\Snapshots;
use enupal\snapshot\Snapshot;

use yii\web\Response;

/**
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 */
class SnapshotVariable
{
    /**
     * @param string $html
     * @param array $settings
     *
     * @return string|Response
     * @throws \Throwable
     * @throws \yii\web\ServerErrorHttpException
     */
    public function displayHtml($html, $settings = null)
    {
        if (isset($settings['asImage']) && $settings['asImage']) {
            return Snapshot::$app->image->displayHtml($html, $settings);
        }

        return Snapshot::$app->pdf->displayHtml($html, $settings);
    }

    /**
     * @param string $template
     * @param array $settings
     *
     * @return string|Response
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function displayTemplate($template, $settings = null)
    {
        if (isset($settings['asImage']) && $settings['asImage']) {
            return Snapshot::$app->image->displayTemplate($template, $settings);
        }

        return Snapshot::$app->pdf->displayTemplate($template, $settings);
    }

    /**
     * @param string $url
     * @param array $settings
     *
     * @return string|Response
     * @throws \Throwable
     * @throws \yii\web\ServerErrorHttpException
     */
    public function displayUrl($url, $settings = null)
    {
        if (isset($settings['asImage']) && $settings['asImage']) {
            return Snapshot::$app->image->displayUrl($url, $settings);
        }

        return Snapshot::$app->pdf->displayUrl($url, $settings);
    }

    /**
     * @return string
     */
    public function getVolumeElementType()
    {
        return Snapshot::$app->snapshots::getVolumeElementType();
    }

    /**
     * @return array
     */
    public function getAvailableSources(): array
    {
        return Snapshot::$app->snapshots->getAvailableSources();
    }

    /**
     * @param array $variables
     */
    public function addVariables(array $variables)
    {
        Snapshots::addVariables($variables);
    }
}
