<?php

use yii\helpers\Html;

?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL ?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>
    <channel>
<?php if (isset($channel['title'])) : ?>
        <title><?= $channel['title']; ?></title>
<?php endif; ?>
<?php if (isset($channel['feed_link'])) : ?>
        <atom:link href="<?= Html::encode($channel['feed_link']); ?>" rel="self" type="application/rss+xml" />
<?php endif; ?>
<?php if (isset($channel['link'])) : ?>
        <link><?= Html::encode($channel['link']); ?></link>
<?php endif; ?>
<?php if (isset($channel['description'])) : ?>
        <description><?= $channel['description']; ?></description>
<?php endif; ?>
<?php if (isset($channel['last_build'])) : ?>
        <lastBuildDate><?= $channel['last_build']; ?></lastBuildDate>
<?php endif; ?>
<?php if (isset($channel['language'])) : ?>
        <language><?= $channel['language']; ?></language>
<?php endif; ?>
<?php if (isset($channel['update_period'])) : ?>
        <sy:updatePeriod><?= $channel['update_period']; ?></sy:updatePeriod>
<?php endif; ?>
<?php if (isset($channel['update_frequency'])) : ?>
        <sy:updateFrequency><?= $channel['update_frequency']; ?></sy:updateFrequency>
<?php endif; ?>
<?php if (isset($channel['generator'])) : ?>
        <generator><?= $channel['generator']; ?></generator>
<?php endif; ?>
<?php if (isset($channel['image'])) : ?>
        <image>
            <url><?= Html::encode($channel['image']['url']); ?></url>
            <title><?= $channel['image']['title']; ?></title>
            <link><?= $channel['image']['link']; ?></link>
            <width><?= $channel['image']['width']; ?></width>
            <height><?= $channel['image']['height']; ?></height>
        </image>
<?php endif; ?>
<?php foreach ($items as $item): ?>
<?php if (isset($item['url']) && isset($item['content'])) : ?>
        <item>
            <title><?= ($item['title']) ? $item['title'] : $item['name']; ?></title>
            <link><?= Html::encode($item['url']); ?></link>
            <pubDate><?= $item['updated_at']; ?></pubDate>
<?php if (isset($item['image'])) :?>
            <media:content xmlns:media="http://search.yahoo.com/mrss/" medium="image" url="<?= $item['image']; ?>" />
<?php endif; ?>
<?php if (isset($item['description'])) : ?>
            <description>
                <![CDATA[
                <?= $item['description']; ?>
                ]]>
            </description>
<?php endif; ?>
            <content:encoded>
                <![CDATA[
                <?= $item['content']; ?>
                ]]>
            </content:encoded>
        </item>
<?php endif; ?><?php endforeach; ?>
    </channel>
</rss>