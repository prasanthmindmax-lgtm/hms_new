<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class awardspage extends Sximo  {
	
	protected $table = 'tb_awards';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_awards.* FROM tb_awards  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_awards.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
