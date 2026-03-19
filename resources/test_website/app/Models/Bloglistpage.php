<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class bloglistpage extends Sximo  {
	
	protected $table = 'tb_bloglist_page';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_bloglist_page.* FROM tb_bloglist_page  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_bloglist_page.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
