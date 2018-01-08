<?php

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
}