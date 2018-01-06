<?php

namespace enupal\pdf\contracts;

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
		return $this->tempdir ?? Craft::$app->path->getTempPath().DIRECTORY_SEPARATOR.'enupalpdftemp';
	}
}