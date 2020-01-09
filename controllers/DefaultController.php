<?php

namespace wdmg\rss\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * DefaultController implements actions
 */
class DefaultController extends Controller
{

    public $defaultAction = 'rss';

    /**
     * Displays the rss-feed in xml for frontend.
     *
     * @return string
     */
    public function actionRss() {

        $module = $this->module;
        if ($module->cacheExpire !== 0 && ($cache = Yii::$app->getCache())) {
            $data = $cache->getOrSet(md5('rss-feed'), function () use ($module) {
                return [
                    'items' => $module->getRssItems(),
                    'builded_at' => date('r')
                ];
            }, intval($module->cacheExpire));
        } else {
            $data = [
                'items' => $module->getRssItems(),
                'builded_at' => date('r')
            ];
        }

        $channel = [];
        if (is_array($module->channelOptions))
            $channel = $module->channelOptions;

        if (!isset($channel['title']))
            $channel['title'] = Yii::$app->name;

        if (!isset($channel['feed_link']))
            $channel['feed_link'] = Yii::$app->getRequest()->getAbsoluteUrl();

        if (!isset($channel['link']))
            $channel['link'] = Url::base(true);

        if (!isset($channel['language']))
            $channel['language'] = Yii::$app->language;

        if (!isset($channel['update_period']))
            $channel['update_period'] = 'hourly';

        if (!isset($channel['update_frequency']))
            $channel['update_frequency'] = 1;

        if (!isset($channel['generator']))
            $channel['generator'] = 'Yii2 RSS-feed';

        if (isset($channel['image']) && !isset($channel['image']['link']))
            $channel['image']['link'] = $channel['link'];

        if (isset($channel['image']) && !isset($channel['image']['title']))
            $channel['image']['title'] = $channel['title'];

        $channel['last_build'] = $data['builded_at'];

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->getResponse()->getHeaders()->set('Content-Type', 'text/xml; charset=UTF-8');
        return $this->renderPartial('rss', [
            'channel' => $channel,
            'items' => $data['items']
        ]);
    }

}
