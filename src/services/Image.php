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

use enupal\snapshot\contracts\SnappyImage;

/**
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 */
class Image extends SnappyImage
{
    /**
     * @return string
     */
    public function test()
    {
        return $this->displayHtml("<h1>Hello world</h1>");
    }
}
