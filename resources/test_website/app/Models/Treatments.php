<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class treatments extends Sximo  {
	
	protected $table = 'tb_treatments';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_treatments.* FROM tb_treatments  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_treatments.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
