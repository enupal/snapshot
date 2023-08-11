<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\models;

use craft\base\Model;
use enupal\snapshot\validators\ImageLibValidator;
use enupal\snapshot\validators\PdfLibValidator;

/**
 * @author    Enupal
 * @package   Snapshot
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $pdfBinPath = '';

    /**
     * @var string
     */
    public $imageBinPath = '';

    /**
     * @var string
     */
    public $timeout;

    /**
     * @var string
     */
    public $singleUploadLocationSource;

    /**
     * @var string
     */
    public $singleUploadLocationSubpath;

    /**
     * @var bool
     */
    public $overrideFile = true;

    /**
     * @var string
     */
    public $craftCommerceTemplate = null;

    /**
     * @var string
     */
    public $craftCommerceFileName = null;

    /**
     * @var bool
     */
    public $enableStripePaymentsPdf = true;

    /**
     * @var string
     */
    public $stripePaymentsTemplate = null;

    /**
     * @var string
     */
    public $stripePaymentsFileName = 'Order-{order.number}';

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['pdfBinPath', 'imageBinPath'], 'required'],
            [['pdfBinPath'], PdfLibValidator::class],
            [['imageBinPath'], ImageLibValidator::class],
            [['timeout'], 'integer']
        ];
    }
}
