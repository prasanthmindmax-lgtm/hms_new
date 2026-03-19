<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class awardsgallery extends Sximo  {
	
	protected $table = 'tb_awards_gallery';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_awards_gallery.* FROM tb_awards_gallery  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_awards_gallery.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
