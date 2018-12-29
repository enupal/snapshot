<?php

namespace enupal\backup\migrations;

use craft\db\Migration;
use craft\db\Query;
use enupal\snapshot\Snapshot;

/**
 * m181229_000000_add_asset_volume migration.
 */
class m181229_000000_add_asset_volume extends Migration
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
        echo "m181229_000000_add_asset_volume cannot be reverted.\n";

        return false;
    }
}
