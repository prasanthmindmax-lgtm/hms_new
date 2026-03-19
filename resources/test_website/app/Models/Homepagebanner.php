<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class homepagebanner extends Sximo  {
	
	protected $table = 'tb_homepage_banner';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_homepage_banner.* FROM tb_homepage_banner  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_homepage_banner.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
