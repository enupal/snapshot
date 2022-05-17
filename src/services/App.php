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

class App extends Component
{
    /**
     * @var Pdf
     */
    public $pdf;

    /**
     * @var Image
     */
    public $image;

    /**
     * @var Snapshots
     */
    public $snapshots;

    /**
     * @var Settings
     */
    public $settings;

    public function init(): void
    {
        $this->pdf = new Pdf();
        $this->image = new Image();
        $this->snapshots = new Snapshots();
        $this->settings = new Settings();
    }
}