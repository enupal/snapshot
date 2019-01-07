<?php

namespace enupal\snapshot\migrations;

use craft\db\Migration;
use enupal\snapshot\Snapshot;

/**
 * m190107_000000_add_asset_volume migration.
 */
class m190107_000000_add_asset_volume extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        Snapshot::$app->snapshots->installDefaultVolume();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190107_000000_add_asset_volume cannot be reverted.\n";

        return false;
    }
}
