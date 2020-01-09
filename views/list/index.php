<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $module->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small>
    </h1>
</div>
<div class="rss-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'url',
            'name',
            'title',
            'image',
            'description',
            /*'content',*/
            'updated_at',
            'status'
        ],
    ]); ?>
    <hr/>
    <div class="btn-group">
        <?= Html::a(Yii::t('app/modules/rss', 'Clear cache'), ['list/clear'], ['class' => 'btn btn-info']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
