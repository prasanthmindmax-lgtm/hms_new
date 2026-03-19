<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class aboutus extends Sximo  {
	
	protected $table = 'tb_aboutus';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_aboutus.* FROM tb_aboutus  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_aboutus.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
