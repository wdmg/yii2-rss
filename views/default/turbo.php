<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL ?>
<rss version="2.0"
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
>
    <channel>
<?php if (isset($channel['title'])) : ?>
        <title><?= $channel['title']; ?></title>
<?php endif; ?>
<?php if (isset($channel['link'])) : ?>
        <link><?= $channel['link']; ?></link>
<?php endif; ?>
<?php if (isset($channel['description'])) : ?>
        <description><?= $channel['description']; ?></description>
<?php endif; ?>
<?php if (isset($channel['language'])) : ?>
        <language><?= $channel['language']; ?></language>
<?php endif; ?>
<?php if (isset($channel['analytics'])) : ?>
        <turbo:analytics><?= $channel['analytics']; ?></turbo:analytics>
<?php endif; ?>
<?php if (isset($channel['adnetwork'])) : ?>
        <turbo:adNetwork><?= $channel['adnetwork']; ?></turbo:adNetwork>
<?php endif; ?>
<?php foreach ($items as $item): ?>
<?php if (isset($item['url']) && isset($item['content'])) : ?>
        <item turbo="<?= ($item['status']) ? 'true' : 'false'; ?>">
            <link><?= $item['url']; ?></link>
<?php if ($item['status']) : ?>
            <pubDate><?= $item['updated_at']; ?></pubDate>
            <turbo:content>
            <![CDATA[
                <header>
                    <h1><?= ($item['title']) ? $item['title'] : $item['name']; ?></h1>
                </header>
<?php if (isset($item['image'])) :?>
                <figure>
                    <img src="<?= $item['image']; ?>" />
<?php if (isset($item['image_title'])) :?>
                    <figcaption><?= $item['image_title']; ?></figcaption>
<?php endif; ?>
                </figure>
<?php endif; ?>
                <?= $item['content']; ?>

            ]]>
            </turbo:content>
<?php endif; ?>
        </item>
<?php endif; ?><?php endforeach; ?>
    </channel>
</rss>