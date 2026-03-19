<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class locations extends Sximo  {
	
	protected $table = 'tb_locations';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_locations.* FROM tb_locations  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_locations.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
