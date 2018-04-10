<?php namespace fortrabbit\AssetBundler\commands;

use Craft;
use craft\helpers\Console;
use craft\helpers\FileHelper;
use fortrabbit\AssetBundler\Plugin;
use yii\base\Action;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;


/**
 * Class CleanupActionAction
 *
 * @package fortrabbit\AssetBundler\commands
 */
class CleanupAction extends Action
{

    /**
     * Cleanup temp directories
     *
     * @return bool
     */
    public function run()
    {
        $config = Craft::$app->getConfig()->getGeneral();
        $dirs   = [
            Craft::getAlias($config->resourceBasePath),
            Craft::getAlias('@webroot/' . Plugin::THUMBS_TMP_DIR),
            \Craft::$app->getPath()->getAssetThumbsPath(),
        ];

        foreach ($dirs as $dir) {
            try {
                echo PHP_EOL . '* clear ' . $dir;
                FileHelper::clearDirectory($dir);
            } catch (InvalidArgumentException $exception) {
            } catch (ErrorException $exception) {
                Console::stderr($exception->getMessage());

                return 1;
            }
            Console::stdout(' OK ', Console::FG_GREEN);
        }

        return 0;

    }


}

