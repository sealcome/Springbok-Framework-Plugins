<?php
/** @TableAlias('p') @DisplayField('title') */
class Post extends SSqlModel{
	const DRAFT=1,PUBLISHED=2,ARCHIVED=3,DELETED=4;
	
	public
		/** @Pk @AutoIncrement @SqlType('int(10) unsigned') @NotNull
		*/ $id,
		/** @SqlType('int(10) unsigned') @NotNull
		*  @ForeignKey('User','id')
		*/ $author_id,
		/** @SqlType('VARCHAR(128)') @NotNull
		*/ $title,
		/** @SqlType('text') @NotNull
		*/ $excerpt,
		/** @SqlType('text') @NotNull
		*/ $content,
		/** @SqlType('varchar(128)') @NotNull
		*/ $slug,
		/** @SqlType('varchar(128)') @NotNull
		*/ $meta_title,
		/** @SqlType('varchar(200)') @NotNull @Default('""')
		* @Text
		*/ $meta_descr,
		/** @SqlType('varchar(255)') @NotNull @Default('""')
		*/ $meta_keywords,
		/** @SqlType('tinyint(1)') @NotNull
		* @Enum(1=>'Draft',2=>'Published',3=>'Archived')
		*/ $status,
		/* IF(blog_comments_enabled) */
		/** @Boolean @Default(true)
		*/ $comments_allowed,
		/* /IF */
		/** @SqlType('datetime') @NotNull
		*/ $created,
		/** @SqlType('datetime') @Null @Default(NULL)
		* @NotBindable
		*/ $published,
		/** @SqlType('datetime') @Null @Default(NULL)
		*/ $updated;
	
	public static $hasOne=array(
		'Rating'=>array('modelName'=>'PostRating','fields'=>array('ROUND(AVG(prat.value))'=>'rating'),'fieldsInModel'=>true),
	);
	
	public static $hasMany=array(
		'PostPost'=>array('onConditions'=>array('deleted'=>false)),
		'LinkedPost'=>array('modelName'=>'PostPost','associationForeignKey'=>'linked_post_id'),
	);
	public static $hasManyThrough=array('Post'=>array('joins'=>array('PostPost'=>array('associationForeignKey'=>'linked_post_id'))));
	
	
	public static function findLatest(){
		return Post::QAll()->byStatus(Post::PUBLISHED)->fields('id,title')->orderByCreated()->limit(5);
	}

	public static function QListAll(){
		return/* */ Post::QAll()->fields('id,title,slug,excerpt,/* IF(blog_comments_enabled) */comments_allowed,/* /IF */created,published,updated')
			->with('PostImage','image_id')
			->with('PostsTag','id,name,slug')
			/* IF(blog_comments_enabled) */->with('PostComment',array('isCount'=>true,'onConditions'=>array('pcom.status'=>PostComment::VALID)))/* /IF */
			->byStatus(Post::PUBLISHED)
			->orderByCreated();
	}
	
	public function beforeInsert(){
		$this->slug=$this->auto_slug();
		$this->meta_title=$this->auto_meta_title();
		return parent::beforeInsert();
	}


	public function name(){ return $this->title; }
	public function link(){
		return array('/:controller/:id-:slug',_tR('post'),sprintf('%03d',$this->id),$this->slug);
	}
	
	public function auto_slug(){ return HString::slug($this->title); }
	public function auto_meta_title(){ return $this->title; }
	public function auto_meta_descr(){ return str_replace('&nbsp;',' ',html_entity_decode(strip_tags($this->excerpt),ENT_QUOTES,'UTF-8')); }
	public function auto_meta_keywords(){ return empty($this->tags)?'':implode(', ',PostsTag::QValues()->field('name')->byId($this->tags)->orderBy('name')); }
	
	public function isPublished(){return $this->status!==self::DRAFT;}
	
	public function toJSON_autocomplete(){
		return json_encode(array('id'=>$this->id,'value'=>$this->title,'url'=>HHtml::url($this->_link,Config::$site_url)));
	}
	public function toJSON_autocomplete_linkedposts(){
		return json_encode(array('id'=>$this->id,'value'=>$this->title,'pblsd'=>$this->isPublished()));
	}
	
	
	public function save(){
		if($this->status===self::PUBLISHED && !Post::existByIdAndStatus($id,self::PUBLISHED)) $this->published=array('NOW()');
		$res=$this->update();
		if($res && $this->status===self::PUBLISHED){
			ACSitemap::generatePosts();
			VPostsLatest::generate();
		}
		return $res;
	}
}