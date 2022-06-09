<?xml version="1.0"?>
<rss version="2.0">
    <channel>
        <title>My awesome blog</title>
        <link>http://blog.test/</link>
        <description>The Awesome blog of Dominique Vilain</description>
        <language>fr-be</language>
        <?php foreach($data['posts'] as $post): ?>
        <item>
            <title><?= $post->title ?></title>
            <link>http://blog.test/?action=show&amp;resource=post&amp;slug=<?= $post->slug ?></link>
            <description><?= $post->excerpt ?></description>
            <pubDate><?= $post->published_at->toRfc822String() ?></pubDate>
        </item>
        <?php endforeach; ?>
    </channel>
</rss>