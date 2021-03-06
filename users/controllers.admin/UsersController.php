<?php
Controller::$defaultLayout='admin/users';
/** @Check('ACSecureAdmin') @Acl('Users') */
class UsersController extends Controller{
	/** */
	static function index(){
		User::Table()->fields('id,email,first_name,last_name,gender/*#if users.pseudo*/,pseudo/*#/if*/,type,status,created,updated')
			->allowFilters()->paginate()->actionClick('view')->render('Utilisateurs');
	}
	
	/** @ValidParams @Id('id') */
	static function view(int $id){
		$user=User::ById($id)->mustFetch();
		mset($user);
		$paginate=$user->findWithPaginate('UserHistory',array('with'=>array('type'),'orderBy'=>array('created'=>'DESC')));
		$paginate->pageSize(25)->execute();
		render();
	}
	/** @ValidParams @Id('id') */
	static function disable(int $id){
		User::QUpdateOneField('status',User::DISABLED)->byIdAndStatus($id,User::VALID)->execute();
		UserHistory::add(UserHistory::DISABLE_USER,CSecure::connected(),$id);
		redirect('/users/view/'.$id);
	}
	
	
	/** */
	static function connections(){
		UserConnection::Table()->orderByCreated()
			->allowFilters()
			->paginate()->fields(array('created','type','succeed','login',
					'connected'=>array('align'=>'center','escape'=>false,
							'callback'=>function($v){return empty($v)?h($v):HHtml::link($v,'/users/view/'.$v);}),
					'ip'))
			->render('Connexions');
	}

	/** */
	static function sendValidMail(int $id){
		$user=User::ById($id)->fetch();
		if($user===false) redirect('/users');
		$uhe=UserHistoryEmail::findOneByUser_idAndStatusAndEmail($id,UserHistoryEmail::WAITING,$user->email);
		if($uhe===false) redirect('/users/view/'.$id);
		CMail::init('');
		CMail::send('user_validation_link',array('user'=>$user,'uhe'=>$uhe),'Validation du compte - '.Config::$projectName,$user->email);
		redirect('/users/view/'.$id);
	}
}
