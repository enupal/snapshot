<?php

namespace enupal\pdf\contracts;

use Knp\Snappy\Image as SnayppyImage;

/**
 * PDF generator component.
 */
class PdfComponent extends BaseSnappy
{
	/**
	 * @return SnayppyImage
	 */
	protected function getGenerator()
	{
		return new SnayppyImage($this->binary, $this->options);
	}
}