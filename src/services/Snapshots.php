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
use craft\elements\Asset;
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

        $volumeHandle = $this->getHandleAsNew("enupalSnapshot");
        $volume = null;

        $volume = $volumes->createVolume([
            'id' => null,
            'type' => Local::class,
            'name' => ucwords($volumeHandle),
            'handle' => $volumeHandle,
            'hasUrls' => true,
            'url' => '/enupalsnapshot/',
            'settings' => json_encode($volumeSettings)
        ]);

        $response = $volumes->saveVolume($volume);

        if (!$response) {
            $errors = $volume->getErrors();
            Craft::error('Unable to save the volume: '.json_encode($errors), __METHOD__);
            return false;
        }

        Snapshot::$app->settings->saveDefaultSettings($volume->id);

        return true;
    }

    /**
     * @throws \Throwable
     */
    public function removeVolume()
    {
        $volumeId = Snapshot::$app->settings->getVolumeId();

        if ($volumeId) {
            Craft::$app->getVolumes()->deleteVolumeById((int)$volumeId);
        }
    }

    /**
     * @return array
     */
    public function getAvailableSources()
    {
        $sourceOptions = [];

        foreach (Asset::sources('settings') as $key => $volume) {
            if (!isset($volume['heading'])) {
                $sourceOptions[] = [
                    'label' => $volume['label'],
                    'value' => $volume['key']
                ];
            }
        }

        return $sourceOptions;
    }

    /**
     * @return string
     */
    public static function getVolumeElementType(): string
    {
        return Asset::class;
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
