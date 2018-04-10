<?php
/**
 * Created by PhpStorm.
 * User: os
 * Date: 21.03.18
 * Time: 23:25
 */

namespace fortrabbit\AssetBundler;


use craft\web\AssetManager;

/**
 * Class ResourceAssetManager
 *
 * @package fortrabbit\AssetBundler
 *
 * @property $modifiedFiles
 * @method getRevision()
 * @method updateRevision()
 */
class ResourceAssetManager extends AssetManager
{
    protected $assetThumbsPath;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->assetThumbsPath = \Craft::$app->getPath()->getAssetThumbsPath();
    }

    public function behaviors()
    {
        return ['RevisionableResource' => RevisionableResourceBehavior::class];
    }

    /**
     * @param $path
     *
     * @return mixed|string
     */
    protected function hash($path)
    {
        // Don't hash thumb path
        if (stristr($path, $this->assetThumbsPath)) {

            $this->basePath        = Plugin::THUMBS_TMP_DIR;
            $this->baseUrl         = '/' . Plugin::THUMBS_TMP_DIR;
            $this->appendTimestamp = true;

            return $this->extractAssetId($path);
        }

        $revision = $this->getRevision();

        return $revision . DIRECTORY_SEPARATOR . sprintf('%x', crc32($path));
    }


    /**
     * @param $src
     * @param $options
     *
     * @return array
     * @throws \Exception
     */
    protected function publishDirectory($src, $options): array
    {
        $this->getRevision();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $mtime = $fileinfo->getMTime();
                if ($mtime > $this->revision) {
                    return parent::publishDirectory($src, ['forceCopy' => true]);
                }
            }
        }

        return parent::publishDirectory($src, $options);

    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    protected function extractAssetId(string $path)
    {
        $pos   = (is_file($path)) ? 2 : 1;
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $id    = $parts[count($parts) - $pos];

        return $id;
    }
}
