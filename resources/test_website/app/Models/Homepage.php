<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class homepage extends Sximo  {
	
	protected $table = 'tb_homepage';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_homepage.* FROM tb_homepage  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_homepage.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
