<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class careerform extends Sximo  {
	
	protected $table = 'tb_careerform';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_careerform.* FROM tb_careerform  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_careerform.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
