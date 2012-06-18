<?php
class VPost extends SViewCachedElement{
	protected static $views=array('view','tags','metas');
	
	/* DEV */ public function exists(){ return false; } /* /DEV */
	
	public static function path($id){return DATA.'elementsCache/posts/'.$id;}
	
	public static function vars($id){
		return array('post'=>Post::QOne()->where(array('id'=>$id))
			/* IF(blog_ratings_enabled) */->with('Rating')/* /IF */
			->with('Post',Post::withOptions())
			->with('PostImage',array('fields'=>'image_id','onConditions'=>array('in_text'=>true)))
			->with('PostsTag','name,slug')
			/* IF(blog_comments_enabled) */->with('PostComment',array('where'=>array('status'=>PostComment::VALID)))/* /IF */
			/* IF(blog_personalizeAuthors_enabled) */->with('PostsAuthor','name,url')/* /IF */
		);
	}
	public function metas(){
		return json_decode($this->read('metas'),true);
	}
}