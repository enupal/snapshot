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
	 * @param array $options display inline | url
	**/
	abstract public function displayHtml($html, $options);

	/**
	 * @param string $template
	 * @param array $options display inline | url
	**/
	abstract public function displayTemplate($template, $options);

	/**
	 * @param string $url
	 * @param array $options display inline | url
	**/
	abstract public function displayUrl($url, $option);

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
}