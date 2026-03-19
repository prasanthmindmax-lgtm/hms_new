<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class trainingpage extends Sximo  {
	
	protected $table = 'tb_training_page';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_training_page.* FROM tb_training_page  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_training_page.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
