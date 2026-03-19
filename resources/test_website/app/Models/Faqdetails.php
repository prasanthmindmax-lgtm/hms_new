<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class faqdetails extends Sximo  {
	
	protected $table = 'tb_faq_details';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_faq_details.* FROM tb_faq_details  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_faq_details.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	

}
