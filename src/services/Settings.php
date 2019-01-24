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
use craft\db\Query;
use enupal\snapshot\Snapshot;

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

    /**
     * @return bool|string
     */
    public function getDefaultCommerceTemplate()
    {
        $defaultTemplate = Craft::getAlias('@enupal/snapshot/templates/_frontend/commerce.twig');

        return $defaultTemplate;
    }

    /**
     * @param int $volumeId
     * @return int|null
     */
    private function getFolderUId(int $volumeId)
    {
        $folder = (new Query())
            ->select('*')
            ->from(['{{%volumefolders}}'])
            ->where(['volumeId' => $volumeId])
            ->one();

        return $folder['uid'] ?? null;
    }
}
