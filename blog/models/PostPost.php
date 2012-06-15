<?php
/** @TableAlias('pp') */
class PostPost extends SSqlModel{
	public
		/** @Pk @SqlType('int(10) unsigned') @NotNull
		* @ForeignKey('Post','id','onDelete'=>'CASCADE')
		*/ $post_id,
		/** @Pk @SqlType('int(10) unsigned') @NotNull
		* @ForeignKey('Post','id','onDelete'=>'CASCADE')
		*/ $linked_post_id,
		/** @Boolean @Default(false)
		*/ $deleted,
		/** @Boolean @Default(false)
		*/ $manual;
	
	public static $belongsTo=array('Post'=>array('foreignKey'=>'linked_post_id'));
	
	public static function refind($postId){
		self::deleteAllByPost_idAndDeletedAndManual($postId,false,false);
		$tags=PostTag::QValues()->field('tag_id')->byPost_id($postId);
		if(!empty($tags))
			self::QInsertSelect()->ignore()->cols('post_id,linked_post_id')->query(
				Post::QAll()->fields($postId.',id')
					->with('PostTag',array('forceJoin'=>true,'fields'=>false))
					->where(array('id !='=>$postId,'pt.tag_id'=>$tags))
			);
		Post::onModified($id);
	}
	
	public static function add($postId,$linkedPostId){
		if(self::QInsert()->ignore()->set(array('post_id'=>$postId,'linked_post_id'=>$linkedPostId,'manual'=>true))){
			Post::onModified($id);
			return true;
		}
	}
}