<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class careerspage extends Sximo  {
	
	protected $table = 'tb_careerspage';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_careerspage.* FROM tb_careerspage  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_careerspage.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
