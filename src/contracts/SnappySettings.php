<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\contracts;

use craft\base\Model;

/**
 * SnappySettings Settings.
 */
class SnappySettings extends Model
{
    public $cliOptions = [];

    public $filename = '';

    public $path = '';

    public $inline = true;

    public $asModel = false;

    public $singleUploadLocationSource = null;

    public $singleUploadLocationSubpath = null;

    public $overrideFile = null;
}