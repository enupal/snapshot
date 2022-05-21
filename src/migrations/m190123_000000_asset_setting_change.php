<?php

namespace enupal\snapshot\migrations;

use craft\db\Migration;
use craft\db\Query;
use Craft;
use craft\db\Table;
use craft\services\Plugins;
use enupal\snapshot\Snapshot;

/**
 * m190123_000000_asset_setting_change migration.
 */
class m190123_000000_asset_setting_change extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $plugin = Snapshot::getInstance();
        $folderIds = [];
        $settings = $plugin->getSettings();

        if (!is_null($settings->singleUploadLocationSource)){
            list(, $folderIds[]) = explode(':', $settings->singleUploadLocationSource);

            $folders = (new Query())
                ->select(['id', 'uid'])
                ->from([Table::VOLUMEFOLDERS])
                ->where(['id' => $folderIds])
                ->pairs();

            if (strpos($settings->singleUploadLocationSource, ':') !== false) {
                $single = explode(':', $settings->singleUploadLocationSource);
                $settings->singleUploadLocationSource = isset($folders[$single[1]]) ? $single[0] . ':' . $folders[$single[1]] : null;
            }
        }

        $projectConfig = Craft::$app->getProjectConfig();
        $projectConfig->set(\craft\services\ProjectConfig::PATH_PLUGINS . '.' . $plugin->handle . '.settings', $settings->toArray());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190123_000000_asset_setting_change cannot be reverted.\n";

        return false;
    }
}
