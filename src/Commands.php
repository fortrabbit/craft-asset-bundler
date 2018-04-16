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

    public $verbose = false;

    public $forceRevision = 0;

    public function options($actionID)
    {
        return ($actionID === 'publish') ? ['verbose', 'forceRevision'] : [];
    }

    public function optionAliases()
    {
        return [
            'v' => 'verbose',
            'r' => 'force-revision'
        ];
    }

    public function actions()
    {
        return [
            'publish' => [
                'class'         => PublishAction::class,
                'verbose'       => $this->verbose,
                'forceRevision' => $this->forceRevision
            ],
            'cleanup' => CleanupAction::class,
        ];
    }


}
