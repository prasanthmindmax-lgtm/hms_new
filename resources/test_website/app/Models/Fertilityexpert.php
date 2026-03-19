<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class fertilityexpert extends Sximo  {
	
	protected $table = 'tb_fertility_experts';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_fertility_experts.* FROM tb_fertility_experts  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_fertility_experts.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
