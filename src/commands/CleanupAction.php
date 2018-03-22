<?php namespace fortrabbit\AssetBundler\commands;

use Craft;
use yii\base\Action;


/**
 * Class CleanupActionAction
 *
 * @package fortrabbit\AssetBundler\commands
 */
class CleanupAction extends Action
{

    /**
     * Cleanup cpresources
     *
     * @return bool
     */
    public function run()
    {
        // rm -r web/cpresources
        // rm -r storage/assets/thumbs

        echo 'WIP' . PHP_EOL;
        return false;
    }


}

