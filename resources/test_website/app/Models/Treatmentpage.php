<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class treatmentpage extends Sximo  {
	
	protected $table = 'tb_treatmentpage';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_treatmentpage.* FROM tb_treatmentpage  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_treatmentpage.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
