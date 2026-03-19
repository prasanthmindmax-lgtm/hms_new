<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class testimonialvideos extends Sximo  {
	
	protected $table = 'tb_testimonial_videos';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_testimonial_videos.* FROM tb_testimonial_videos  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_testimonial_videos.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
