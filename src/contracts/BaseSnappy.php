<?php

namespace enupal\snapshot\contracts;

use Craft;
use craft\helpers\UrlHelper;
use Knp\Snappy\GeneratorInterface;
use craft\base\Component;
use craft\helpers\FileHelper;
use yii\web\Response;

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
	 * @param int    $length   How many characters do we want?
	 * @param string $keyspace A string of all possible characters
	 *                         to select from
	 *
	 * @return string
	 * @throws \Exception
	 * @throws \Exception
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
	 * By default will generate a filename with a proper extension
	 *
	 * @param SnappySettings $settings
	 * @param bool $isPdf
	*/
	public function getSettings(SnappySettings $settings, $isPdf = true): SnappySettings
	{
		$extension = $isPdf ? '.pdf' : '.png';
		$isValidFileName = false;

		if ($settings->cliOptions)
		{
			$this->options = $settings->cliOptions;
		}

		if($settings->filename)
		{
			$isValidFileName = $this->validateFileName($settings->filename, $isPdf);
		}

		if(!$isValidFileName)
		{
			$settings->filename = $this->getRandomStr().$extension;
		}

		// @todo - support volumes
		$path = $this->getSnapshotPath().DIRECTORY_SEPARATOR.$settings->filename;

		// let's delete any duplicate filename
		if (file_exists($path))
		{
			unlink($path);
		}

		$settings->path = $path;

		return $settings;
	}

	public function getSnapshotPath()
	{
		// Get the public path of Craft CMS
		$debugTrace = debug_backtrace();
		$initialCalledFile = count($debugTrace) ? $debugTrace[count($debugTrace) - 1]['file'] : __FILE__;
		$publicFolderPath = dirname($initialCalledFile);
		$publicFolderPath = $publicFolderPath.DIRECTORY_SEPARATOR."enupalsnapshot";
		$publicFolderPath = FileHelper::normalizePath($publicFolderPath);

		return $publicFolderPath;
	}

	public function getPublicUrl($filename)
	{
		// Get the public path url for download
		$file = "enupalsnapshot/".$filename;

		return UrlHelper::url($file);
	}

	public function displayInline(SnappySettings $settings): Response
	{
		Craft::$app->response->sendFile($settings->path, $settings->filename, ['inline'=>true]);
		exit();
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