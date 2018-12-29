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

use craft\base\Component;
use craft\helpers\FileHelper;
use craft\volumes\Local;
use Craft;
use enupal\snapshot\Snapshot;
use craft\records\Volume as VolumeRecord;

class Snapshots extends Component
{
    /**
     * @return bool
     * @throws \Throwable
     */
    public function installDefaultVolume()
    {
        /** @var Volume $volume */
        $volumes = Craft::$app->getVolumes();
        $enupalSnapshotPath = $this->getDefaultSnapshotPath();
        $volumeSettings = [
            'path' => $enupalSnapshotPath
        ];
        $plugin = Snapshot::$app->settings->getPlugin();

        $volumeHandle = $this->getHandleAsNew("enupalSnapshot");
        $volume = null;

        $volume = $volumes->createVolume([
            'id' => null,
            'type' => Local::class,
            'name' => "Enupal Snapshot",
            'handle' => $volumeHandle,
            'hasUrls' => true,
            'url' => '/enupalsnapshot/',
            'settings' => json_encode($volumeSettings)
        ]);

        $response = $volumes->saveVolume($volume);

        if (!$response) {
            Craft::error('Unable to save the volume', __METHOD__);
            return false;
        }

        // @todo - update to craft 3.1 getPluin returns null here.
        $settings = [
            'volumeId' => $volume->id,
            'pdfBinPath' => '',
            'imageBinPath' => '',
            'timeout' => ''
        ];
        $settings = json_encode($settings);
        Craft::$app->getDb()->createCommand()->update('{{%plugins}}', [
            'settings' => $settings
        ], [
                'handle' => 'enupal-snapshot'
            ]
        )->execute();


        return true;
    }

    /**
     * @return string
     */
    private function getDefaultSnapshotPath()
    {
        $debugTrace = debug_backtrace();
        $initialCalledFile = count($debugTrace) ? $debugTrace[count($debugTrace) - 1]['file'] : __FILE__;
        $publicFolderPath = dirname($initialCalledFile);
        $publicFolderPath = $publicFolderPath.DIRECTORY_SEPARATOR."enupalsnapshot";
        $publicFolderPath = FileHelper::normalizePath($publicFolderPath);

        return $publicFolderPath;
    }

    /**
     * Create a unique string for "handle" if it's already taken
     *
     * @param $value
     *
     * @return string
     */
    public function getHandleAsNew($value)
    {
        $newHandle = $value;
        $aux = true;
        $i = 1;
        do {
            if ($i > 1) {
                $newHandle = $value.$i;
            }

            $volume = $this->getVolumeRecordByHandle($newHandle);

            if (is_null($volume)) {
                $aux = false;
            }

            $i++;
        } while ($aux);

        return $newHandle;
    }

    /**
     * @param $handle
     *
     * @return VolumeRecord|null
     */
    private function getVolumeRecordByHandle($handle)
    {
        $result = VolumeRecord::find()
            ->where(['handle' => $handle])
            ->one();

        return $result;
    }
}
