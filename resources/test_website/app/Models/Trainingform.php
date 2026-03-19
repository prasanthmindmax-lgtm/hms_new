<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class trainingform extends Sximo  {
	
	protected $table = 'tb_trainingform';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_trainingform.* FROM tb_trainingform  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_trainingform.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
