<?php

namespace enupal\snapshot\contracts;

use craft\base\Component;
use Knp\Snappy\GeneratorInterface;
use Craft;

/**
 * Base class for generator components.
 *
 * @method void generate(array|string $input, string $output, array $options, bool $overwrite)
 * @method void generateFromHtml(array|string $html, string $output, array $options, bool $overwrite)
 * @method string getOutput(array|string $input, array $options)
 * @method string getOutputFromHtml(array|string $html, array $options)
 */
abstract class BaseSnappy extends Component
{
	/**
	 * @var string Path to wkhtmltox binary
	 */
	public $binary;

	/**
	 * @var array Command line options
	 */
	public $options = [];

	/**
	 * @var string Path to directory used for temporary files
	 */
	public $tempdir;

	/**
	 * Returns generator instance.
	 *
	 * @return \Knp\Snappy\GeneratorInterface
	 */
	abstract protected function getGenerator(): GeneratorInterface;

	/**
	 * Set binary path
	 *
	 * @return String
	 */
	abstract protected function getBinary();

	/**
	 * @param string $html
	 * @param array $settings display inline | url
	**/
	abstract public function displayHtml($html, $settings = null);

	/**
	 * @param string $template
	 * @param array $settings display inline | url
	**/
	abstract public function displayTemplate($template, $settings = null);

	/**
	 * @param string $url
	 * @param array $settings display inline | url
	**/
	abstract public function displayUrl($url, $settings = null);

	/**
	 * @inheritDoc
	 */
	public function __call($name, $parameters)
	{
		if (! method_exists('Knp\\Snappy\\GeneratorInterface', $name)) {
			return parent::__call($name, $parameters);
		}

		$this->binary = $this->getBinary();
		$generator    = $this->getGenerator();
		$generator->setTemporaryFolder($this->resolveTempdir());

		return call_user_func_array(array($generator, $name), $parameters);
	}

	/**
	 * Resolves path to temporary directory.
	 *
	 * @return string|null
	 */
	protected function resolveTempdir()
	{
		return $this->tempdir ?? Craft::$app->path->getTempPath().DIRECTORY_SEPARATOR.'enupalsnapshottemp';
	}

	/**
	 * Generate a random string, using a cryptographically secure
	 * pseudorandom number generator (random_int)
	 *
	 * For PHP 7, random_int is a PHP core function
	 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
	 *
	 * @param int $length      How many characters do we want?
	 * @param string $keyspace A string of all possible characters
	 *                         to select from
	 * @return string
	 */
	public function getRandomStr($length = 10, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
		$str = '';
		$max = mb_strlen($keyspace, '8bit') - 1;

		for ($i = 0; $i < $length; ++$i)
		{
				$str .= $keyspace[random_int(0, $max)];
		}
		return $str;
	}

	/**
	 * By default let's display the output in the browser
	 *
	 * @return []
	*/
	public function getDefaultSettings()
	{
		return [
			'cliOptions' => [],
			// default filename without extension
			'filename' => $this->getRandomStr(),
			// true => display on browser else return download url
			'inline' => true
		];
	}

	/**
	 * By default will generate a filename with a proper extension
	 *
	 * @param array $settings
	 * @param bool $isPdf
	*/
	public function getSettings($settings, $isPdf = true)
	{
		$defaultSettings = $this->getDefaultSettings();
		$extension = $isPdf ? '.pdf' : '.png';
		$isValidFileName = false;

		if (isset($settings['cliOptions']))
		{
			$this->options = $settings['cliOptions'];
		}

		if(isset($settings['filename']))
		{
			$isValidFileName = $this->validateFileName($settings['filename'], $isPdf);
		}

		if(!$isValidFileName)
		{
			$settings['filename'] = $defaultSettings['filename'].$extension;
		}

		// @todo - support volumes
		$path = Craft::$app->path->getTempPath().DIRECTORY_SEPARATOR.'enupalsnapshot'.DIRECTORY_SEPARATOR.$settings['filename'];

		// let's delete any duplicate filename
		if (file_exists($path))
		{
			unlink($path);
		}

		$settings['path'] = $path;

		if (!isset($settings['inline']))
		{
			$settings['inline'] = $defaultSettings['inline'];
		}

		return $settings;
	}

	public function displayInline($path, $settings)
	{
		Craft::$app->response->sendFile($path, $settings['filename'], ['inline'=>true]);
	}

	public function validateFileName($fileName, $isPdf)
	{
		$supportedImages = [
			'gif',
			'jpg',
			'jpeg',
			'png'
		];

		$ext = pathinfo($fileName, PATHINFO_EXTENSION);

		if(!$isPdf)
		{
			if(in_array($ext, $supportedImages))
			{
				return true;
			}
		}
		// we have a pdf
		else
		{
			if($ext == 'pdf')
			{
				return true;
			}
		}

		return false;
	}
}