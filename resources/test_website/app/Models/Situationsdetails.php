<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class situationsdetails extends Sximo  {
	
	protected $table = 'tb_situations_details';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_situations_details.* FROM tb_situations_details  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_situations_details.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
