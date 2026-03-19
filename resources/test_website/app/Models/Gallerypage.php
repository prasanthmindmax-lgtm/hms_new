<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class gallerypage extends Sximo  {
	
	protected $table = 'tb_gallery';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_gallery.* FROM tb_gallery  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_gallery.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
