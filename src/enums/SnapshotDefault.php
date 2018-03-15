<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\enums;

/**
 * Current status of a Backup
 */
abstract class SnapshotDefault extends BaseEnum
{
    // Constants
    // =========================================================================

    const TEMP_DIR = 'enupalsnapshottemp';
    const PUBLIC_DIR = 'enupalsnapshot';
}
