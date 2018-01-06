<?php

namespace enupal\pdf\contracts;

use Knp\Snappy\Pdf as SnappyPdf;

/**
 * PDF generator component.
 */
class PdfComponent extends BaseSnappy
{
	/**
	 * @return SnappyPdf
	 */
	protected function getGenerator()
	{
		return new SnappyPdf($this->binary, $this->options);
	}
}