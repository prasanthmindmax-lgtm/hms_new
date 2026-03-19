<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class expertslistpage extends Sximo  {
	
	protected $table = 'tb_expertslist_page';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_expertslist_page.* FROM tb_expertslist_page  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_expertslist_page.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
