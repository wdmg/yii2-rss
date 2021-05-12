<?php

use yii\bootstrap\Modal;
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
    <?php if ($rss_feed_url = $module->getFeedURL()) : ?>
        <p><?= Yii::t('app/modules/rss', 'RSS-feed of the current site is available at: {url}',
                ['url' => Html::a($rss_feed_url, $rss_feed_url, ['target' => '_blank', 'data-pjax' => 0])]
            ) ?></p>
    <?php endif; ?>
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
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/rss', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/rss', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/rss', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/rss', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div class="btn-group">
        <?= Html::a(Yii::t('app/modules/rss', 'Clear cache'), ['list/clear'], ['class' => 'btn btn-danger']) ?>
        <?= Html::a(Yii::t('app/modules/rss', 'View RSS-feed'), ['list/view'], [
            'class' => 'btn btn-info',
            'data-toggle' => 'modal',
            'data-target' => '#viewRSSFeedModal',
            'data-pjax' => '1'
        ]) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php
$this->registerJs(<<< JS
    $('body').delegate('[data-toggle="modal"][data-target]', 'click', function(event) {
        
        event.preventDefault();
        var target = $(event.target).data('target');
        $.get(
            $(this).attr('href'),
            function (data) {
                
                $(target).find('.modal-body').html($(data).remove('.modal-footer'));
                if ($(data).find('.modal-footer').length > 0) {
                    $(target).find('.modal-footer').remove();
                    $(target).find('.modal-content').append($(data).find('.modal-footer'));
                }
                
                if ($(target).find('button[type="submit"]').length > 0 && $(target).find('form').length > 0) {
                    $(target).find('button[type="submit"]').on('click', function(event) {
                        event.preventDefault();
                        $(target).find('form').submit();
                    });
                }
                
                $(target).modal();
            }  
        );
    });
JS
); ?>

<?php echo $this->render('../_debug'); ?>

<?php Modal::begin([
    'id' => 'viewRSSFeedModal',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/rss', 'View RSS-feed').'</h4>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>
