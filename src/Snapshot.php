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

use enupal\snapshot\services\App;
use enupal\snapshot\variables\SnapshotVariable;
use enupal\snapshot\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;

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
    public $schemaVersion = '1.0.6';

    /**
     * @inheritdoc
     */
    public $hasCpSection = false;

    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

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
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'enupal-snapshot/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    protected function afterInstall()
    {
        self::$app->snapshots->installDefaultVolume();
    }

    /**
     * @param string $message
     * @param array  $params
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
}
