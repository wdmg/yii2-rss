[![Progress](https://img.shields.io/badge/required-Yii2_v2.0.33-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-rss/total.svg)](https://GitHub.com/wdmg/yii2-rss/releases/)
[![GitHub version](https://badge.fury.io/gh/wdmg/yii2-rss.svg)](https://github.com/wdmg/yii2-rss)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-rss.svg)](https://github.com/wdmg/yii2-rss/blob/master/LICENSE)

# Yii2 RSS
RSS-feed generator

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.33 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 Options](https://github.com/wdmg/yii2-options) module (optionality)
* [Yii2 News](https://github.com/wdmg/yii2-news) module (support)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-rss"`

After configure db connection, run the following command in the console:

`$ php yii rss/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations
  3) Flush RSS-feed cache

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-rss/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'rss' => [
            'class' => 'wdmg\rss\Module',
            'supportModels'  => [ // list of supported news models for displaying a news rss-feed
                'news' => 'wdmg\news\models\News',
            ],
            'cacheExpire' => 3600, // cache lifetime, `0` - for not use cache
            'channelOptions' => [], // default channel options
            'feedRoute' => '/' // default route to render RSS-feed (use "/" - for root)
        ],
        ...
    ],

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('rss')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [in progress development]
* v.1.0.1 - Added pagination, up to date dependencies
* v.1.0.0 - Added console, migrations and controller, support for Pages and News models