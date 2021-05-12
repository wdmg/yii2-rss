<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $path string */
?>

<div class="rss-feed-view">
    <?php
        $webPath = Url::to($path, true);
        $checkFailtureMessage = Yii::t('app/modules/rss', 'RSS-feed is invalid or not unavailable by URL: {url}', [
            'url' => Html::a($webPath, $webPath, [
                'target' => "_blank",
                'data-pjax' => "0"
            ])
        ]);
        $checkSuccessMessage = Yii::t('app/modules/rss', 'RSS-feed valid and available by URL: {url}', [
            'url' => Html::a($webPath, $webPath, [
                'target' => "_blank",
                'data-pjax' => "0"
            ])
        ]);
        $feedLimitMessage = Yii::t('app/modules/rss', 'Notice. Show only first 10 items of RSS-feed.');
    ?>
    <div id="rss-feed-check"></div>
    <div id="rss-feed-list" class="panel-group" role="tablist" aria-multiselectable="true"></div>
</div>
<?php $this->registerJs(<<< JS
    $(function() {
        $.ajax({
            url: '$webPath',
            type: 'GET',
            dataType: 'xml',
            error: function() {
                $('#rss-feed-check').append('<div class="alert alert-danger">$checkFailtureMessage</div>');
            },
            success: function(data) {
                
                var html = '';
                $('#rss-feed-check').append('<div class="alert alert-success">$checkSuccessMessage</div>');
                
                var count = 0;
                $.each($("item", $(data)), function(i, xml) {
                    
                    count++;
                    if (count >= 10)
                        return false;
                    
                    var _this = $(xml);
                    var content = _this.get(0).getElementsByTagNameNS("*", "encoded").item(0).textContent;
                    var item = {
                        title: _this.find("title").text(),
                        link: _this.find("link").text(),
                        content: (content) ? content : _this.find("description").text(),
                        pubDate: _this.find("pubDate").text(),
                        author: _this.find("author").text()
                    };
                    
                    html += '<div class="panel panel-primary">'
                    + '<div class="panel-heading" role="tab" id="rss-item-heading-' + i + '">'
                    + '<h4 class="panel-title"><span class="glyphicon glyphicon-globe"></span> '
                    + '<a role="button" data-toggle="collapse" data-parent="#rss-feed-list" href="#rss-item-' + i + '" aria-expanded="true" aria-controls="rss-item-' + i + '">'
                    + item.title
                    + '</a>'
                    + '</h4>'
                    + '</div>'
                    + '<div id="rss-item-' + i + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="rss-item-heading-' + i + '">'
                    + '<div class="panel-body">'
                    + item.content
                    + '</div>'
                    + '<div class="panel-footer">'
                    + '<span class="glyphicon glyphicon-calendar"></span> '
                    + item.pubDate
                    + item.author
                    + '<a href="' + item.link + '" target="_blank" data-pjax="0" class="pull-right">'
                    + item.link
                    + '</a>'
                    + '</div>'
                    + '</div>'
                    + '</div>';
                    
                });
                
                if (html.length > 0) {
                    $('#rss-feed-list').append(html);
                    
                    if (count >= 10)
                        $('#rss-feed-list').append('<hr/><div class="alert alert-warning">$feedLimitMessage</div>');
                    
                }

            }
        });
    });
JS
); ?>