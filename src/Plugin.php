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
use craft\events\RegisterUrlRulesEvent;
use craft\web\Controller;
use craft\web\UrlManager;
use fortrabbit\AssetBundler\controllers\AssetsController;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\base\Event;


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

    const THUMBS_TMP_DIR = 't';

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

        // Register console commands
        if (Craft::$app instanceof ConsoleApplication) {
            Craft::$app->controllerMap['asset-bundler'] = Commands::class;
        }

        // Register route for Asset thumbs generation
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                // /t/{asset_id}/thumb-{width}x{height}.{ext}?v={modified_date}
                $event->rules[self::THUMBS_TMP_DIR . '/<assetId:\d+>/thumb-<width:\d+>x<height:\d+>.<ext>'] = 'asset-bundler/assets/generate-thumb';

            }
        );



    }

    /**
     * Registers AssetManager for web AND console requests
     * with a simplified hashCallback that does not
     * include a timestamp in the resource path.
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function registerAssetManager()
    {

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
