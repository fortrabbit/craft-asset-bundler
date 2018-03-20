<?php namespace fortrabbit\AssetBundler;

use Craft;
use craft\console\controllers\SetupController;
use craft\helpers\FileHelper;
use craft\web\View;
use Symfony\Component\Process\Exception\RuntimeException as ProcessRuntimeException;
use Symfony\Component\Process\Process;
use yii\base\InvalidArgumentException;

/**
 * Craft CMS setup installer (+ setup/asset-bundles action).
 */
class AssetBundlesSetupController extends SetupController
{

    /**
     * Publishes asset bundles
     */
    public function actionAssetBundles()
    {
        // Prepare for console
        $this->adjustAliases();

        // Make @webroot/cpresources) exist
        $this->createBasePath();

        // Register aliases for disabled plugins as well
        foreach (\Craft::$app->getPlugins()->getAllPluginInfo() as $plugin) {
            if (!$plugin['isEnabled']) {
                foreach ($plugin['aliases'] as $alias => $path) {
                    \Craft::setAlias($alias, $path);
                }
            }
        }

        $warnings       = [];
        $composerLoader = require \Craft::getAlias('@vendor/autoload.php');

        foreach (array_keys($composerLoader->getClassMap()) as $class) {

            if (!strstr($class, 'Asset')) {
                continue;
            }

            $ref    = new \ReflectionClass($class);
            $parent = $ref->getParentClass();

            if ($parent && strstr($parent->getName(), 'AssetBundle')) {
                try {
                    \Craft::$app->getView()->registerAssetBundle($class);
                } catch (\Exception $e) {
                    if ($e instanceof InvalidArgumentException) {
                        $warnings[] = $e->getMessage();
                    }
                }
            }
        }

        // Result
        echo PHP_EOL . count(\Craft::$app->getView()->assetBundles) . ' Bundles registered' . PHP_EOL . PHP_EOL;

        // Warnings
        if ($warnings) {
            foreach (array_unique($warnings) as $message) {
                echo "❌  $message" . PHP_EOL;
            }
        }

        return (count(\Craft::$app->getView()->assetBundles)) ? 0 : 1;

    }


    protected function adjustAliases()
    {
        // Make 'web' aliases available in console
        Craft::setAlias('@webroot', CRAFT_BASE_PATH . '/web');
        Craft::setAlias('@web', '/');

        // Override where Yii should find its asset deps
        $libPath = Craft::getAlias('@lib');
        Craft::setAlias('@bower/bootstrap/dist', $libPath . '/bootstrap');
        Craft::setAlias('@bower/jquery/dist', $libPath . '/jquery');
        Craft::setAlias('@bower/inputmask/dist', $libPath . '/inputmask');
        Craft::setAlias('@bower/punycode', $libPath . '/punycode');
        Craft::setAlias('@bower/yii2-pjax', $libPath . '/yii2-pjax');
    }

    protected function createBasePath()
    {
        $basePath = Craft::getAlias(Craft::$app->getConfig()->getGeneral()->resourceBasePath);
        if (!is_dir($basePath)) {
            if (FileHelper::createDirectory($basePath)) {
                echo PHP_EOL . $basePath . ' created' . PHP_EOL;
            }
        }
    }
}
