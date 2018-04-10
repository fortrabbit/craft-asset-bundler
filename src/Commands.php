<?php

namespace fortrabbit\AssetBundler;

use fortrabbit\AssetBundler\commands\CleanupAction;
use fortrabbit\AssetBundler\commands\PublishAction;
use yii\console\Controller as BaseConsoleController;


/**
 * Tooling for assets in load balanced environments
 */
class Commands extends BaseConsoleController
{

    public $defaultAction = 'publish';


    public function actions()
    {
        return [
            'publish'   => PublishAction::class,
            'cleanup'   => CleanupAction::class,
        ];
    }



}
