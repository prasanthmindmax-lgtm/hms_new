<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class facilitiespage extends Sximo  {
	
	protected $table = 'tb_facilities_page';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_facilities_page.* FROM tb_facilities_page  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_facilities_page.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
