<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot;

use craft\helpers\UrlHelper;
use craft\services\Plugins;
use enupal\snapshot\services\App;
use enupal\snapshot\variables\SnapshotVariable;
use enupal\snapshot\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use enupal\stripe\events\NotificationEvent;
use enupal\stripe\services\Emails;

use enupal\stripe\Stripe;
use yii\base\Event;

/**
 * Class Snapshot
 *
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 *
 * @property  Snapshot $snapshot
 */
class Snapshot extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var App
     */
    public static $app;

    /**
     * @inheritdoc
     */
    public string $schemaVersion = '2.0.0';

    /**
     * @inheritdoc
     */
    public bool $hasCpSection = false;

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$app = $this->get('app');

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('enupalsnapshot', SnapshotVariable::class);
            }
        );

        if ($this->isStripePaymentsInstalled()){
            Craft::$app->view->hook('cp.enupal-stripe.order.actionbutton', function(array &$context) {
                $order = $context['order'];
                $pluginSettings = self::getSettings();
                $view = Craft::$app->getView();
                if ($pluginSettings->stripePaymentsTemplate){
                    $settings = $this->getStripePaymentsSettings();
                    $view->setTemplatesPath(Craft::$app->path->getSiteTemplatesPath());
                    $pdfUrl = Snapshot::$app->pdf->displayOrder($order, $settings);
                    $view->setTemplatesPath(Craft::$app->path->getCpTemplatesPath());
                    if ($pdfUrl !== null) {
                        return $view->renderTemplate('enupal-snapshot/_pdfbuttons/stripepayments', ['pdfUrl' => $pdfUrl]);
                    }
                }

                return $view->renderTemplate('enupal-snapshot/_pdfbuttons/templateNotFound');
            });

            Event::on(Emails::class, Emails::EVENT_BEFORE_SEND_NOTIFICATION_EMAIL, function(NotificationEvent $e) {
                $message = $e->message;
                $settings = $this->getStripePaymentsSettings();
                $pluginSettings = self::getSettings();
                if ($pluginSettings->stripePaymentsTemplate){
                    if (isset($e->order) && $e->type == Stripe::$app->emails::CUSTOMER_TYPE){
                        $view = Craft::$app->getView();
                        $view->setTemplatesPath(Craft::$app->path->getSiteTemplatesPath());
                        $pdfUrl = Snapshot::$app->pdf->displayOrder($e->order, $settings);
                        $view->setTemplatesPath(Craft::$app->path->getCpTemplatesPath());

                        if ($pdfUrl === null) {
                            Craft::error('Unable to find the Stripe Payments Order template');
                            return null;
                        }

                        if (UrlHelper::isFullUrl($pdfUrl)){
                            $pdfUrl = UrlHelper::siteUrl($pdfUrl);
                        }
                        $content = file_get_contents($pdfUrl);
                        $path = parse_url($pdfUrl, PHP_URL_PATH);
                        $fileName = basename($path);

                        if ($content){
                            $message->attachContent($content, ['fileName' => $fileName, 'contentType' => 'application/pdf']);
                        }
                    }
                }
            });
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?\craft\base\Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'enupal-snapshot/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    /**
     * @param string $message
     * @param array $params
     *
     * @return string
     */
    public static function t($message, array $params = [])
    {
        return Craft::t('enupal-snapshot', $message, $params);
    }

    /**
     * @param        $message
     * @param string $type
     */
    public static function log($message, $type = 'info')
    {
        Craft::$type(self::t($message), __METHOD__);
    }

    /**
     * @param $message
     */
    public static function info($message)
    {
        Craft::info(self::t($message), __METHOD__);
    }

    /**
     * @param $message
     */
    public static function error($message)
    {
        Craft::error(self::t($message), __METHOD__);
    }

    private function isStripePaymentsInstalled()
    {
        $stripePluginHandle = 'enupal-stripe';
        $projectConfig = Craft::$app->getProjectConfig();
        $stripePaymentsSettings = $projectConfig->get(\craft\services\ProjectConfig::PATH_PLUGINS.'.'.$stripePluginHandle);
        $isInstalled = $stripePaymentsSettings['enabled'] ?? false;

        return $isInstalled;
    }

    /**
     * @return array
     */
    private function getStripePaymentsSettings()
    {
        $settings = [
            'inline' => false,
            'overrideFile' => false,
            'cliOptions' => [
                'viewport-size' => '1280x1024',
                'margin-top' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,
                'margin-right' => 0
            ]
        ];

        return $settings;
    }
}
