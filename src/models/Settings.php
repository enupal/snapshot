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

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pdfBinPath', 'imageBinPath'], 'required'],
            [['pdfBinPath'], PdfLibValidator::class],
            [['imageBinPath'], ImageLibValidator::class]
        ];
    }
}
