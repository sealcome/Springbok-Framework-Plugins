<?php HBreadcrumbs::set(array('Articles'=>'/posts')); $v=new AjaxBreadcrumbsPageView('Edition article','mr200'); ?>

<?php $form=HForm::create('Post',array('id'=>'formPostEdit','novalidate'=>true),'div',false); ?>
<div class="fixed right w200">
	<div class="content">
		<div class="center">
			{=$form->select('status',Post::statusesList())}
			{iconLink 'delete','Supprimer cet article','/posts/delete/'.$post->id,array('confirm'=>'Êtes-vous sûr de vouloir supprimer cet article ?')}
		</div>
		<div id="PostTags" class="content block4">
			<b>Tags</b>
			<? HHtml::ajaxCRDSelectFiltrable('/postTags',PostsTag::findListName(),$post->tags,array('url'=>'/'.$id,'allowNew'=>true,'selectAttributes'=>array('style'=>'width:135px'))) ?>
		</div>
		<div id="PostCategories" class="content block4 mt10">
			<b>Catégories</b>
			<? HHtml::ajaxCRDSelectFiltrable('/postCategories',PostsCategory::findListName(),$post->categories,array('url'=>'/'.$id,'selectAttributes'=>array('style'=>'width:135px'))) ?>
		</div>
		<? $form->submit(true,array(),array('class'=>'submit center')); ?>
	</div>
</div>

<div class="variable padding">
	<div id="editTabs" class="tabs">
		<ul><li>{iconLink 'page','Article','#editTab1'}</li><li>{iconLink 'pageEdit','Contenu','#editTab2'}</li><li>{iconLink 'time','Historique','/postHistories/view/'.$id}</li><li>{iconLink 'pageLink','Articles liés','/postPosts/view/'.$id}</li></ul>
		<div id="editTab1" class="clearfix">
			{=$form->input('name',array('class'=>'wp100'))}
			
			/* IF(blog_personalizeAuthors_enabled) */
			<div class="float_right clearfix mt10 ml10 content block4" style="width:180px">
				<b>Auteurs</b>
				<? HHtml::ajaxCRDSelectFiltrable('/postAuthors',PostsAuthor::findListName(),$post->authors,array('url'=>'/'.$id,'selectAttributes'=>array('style'=>'width:135px'))) ?>
				{=$form->submit(true,array(),array('class'=>'submit center'))}
			</div>
			/* /IF */
			
			<div class="content mt10 mr200 clearfix">
				<div id="divPostImage">
					{ifnull $post->image->image_id} Pas d'image associée. {link 'Sélectionner une image','#',array('onclick'=>'return _.posts.selectImage('.$id.')')}
					{else}<?php $image=$post->image ?>{include post_image.php}{/if}
				</div>
				{=$form->submit(true,array(),array('class'=>'submit center'))}
			</div>
			
			<div class="sepTop content block4">
				<? View::element('seo',array('model'=>$post,'form'=>$form)) ?>
				<? $form->submit(true,array(),array('class'=>'submit center')); ?>
			</div>
			
			<br class="clear"/>
		</div>
		<div id="editTab2">
			<span class="bold" style="color:red;text-shadow:#777 1px 0 0;">Attention au bug de sauvegarde sur Firefox !</span>
			<div class="alignRight"><a href="#" onclick="S.tinymce.switchtoHtml('PostExcerpt');return false">HTML</a> - <a href="#" onclick="S.tinymce.switchtoVisual('PostExcerpt');return false">Visuel</a></div>
			<? $form->textarea('excerpt') ?>
			
			<div class="alignRight mt10"><a href="#" onclick="S.tinymce.switchtoHtml('PostContent');return false">HTML</a> - <a href="#" onclick="S.tinymce.switchtoVisual('PostContent');return false">Visuel</a></div>
			<? $form->textarea('content') ?>
			
			<? $form->submit(); ?>
		</div>
	</div>
</div>
<? HHtml::jsInline('_.posts.edit('.$id.')') ?>
<? $form->end(false); ?>