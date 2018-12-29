<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\services;

use Craft;
use craft\base\Component;

class Settings extends Component
{
    /**
     * @return \enupal\snapshot\models\Settings|null
     */
    public function getSettings()
    {
        $plugin = $this->getPlugin();

        return $plugin->getSettings();
    }

    /**
     * @return \craft\base\PluginInterface|null
     */
    public function getPlugin()
    {
        return Craft::$app->getPlugins()->getPlugin('enupal-snapshot');
    }
}
