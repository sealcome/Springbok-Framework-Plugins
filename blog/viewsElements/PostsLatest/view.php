<h2>{t 'plugin.blog.PostsLatestTitle'}</h2>
{f $posts as $post}
	<article itemscope itemtype="http://schema.org/Article" class="clearfix sepTop">
		{if!null $post->image->image_id}
			<?php $url=Config::$static_url.'/files/posts_images/'.$post->image->image_id; ?>
			{link '<img class="float_left mr10" itemprop="image" content="'.$url.'.jpg" width="75" height="75" src="'.$url.'-medium.jpg" />',$post->link(),array('escape'=>false)}
		{/if}
		<h3 class="noclear" itemprop="name">{link $post->title,$post->link(),array('itemprop'=>'url')}</h3>
		<span itemprop="dateCreated" content="{$post->created}"></span>
		<span itemprop="datePublished" content="{$post->published}"></span>
		{if!null $post->updated}<span itemprop="dateModified" content="{$post->updated}"></span>{/if}
		{=$post->excerpt}
	</article>
{/f}
