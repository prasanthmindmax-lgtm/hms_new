<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class locationdetailpage extends Sximo  {
	
	protected $table = 'tb_locationdetailpage';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_locationdetailpage.* FROM tb_locationdetailpage  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_locationdetailpage.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
