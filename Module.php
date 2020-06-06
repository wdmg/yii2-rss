<?php

namespace wdmg\rss;

/**
 * Yii2 RSS-feeds manager
 *
 * @category        Module
 * @version         1.0.3
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-rss
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * RSS-feed module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\rss\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "list/index";

    /**
     * @var string, the name of module
     */
    public $name = "RSS";

    /**
     * @var string, the description of module
     */
    public $description = "RSS-feeds manager";

    /**
     * @var array list of supported news models for displaying a news rss-feed
     */
    public $supportModels = [
        'news' => 'wdmg\news\models\News',
        'blog' => 'wdmg\blog\models\Posts'
    ];

    /**
     * @var int cache lifetime, `0` - for not use cache
     */
    public $cacheExpire = 3600; // 1 hr.

    /**
     * @var array default channel options
     */
    public $channelOptions = [];

    /**
     * @var string default route to render RSS-feed (use "/" - for root)
     */
    public $feedRoute = "/rss";

    /**
     * @var string the module version
     */
    private $version = "1.0.3";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 5;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

        // Process and normalize route for frontend
        $this->feedRoute = self::normalizeRoute($this->feedRoute);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        $items = [
            'label' => $this->name,
            'icon' => 'fa fa-fw fa-rss',
            'url' => [$this->routePrefix . '/'. $this->id],
            'active' => (in_array(\Yii::$app->controller->module->id, [$this->id]) &&  Yii::$app->controller->id == 'list'),
        ];
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        if (isset(Yii::$app->params["rss.supportModels"]))
            $this->supportModels = Yii::$app->params["rss.supportModels"];

        if (isset(Yii::$app->params["rss.cacheExpire"]))
            $this->cacheExpire = Yii::$app->params["rss.cacheExpire"];

        if (isset(Yii::$app->params["rss.channelOptions"]))
            $this->channelOptions = Yii::$app->params["rss.channelOptions"];

        if (isset(Yii::$app->params["rss.feedRoute"]))
            $this->feedRoute = Yii::$app->params["rss.feedRoute"];

        if (!isset($this->supportModels))
            throw new InvalidConfigException("Required module property `supportModels` isn't set.");

        if (!isset($this->cacheExpire))
            throw new InvalidConfigException("Required module property `cacheExpire` isn't set.");

        if (!isset($this->channelOptions))
            throw new InvalidConfigException("Required module property `channelOptions` isn't set.");

        if (!isset($this->feedRoute))
            throw new InvalidConfigException("Required module property `feedRoute` isn't set.");

        if (!is_array($this->supportModels))
            throw new InvalidConfigException("Module property `supportModels` must be array.");

        if (!is_array($this->channelOptions))
            throw new InvalidConfigException("Module property `channelOptions` must be array.");

        if (!is_integer($this->cacheExpire))
            throw new InvalidConfigException("Module property `cacheExpire` must be integer.");

        if (!is_string($this->feedRoute))
            throw new InvalidConfigException("Module property `feedRoute` must be a string.");

        // Add route to pass RSS-feed in frontend
        $feedRoute = $this->feedRoute;
        if (empty($feedRoute) || $feedRoute == "/") {
            $app->getUrlManager()->addRules([
                [
                    'pattern' => '/rss',
                    'route' => 'admin/rss/default',
                    'suffix' => '.xml'
                ],
                '/rss.xml' => 'admin/rss/default'
            ], true);
        } else if (is_string($feedRoute)) {
            $app->getUrlManager()->addRules([
                [
                    'pattern' => $feedRoute . '/feed',
                    'route' => 'admin/rss/default',
                    'suffix' => '.xml'
                ],
                $feedRoute . '/feed.xml' => 'admin/rss/default'
            ], true);
        }

        // Attach to events of create/change/remove of models for the subsequent clearing cache of feeds
        if (!($app instanceof \yii\console\Application)) {
            if ($cache = $app->getCache()) {
                if (is_array($models = $this->supportModels)) {
                    foreach ($models as $name => $class) {
                        if (class_exists($class)) {
                            $model = new $class();
                            \yii\base\Event::on($class, $model::EVENT_AFTER_INSERT, function ($event) use ($cache) {
                                $cache->delete(md5('rss-feed'));
                            });
                            \yii\base\Event::on($class, $model::EVENT_AFTER_UPDATE, function ($event) use ($cache) {
                                $cache->delete(md5('rss-feed'));
                            });
                            \yii\base\Event::on($class, $model::EVENT_AFTER_DELETE, function ($event) use ($cache) {
                                $cache->delete(md5('rss-feed'));
                            });
                        }
                    }
                }
            }
        }
    }

    /**
     * Generate current RSS-feed URL
     *
     * @return null|string
     */
    public function getFeedURL() {
        $url = null;
        $feedRoute = $this->feedRoute;
        if (empty($feedRoute) || $feedRoute == "/") {
            $url = Url::to('/rss.xml', true);
        } else {
            $url = Url::to($feedRoute . '/feed.xml', true);
        }
        return $url;
    }



    /**
     * Get items for building a rss-feed
     *
     * @return array
     */
    public function getRssItems() {
        $items = [];

        if (is_array($models = $this->supportModels)) {
            foreach ($models as $name => $class) {

                // If class of model exist
                if (class_exists($class)) {

                    $model = new $class();

                    // If module is loaded
                    if ($model->getModule()) {
                        $append = [];

                        foreach ($model->getAllPublished(['in_rss' => true]) as $item) {
                            $append[] = [
                                'url' => (isset($item->url)) ? $item->url : null,
                                'name' => (isset($item->name)) ? $item->name : null,
                                'title' => (isset($item->title)) ? $item->title : null,
                                'image' => (isset($item->image)) ? $model->getImagePath(true) . '/' . $item->image : null,
                                'content' => (isset($item->content)) ? $item->content : null,
                                'updated_at' => (isset($item->updated_at)) ? $item->updated_at : null
                            ];
                        };
                        $items = ArrayHelper::merge($items, $append);
                    }
                }
            }
        }

        return $items;
    }
}