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
     * @return int|null
     */
    public function getVolumeId()
    {
        $settings = $this->getSettings();
        $volumeId = $settings->volumeId ?? null;

        return $volumeId;
    }

    /**
     * @param int $volumeId
     * @throws \yii\db\Exception
     */
    public function saveDefaultSettings(int $volumeId)
    {
        $folderId = $this->getFolderId($volumeId);
        // @todo - update to craft 3.1 getPluin returns null here.

        $settings = Snapshot::getInstance()->getSettings();
        $settings->singleUploadLocationSource = 'folder:'.$folderId;
        $settings->volumeId = $volumeId;

        $settings = json_encode($settings->getAttributes());

        Craft::$app->getDb()->createCommand()->update('{{%plugins}}', [
            'settings' => $settings
        ], [
                'handle' => 'enupal-snapshot'
            ]
        )->execute();
    }

    /**
     * @param int $volumeId
     * @return int|null
     */
    private function getFolderId(int $volumeId)
    {
        $folder = (new Query())
            ->select('*')
            ->from(['{{%volumefolders}}'])
            ->where(['volumeId' => $volumeId])
            ->one();

        return $folder['id'] ?? null;
    }
}
