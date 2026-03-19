<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class treatmentcategory extends Sximo  {
	
	protected $table = 'tb_treatement_category';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_treatement_category.* FROM tb_treatement_category  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_treatement_category.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
