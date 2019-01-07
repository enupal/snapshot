<?php
/**
 * Snapshot plugin for Craft CMS 3.x
 *
 * Snapshot or PDF generation from a url or a html page.
 *
 * @link      https://enupal.com
 * @copyright Copyright (c) 2018 Enupal
 */

namespace enupal\snapshot\contracts;

use Craft;
use craft\elements\Asset;
use craft\errors\InvalidSubpathException;
use craft\errors\InvalidVolumeException;
use craft\helpers\UrlHelper;
use craft\models\VolumeFolder;
use enupal\snapshot\models\Settings;
use enupal\snapshot\Snapshot;
use Knp\Snappy\GeneratorInterface;
use craft\base\Component;
use craft\helpers\FileHelper;
use enupal\snapshot\enums\SnapshotDefault;

/**
 * Base class for generator components.
 *
 * @method void generate(array | string $input, string $output, array $options, bool $overwrite)
 * @method void generateFromHtml(array | string $html, string $output, array $options, bool $overwrite)
 * @method string getOutput(array | string $input, array $options)
 * @method string getOutputFromHtml(array | string $html, array $options)
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
     * @var Settings
     */
    public $pluginSettings;

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
     * @param array  $settings display inline | url
     **/
    abstract public function displayHtml($html, $settings = null);

    /**
     * @param string $template
     * @param array  $settings display inline | url
     **/
    abstract public function displayTemplate($template, $settings = null);

    /**
     * @param array $options
     **/
    abstract public function getDefaultOptions($options = []);

    /**
     * @param string $url
     * @param array  $settings display inline | url
     **/
    abstract public function displayUrl($url, $settings = null);

    /**
     * @inheritDoc
     */
    public function __call($name, $parameters)
    {
        if (!method_exists('Knp\\Snappy\\GeneratorInterface', $name)) {
            return parent::__call($name, $parameters);
        }

        $this->binary = $this->getBinary();
        $generator = $this->getGenerator();
        // default command options
        $generator->setTemporaryFolder($this->resolveTempdir());

        return call_user_func_array([$generator, $name], $parameters);
    }

    /**
     * Resolves path to temporary directory.
     *
     * @return string|null
     */
    protected function resolveTempdir()
    {
        return $this->tempdir ?? Craft::$app->path->getTempPath().DIRECTORY_SEPARATOR.SnapshotDefault::TEMP_DIR;
    }

    /**
     * Resolves path to temporary public directory.
     *
     * @return string|null
     */
    protected function resolveTempPublicDir()
    {
        return Craft::$app->path->getTempPath().DIRECTORY_SEPARATOR.SnapshotDefault::TEMP_DIR.DIRECTORY_SEPARATOR.'public';
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

        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    /**
     * By default will generate a filename with a proper extension
     *
     * @param SnappySettings $settings
     * @param bool           $isPdf
     *
     * @return SnappySettings
     * @throws \Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function getSettings(SnappySettings $settings, $isPdf = true): SnappySettings
    {
        $extension = $isPdf ? '.pdf' : '.png';
        $isValidFileName = false;

        $this->options = $this->getDefaultOptions($settings->cliOptions);

        if ($settings->filename) {
            $isValidFileName = $this->validateFileName($settings->filename, $isPdf);
        }

        if (!$isValidFileName) {
            $info = Craft::$app->getInfo();
            $systemName = FileHelper::sanitizeFilename(
                $info->name,
                [
                    'asciiOnly' => true,
                    'separator' => '_'
                ]
            );
            $siteName = $systemName ?? 'enupal_snapshot';

            $settings->filename = $siteName.'_'.$this->getRandomStr().$extension;
        }

        $path = $this->resolveTempPublicDir().DIRECTORY_SEPARATOR.$settings->filename;

        if (file_exists($path)){
            unlink($path);
        }

        $settings->path = $path;

        return $settings;
    }

    /**
     * @return string
     */
    public function getSnapshotPath()
    {
        // Get the public path of Craft CMS
        $debugTrace = debug_backtrace();
        $initialCalledFile = count($debugTrace) ? $debugTrace[count($debugTrace) - 1]['file'] : __FILE__;
        $publicFolderPath = dirname($initialCalledFile);
        $publicFolderPath = $publicFolderPath.DIRECTORY_SEPARATOR.SnapshotDefault::PUBLIC_DIR;
        $publicFolderPath = FileHelper::normalizePath($publicFolderPath);

        return $publicFolderPath;
    }

    /**
     * @param SnappySettings $settingsModel
     * @return Asset
     * @throws InvalidSubpathException
     * @throws InvalidVolumeException
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\VolumeException
     * @throws \yii\base\Exception
     */
    public function getAsset($settingsModel)
    {
        $targetFolderId = $this->determineUploadFolderId($settingsModel);
        $folder = Craft::$app->getAssets()->getFolderById($targetFolderId);

        $asset = $this->checkIfFileExists($folder, $settingsModel->filename);

        if ($asset){
            if (!$this->pluginSettings->overrideFile){
                return $asset;
            }

            Craft::$app->getElements()->deleteElement($asset);
        }

        $asset = new Asset();
        $asset->tempFilePath = $settingsModel->path;
        $asset->filename = $settingsModel->filename;
        $asset->newFolderId = $targetFolderId;
        $asset->volumeId = $folder->volumeId;
        $asset->setScenario(Asset::SCENARIO_CREATE);
        $asset->avoidFilenameConflicts = true;
        Craft::$app->getElements()->saveElement($asset);

        return $asset;
    }

    /**
     * @param VolumeFolder $folder
     * @param $fileName
     * @return array|\craft\base\ElementInterface|Asset|null
     */
    private function checkIfFileExists($folder, $fileName)
    {
        $query = Asset::find();

        $query->volumeId = $folder->volumeId;
        $query->folderId = $folder->id;
        $query->filename = $fileName;

        return $query->one();
    }

    /**
     * Determine an upload folder id by looking at the settings and whether Element this field belongs to is new or not.
     * @param SnappySettings $settingsModel
     * @return int
     * @throws InvalidSubpathException if the folder subpath is not valid
     * @throws InvalidVolumeException if there's a problem with the field's volume configuration
     * @throws \craft\errors\VolumeException
     */
    private function determineUploadFolderId($settingsModel): int
    {
        $uploadVolume = $this->pluginSettings->singleUploadLocationSource;
        $subpath = $this->pluginSettings->singleUploadLocationSubpath;

        if ($settingsModel->singleUploadLocationSource !== null){
            $uploadVolume = $settingsModel->singleUploadLocationSource;
        }

        if ($settingsModel->singleUploadLocationSubpath !== null){
            $subpath = $settingsModel->singleUploadLocationSubpath;
        }

        $settingName = Craft::t('app', 'Upload Location');

        try {
            if (!$uploadVolume) {
                throw new InvalidVolumeException();
            }
            $folderId = $this->resolveVolumePathToFolderId($uploadVolume, $subpath);
        } catch (InvalidVolumeException $e) {
            throw new InvalidVolumeException(Craft::t('app', '{setting} setting is set to an invalid volume.', [
                'setting' => $settingName,
            ]), 0, $e);
        } catch (InvalidSubpathException $e) {
            // Existing element, so this is just a bad subpath
            throw new InvalidSubpathException($e->subpath, Craft::t('app', '{setting} setting has an invalid subpath (“{subpath}”).', [
                'setting' => $settingName,
                'subpath' => $e->subpath,
            ]), 0, $e);
        }

        return $folderId;
    }

    /**
     * Resolve a source path to it's folder ID by the source path and the matched source beginning.
     *
     * @param string $uploadSource
     * @param string $subpath
     * @return int
     * @throws InvalidSubpathException
     * @throws InvalidVolumeException if the volume root folder doesn’t exist
     * @throws \craft\errors\VolumeException
     */
    private function resolveVolumePathToFolderId(string $uploadSource, string $subpath): int
    {
        $assetsService = Craft::$app->getAssets();

        $volumeId = $this->volumeIdBySourceKey($uploadSource);

        // Make sure the volume and root folder actually exists
        if ($volumeId === null || ($rootFolder = $assetsService->getRootFolderByVolumeId($volumeId)) === null) {
            throw new InvalidVolumeException();
        }

        // Are we looking for a subfolder?
        $subpath = is_string($subpath) ? trim($subpath, '/') : '';

        if ($subpath === '') {
            $folderId = $rootFolder->id;
        } else {
            // Prepare the path by parsing tokens and normalizing slashes.
            try {
                $renderedSubpath = Craft::$app->getView()->renderObjectTemplate($subpath, Snapshot::$app->snapshots->getFieldVariables());
            } catch (\Throwable $e) {
                throw new InvalidSubpathException($subpath);
            }

            if (
                $renderedSubpath === '' ||
                trim($renderedSubpath, '/') != $renderedSubpath ||
                strpos($renderedSubpath, '//') !== false
            ) {
                throw new InvalidSubpathException($subpath);
            }

            $segments = explode('/', $renderedSubpath);
            foreach ($segments as &$segment) {
                $segment = FileHelper::sanitizeFilename($segment, [
                    'asciiOnly' => Craft::$app->getConfig()->getGeneral()->convertFilenamesToAscii
                ]);
            }
            unset($segment);
            $subpath = implode('/', $segments);

            $folder = $assetsService->findFolder([
                'volumeId' => $volumeId,
                'path' => $subpath.'/'
            ]);

            if (!$folder) {
                $volume = Craft::$app->getVolumes()->getVolumeById($volumeId);
                $folderId = $assetsService->ensureFolderByFullPathAndVolume($subpath, $volume);
            } else {
                $folderId = $folder->id;
            }
        }

        return $folderId;
    }

    /**
     * Returns a volume ID from an upload source key.
     *
     * @param string $sourceKey
     * @return int|null
     */
    public function volumeIdBySourceKey(string $sourceKey)
    {
        $parts = explode(':', $sourceKey, 2);

        if (count($parts) !== 2 || !is_numeric($parts[1])) {
            return null;
        }

        $folder = Craft::$app->getAssets()->getFolderById((int)$parts[1]);

        return $folder->volumeId ?? null;
    }

    /**
     * @param $url
     *
     * @return array
     */
    public function sanitizeUrl($url)
    {
        $urls = [];

        if (is_array($url)) {
            foreach ($url as $item) {
                if (is_string($item)) {
                    $urls[] = UrlHelper::url($item);
                }
            }
        } else {
            if (is_string($url)) {
                $urls[] = UrlHelper::url($url);
            }
        }

        return $urls;
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

        // we have a pdf
        if ($isPdf && $ext == 'pdf') {
            return true;
        }

        if (!$isPdf) {
            if (in_array($ext, $supportedImages)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns SnappySettings model
     *
     * @param array $settings
     *
     * @param bool  $isPdf
     *
     * @return SnappySettings
     * @throws \Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function populateSettings($settings, $isPdf = true): SnappySettings
    {
        $settingsModel = new SnappySettings();
        $settingsModel->setAttributes($settings, false);
        $settingsModel = $this->getSettings($settingsModel, $isPdf);

        return $settingsModel;
    }

    /**
     * @param SnappySettings $settingsModel
     * @throws \yii\base\ExitException
     */
    public function overrideSettings($settingsModel)
    {
        if ($settingsModel->singleUploadLocationSubpath !== null){
            Craft::dd($this->pluginSettings);
            $this->pluginSettings->singleUploadLocationSubpath = $settingsModel->singleUploadLocationSubpath;
        }

        if ($settingsModel->singleUploadLocationSource !== null){
            $this->pluginSettings->singleUploadLocationSource = $settingsModel->singleUploadLocationSource;
        }
    }
}