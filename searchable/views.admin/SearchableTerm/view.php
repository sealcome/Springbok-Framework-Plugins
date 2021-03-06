<?php new AjaxContentView('Term: '.$term->term) ?>

{=$form=SearchablesTerm::Form('term')->id('formTermEdit')->noDefaultLabel()}

{=$form->input('term')->attrClass('wp100 biginfo')}

/*#if searchable.keywordTerms.text*/
<div class="row gut mt10">
	<div class="col w300">
/*#/if*/
		<div class="/*#if !searchable.keywordTerms.text*/col /*#/if*/block2">
			<div>Created : <? HTime::compact($term->created) ?></div>
			
			<?php $types=$typesDefault=SearchablesTypedTerm::typesList(); ?>
			
			<div>
				<?php if($term->type!==0) unset($typesDefault[0]); unset($typesDefault[SearchablesTypedTerm::ITSELF]) ?>
				{=$form->select('type',$typesDefault,$term->type)->label('Default type:')}
				{=$form->submit(true,array(),array('class'=>'submit center'))}
			</div>
			
			<div>
				Types: 
				{f $term->types as $type}
					{if isset($types[$type])}{$types[$type]}{else}{$type}{/if}, 
					<?php unset($types[$type]) ?>
				{/f}
				<?php if($term->type!==0) unset($types[0]); ?>
				{* {if!e $types}
					{=$form->select('type',$types,$term->type)->label('Add a type:')}
					{=$form->submit()->container()->addClass('center')}
				{/if} *}
			</div>
			
			{if SearchablesKeyword::existById($term->id)}<div class="mt6">{link 'Go to the keyword','/searchableKeyword/view/'.$term->id}</div>{/if}
		</div>
		
		<div class="mt10 block1">
			<h5 class="noclear">{t 'plugin.searchable.Abbreviations'}</h5>
			<? HHtml::ajaxCRDInputAutocomplete('/searchableTermAbbr',$term->abbreviations,
				array('js'=>'{allowNew:1,url:"/'.$term->id.'"}','modelFunctionName'=>'adminLink','escape'=>false,
						'inputAttributes'=>array('style'=>'width:240px'))) ?>
		</div>
		<div id="linkedKeywords" class="mt10 block1">
			<h5 class="noclear">{t 'plugin.searchable.LinkedKeywords'}</h5>
			<ul class="compact">
			{f $term->keywords as $keyword}
				<li>{=$keyword->adminLink()}</li>
			{/f}
			</ul>
		</div>

/*#if searchable.keywordTerms.text*/
	</div>
/*#/if*/


	<div class="col">
		/*#if searchable.keywordTerms.seo*/
		<? View::element('seo',array('model'=>$term,'form'=>$form)) ?>
		{=$form->submit()->container()->addClass('center')}
		/*#/if*/
	</div>

/*#if searchable.keywordTerms.text*/
</div>
<div class="mt10">
	<h4>Description du terme</h4>
	{=$form->textarea('text')->wp100()}
	{=$form->submit()->container()->addClass('center')}
</div>
/*#/if*/
{=$form->end(false)}

<?php HHtml::jsReady('/*#if searchable.keywordTerms.seo*/_.seo.init($(\'#SearchablesTermTerm\')/*,$(\'#linkedTerms ul\')*/);/*#/if*/'
	.'/*#if searchable.keywordTerms.text*/S.tinymce.init("100%","330px","basicAdvanced",!!_.cms).wordCount().autolink().autoSave().validXHTML()'
		/*#if searchable.keywordTerms.seo*/.'.addAttr("onchange_callback",_.seo.tinymceChanged_metaKeywords)'/*#/if*/
		.'.createForId("SearchablesTermText");/*#/if*/'
	.'$("#formTermEdit").ajaxForm(baseUrl+"searchableTerm/save/'.$term->id.'",false,function(){'
		//.'if($("#SearchablesKeywordDescr").val()==""){alert("Le texte est vide !");return false;}'
	.'});') ?>
<br class="clear"/>
