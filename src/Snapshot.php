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

use enupal\snapshot\services\Snapshot as SnapshotService;
use enupal\snapshot\variables\SnapshotVariable;
use enupal\snapshot\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Snapshot
 *
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 *
 * @property  SnapshotService $snapshot
 */
class Snapshot extends Plugin
{
	// Static Properties
	// =========================================================================

	/**
	 * @var Snapshot
	 */
	public static $app;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
		self::$app = $this->get('app');

		Event::on(
			UrlManager::class,
			UrlManager::EVENT_REGISTER_SITE_URL_RULES,
			function (RegisterUrlRulesEvent $event) {
				$event->rules['siteActionTrigger1'] = 'enupal-snapshot/settings';
				$event->rules['siteActionTrigger2'] = 'enupal-snapshot/snapshot';
			}
		);

		Event::on(
			UrlManager::class,
			UrlManager::EVENT_REGISTER_CP_URL_RULES,
			function (RegisterUrlRulesEvent $event) {
				$event->rules['cpActionTrigger1'] = 'enupal-snapshot/settings/do-something';
				$event->rules['cpActionTrigger2'] = 'enupal-snapshot/snapshot/do-something';
			}
		);

		Event::on(
			CraftVariable::class,
			CraftVariable::EVENT_INIT,
			function (Event $event) {
				/** @var CraftVariable $variable */
				$variable = $event->sender;
				$variable->set('enupal-snapshot', SnapshotVariable::class);
			}
		);

		Event::on(
			Plugins::class,
			Plugins::EVENT_AFTER_INSTALL_PLUGIN,
			function (PluginEvent $event) {
				if ($event->plugin === $this) {
				}
			}
		);

		Craft::info(
			Craft::t(
				'enupal-snapshot',
				'{name} plugin loaded',
				['name' => $this->name]
			),
			__METHOD__
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
}
