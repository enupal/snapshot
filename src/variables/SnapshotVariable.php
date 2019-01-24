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

use enupal\stripe\elements\Order;
use yii\web\Response;
use Craft;

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
     * Display a Stripe Payments Order PDF
     *
     * @param Order $order
     * @param null $settings
     * @return \craft\web\Response|string
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function displayOrder(Order $order, $settings = null)
    {
        if (isset($settings['asImage']) && $settings['asImage']) {
            return Snapshot::$app->image->displayOrder($order, $settings);
        }

        return Snapshot::$app->pdf->displayOrder($order, $settings);
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

    /**
     * @return bool
     */
    public function isStripePaymentsInstalled()
    {
        return $this->getPlugin('enupal-stripe');
    }

    /**
     * @return bool
     */
    public function isCommerceInstalled()
    {
        return $this->getPlugin('commerce');
    }

    /**
     * @param $handle
     * @return bool
     */
    private function getPlugin($handle)
    {
        $plugin = Craft::$app->getPlugins()->getPlugin($handle);

        if (is_null($plugin)){
            return false;
        }

        return true;
    }
}
