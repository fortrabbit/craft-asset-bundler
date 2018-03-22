<?php

namespace fortrabbit\AssetBundler;

use fortrabbit\AssetBundler\commands\CleanupAction;
use fortrabbit\AssetBundler\commands\PublishAction;
use yii\console\Controller as BaseConsoleController;


/**
 * fortrabbit AssetBundler - a tooling for assets
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
