<?php
class SearchableWordsCli{
	public static function doCli(){
		$db=SearchableWord::$__modelDb;
		$db->doUpdate('SET FOREIGN_KEY_CHECKS=0');
		SearchableTermWord::truncate();
		$db->doUpdate('SET FOREIGN_KEY_CHECKS=1');
		set_time_limit(0); ini_set('memory_limit', '1024M');
				
		foreach(SearchablesTerm::QRows()->fields('id,term') as $term){
			SearchableTermWord::add((int)$term['id'],$term['term']);
		}
	}
}
SearchableWordsCli::doCli();