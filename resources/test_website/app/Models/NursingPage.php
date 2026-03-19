<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class NursingPage extends Sximo
{
    protected $table = 'tb_nursing_page';
	protected $primaryKey = 'id';

	public function __construct() {
		parent::__construct();
		
	}

	public static function querySelect(  ){
		
		return "  SELECT tb_nursing_page.* FROM tb_nursing_page  ";
	}	

	public static function queryWhere(  ){
		
		return "  WHERE tb_nursing_page.id IS NOT NULL ";
	}
	
	public static function queryGroup(){
		return "  ";
	}
	
}
