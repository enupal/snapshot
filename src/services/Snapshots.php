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
     * @var array
     */
    protected static $fieldVariables = [];

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
     * Adds variables to parse in templates
     *
     * @param array $variables
     */
    public static function addVariables(array $variables)
    {
        static::$fieldVariables = array_merge(static::$fieldVariables, $variables);
    }

    /**
     * @return array
     */
    public function getFieldVariables()
    {
        return static::$fieldVariables;
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
