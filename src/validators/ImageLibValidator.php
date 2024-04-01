<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\validators;

use yii\validators\Validator;
use enupal\snapshot\Snapshot;
use craft\helpers\UrlHelper;
use Craft;

class ImageLibValidator extends Validator
{
    public $skipOnEmpty = false;

    /**
     * Wkhtmltopdf validation
     *
     * @param $object
     * @param $attribute
     */
    public function validateAttribute($object, $attribute)
    {
        $url = Snapshot::$app->image->test();

        if ($object->imageBinPath && !UrlHelper::isFullUrl($url)) {
            $this->addError($object, $attribute, Snapshot::t('Something went wrong in the test. Please check the path and your logs.'));
        }
    }
}
