<?php
/**
 * AssetBundler plugin for Craft CMS 3.x
 *
 * Provides a console command to publish web/cpresources
 *
 * @link      http://www.fortrabbit.com
 * @copyright Copyright (c) 2018 Oliver Stark
 */

namespace fortrabbit\AssetBundler;


use Craft;
use craft\base\Plugin as BasePlugin;
use craft\console\Application as ConsoleApplication;
use Ottosmops\Md5sum\Md5sum;


/**
 * AssetBundler Plugin
 *
 * @author    Oliver Stark
 * @package   AssetBundler
 * @since     0.1.0
 *
 */
class Plugin extends BasePlugin
{
    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * AssetBundler::$plugin
     *
     * @var Plugin
     */
    public static $plugin;


    /**
     * Init plugin
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // AssetManager with hashCallback
        $this->registerAssetManager();

        // Extend SetupController
        if (Craft::$app instanceof ConsoleApplication) {
            Craft::$app->controllerMap['setup'] = AssetBundlesSetupController::class;
        }

    }

    /**
     * Registers AssetManager for web AND console requests
     * with a simplified hashCallback that does not
     * include a timestamp in the resource path.
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function registerAssetManager() {

        Craft::$app->set('assetManager', function () {
            $generalConfig = Craft::$app->getConfig()->getGeneral();
            $config        = [
                'class'           => ResourceAssetManager::class,
                'basePath'        => $generalConfig->resourceBasePath,
                'baseUrl'         => $generalConfig->resourceBaseUrl,
                'fileMode'        => $generalConfig->defaultFileMode,
                'dirMode'         => $generalConfig->defaultDirMode,
                'appendTimestamp' => false,
            ];

            return Craft::createObject($config);
        });

    }

}
