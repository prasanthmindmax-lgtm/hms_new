<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class faqpage extends Sximo  {
	
	protected $table = 'tb_faq_page';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_faq_page.* FROM tb_faq_page  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_faq_page.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
