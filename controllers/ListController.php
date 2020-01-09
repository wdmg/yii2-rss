<?php

namespace wdmg\rss\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ListController implements the CRUD actions
 */
class ListController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * Lists all RSS-feed items.
     * @return mixed
     */
    public function actionIndex()
    {
        $module = $this->module;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $module->getRssItems()
        ]);
        return $this->render('index', [
            'module' => $module,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Clear RSS-feed cache
     *
     * @return mixed
     */
    public function actionClear()
    {
        if ($cache = Yii::$app->getCache()) {
            if ($cache->delete(md5('rss-feed'))) {
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/rss', 'RSS-feed cache has been successfully flushing!')
                );
            } else {
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/rss', 'An error occurred while flushing the RSS-feed cache.')
                );
            }
        } else {
            Yii::$app->getSession()->setFlash(
                'warning',
                Yii::t('app/modules/rss', 'Error! Cache component not configured in the application.')
            );
        }

        return $this->redirect(['list/index']);
    }
}
