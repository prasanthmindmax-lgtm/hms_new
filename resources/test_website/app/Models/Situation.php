<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class situation extends Sximo  {
	
	protected $table = 'tb_situations';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_situations.* FROM tb_situations  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_situations.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
