<?php

namespace fortrabbit\AssetBundler\controllers;

use craft\elements\Asset as AssetElement;
use craft\web\Controller;
use yii\base\ErrorException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class AssetsController extends Controller
{
    /**
     * @param int    $assetId
     * @param int    $width
     * @param int    $height
     * @param string $ext
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionGenerateThumb($assetId, $width, $height, $ext)
    {
        $asset = AssetElement::find()->id($assetId)->one();

        if (!$asset) {
            throw new NotFoundHttpException('Invalid asset ID: ' . $assetId);
        }
        try {
            $url = \Craft::$app->getAssets()->getThumbUrl($asset, $width, $height, true);
        } catch (\Exception $e) {
            throw new HttpException(410, 'Unable to generate Thumb  ID: ' . $assetId);
        }

        return $this->redirect($url);
    }


}
