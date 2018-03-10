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
use enupal\snapshot\services\Pdf;
use enupal\snapshot\services\Image;

class App extends Component
{
    public $pdf;
    public $image;

    public function init()
    {
        $this->pdf = new Pdf();
        $this->image = new Image();
    }
}