<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|


Route::get('/', function () {
    return view('welcome');
});
*/

use App\Models\Situation;
use App\Models\Facilitiespage;
use App\Models\Fertilityexpert;
use App\Models\Treatments; 
use App\Models\Locations;
use App\Models\Locationdetailpage;
use App\Models\Post;

use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
    // Clear application cache
    Artisan::call('cache:clear');
    
    // Clear route cache
    Artisan::call('route:clear');
    
    // Clear config cache
    Artisan::call('config:clear');
    
    // Clear view cache
    Artisan::call('view:clear');
    
    // Optionally, you can add custom messages
    return 'Cache cleared successfully!';
});


//Default Controller
Route::get('/', 'IswaryaController@home');
Route::get('/home', 'IswaryaController@home1');

Route::get('/about-us', 'IswaryaController@aboutus');
Route::get('/jobs', function() {
    return redirect('https://app.draravindsivf.com/hrms/frontend/jobform');
    //  return redirect('https://draravinds.com/hrms/frontend/jobform');
});

Route::get('/feedback', function() {
    return redirect('https://draravinds.com/tasks/patientform');
});

Route::get('/best_ivf_center', 'IswaryaController@bestIvfCenter');


// Route::get('/treatment', 'IswaryaController@treatment');
Route::get('/patient_testimonial', 'IswaryaController@patientTestimonial');
Route::get('/blog', 'IswaryaController@blog'); 
Route::get('/blog-preview', 'IswaryaController@blogPreview');
Route::get('/career', 'IswaryaController@career');
Route::get('/international_nav', 'IswaryaController@internationalNav');
Route::get('/career1', 'IswaryaController@career1');
Route::get('/contact-us', 'IswaryaController@contactus'); //
// Route::get('/doctor-details', 'IswaryaController@doctorDetails');
Route::get('/doctor-detail', 'IswaryaController@doctorDetail');
Route::get('/event', 'IswaryaController@event');
Route::get('/event-details', 'IswaryaController@eventDetails');
Route::get('/faq', 'IswaryaController@faq'); 
Route::get('/international', 'IswaryaController@international'); 
Route::get('/international-details', 'IswaryaController@internationalDetails'); 
Route::get('/international-srilanka', 'IswaryaController@internationalSrilanka'); 
Route::get('/patient-guide', 'IswaryaController@patientGuide'); 
Route::get('/location', 'IswaryaController@location'); 
Route::get('/location-details', 'IswaryaController@locationDetails'); 
Route::get('/payment', 'IswaryaController@payment');
Route::get('/testimonial', 'IswaryaController@testimonial');
Route::get('/training', 'IswaryaController@training');
Route::get('/clinical_embryology_page', 'IswaryaController@clinicalEmbryology');
Route::get('/traning_embryology', 'IswaryaController@traningEmbryology');
Route::get('/traning_andrology', 'IswaryaController@traningAndrology');
Route::get('/institute_of_paramedical', 'IswaryaController@instituteOfParamedical');
Route::get('/fertilityacademy', 'IswaryaController@fertilityAcademy');

Route::get('/nursing', 'IswaryaController@nursing');
Route::get('/training/course-registration', 'IswaryaController@courseRegistration');
Route::get('/training/course-registration/list', 'IswaryaController@courseRegistrationList');
Route::post('/training/save-course-registration', 'IswaryaController@saveCourseRegistration')->name('saveCourseRegistration');
Route::get('/fertility-experts', 'IswaryaController@experts');

Route::get('/gallery', 'IswaryaController@gallery');
Route::get('/award-and-recognition', 'IswaryaController@award');

Route::get('/database_backup', 'HomepageController@databaseBackup');

Route::get('/book-your-appointment', 'IswaryaController@book_your_appointment');
Route::get('/thankyou', 'IswaryaController@thankyou');
Route::get('/book-online', 'IswaryaController@bookonline');
Route::post('/save_appointment', 'IswaryaController@save_appointments');


// description newly added
Route::get('/i-want-a-baby', 'IswaryaController@iwantababy');
Route::get('/irregular-menses', 'IswaryaController@irregularmenses');
Route::get('/pregnancy-failures', 'IswaryaController@pregnancyfailures');
Route::get('/treatment-options', 'IswaryaController@treatmentoptions');
Route::get('/diagnosed-with-pcos', 'IswaryaController@diagnosedwithpcos');


// facilities newly added
Route::get('/operation', 'IswaryaController@operation');
Route::get('/lab', 'IswaryaController@lab');
Route::get('/radio', 'IswaryaController@radio');
Route::get('/accommodation', 'IswaryaController@accommodation');
Route::get('/pharmacy', 'IswaryaController@pharmacy');
Route::get('/ambulatory', 'IswaryaController@ambulatory');


// newly added
Route::get('/blogs/blog-1', 'IswaryaController@blog_1');
Route::get('/blogs/blog-2', 'IswaryaController@blog_2');
Route::get('/blogs/blog-3', 'IswaryaController@blog_3');
Route::get('/blogs/blog-4', 'IswaryaController@blog_4');
Route::get('/blogs/blog-5', 'IswaryaController@blog_5');
Route::get('/blogs/blog-6', 'IswaryaController@blog_6');
Route::get('/blogs/blog-7', 'IswaryaController@blog_7');
Route::get('/blogs/blog-8', 'IswaryaController@blog_8');
Route::get('/blogs/blog-9', 'IswaryaController@blog_9');
Route::get('/blogs/blog-10', 'IswaryaController@blog_10');
Route::get('/blogs/blog-11', 'IswaryaController@blog_11');
Route::get('/blogs/blog-12', 'IswaryaController@blog_12');
Route::get('/blogs/blog-13', 'IswaryaController@blog_13');
Route::get('/blogs/blog-14', 'IswaryaController@blog_14');

Route::get('/naturalplusoi', 'IswaryaController@naturalplusoi');
Route::get('/iui', 'IswaryaController@iui');
Route::get('/ivf', 'IswaryaController@ivf');
Route::get('/icsi', 'IswaryaController@icsi');
Route::get('/imsi', 'IswaryaController@imsi');
Route::get('/pgs', 'IswaryaController@pgs');
Route::get('/pgd', 'IswaryaController@pgd');
Route::get('/surrogacy', 'IswaryaController@surrogacy');
Route::get('/azoospermia', 'IswaryaController@azoospermia');
Route::get('/eggdonor', 'IswaryaController@eggdonor');
Route::get('/andrology', 'IswaryaController@andrology');

Route::get('/coimbatore', 'IswaryaController@locationCoimbatore');
Route::get('/erode', 'IswaryaController@locationErode');
Route::get('/tiruppur', 'IswaryaController@locationTrippur');
Route::get('/salem', 'IswaryaController@locationSalem');
Route::get('/hosur', 'IswaryaController@locationHosur');
Route::get('/chennai', 'IswaryaController@locationChennai');
Route::get('/kerala-palakkad', 'IswaryaController@location_kerala_palakkad');
Route::get('/kerala-kozhikode', 'IswaryaController@location_kerala_kozhikode'); 

// Route::get('/coimbatore/ganapathy', 'IswaryaController@coimbatoreDetail');
// Route::get('/erode/thindal', 'IswaryaController@erodeDetail');
// Route::get('/tiruppur/pudur', 'IswaryaController@trippurDetail');
// Route::get('/salem/narasothipatti', 'IswaryaController@salemDetail');
// Route::get('/hosur/hosur-detail', 'IswaryaController@hosurDetail');
// Route::get('/kerala-kozhikode/kozhikode-detail', 'IswaryaController@kozhikodeDetail');
// Route::get('/kerala-palakkad/palakkad-detail', 'IswaryaController@palakkadDetail');
// Route::get('/chennai-sholinganallur/sholinganallur-detail', 'IswaryaController@sholinganallurDetail');
// Route::get('/chennai-urapakkam/urapakkam-detail', 'IswaryaController@urapakkamDetail');

Route::post('/save-appointment', 'IswaryaController@save_appointment');
Route::get('/get-video-modal', 'IswaryaController@get_video_modal');
Route::get('/get-video-modal-testimonial', 'IswaryaController@get_video_modal_testimonial');

Route::post('/careerform-submit', 'IswaryaController@careerform_submit');
Route::post('/trainingform-submit', 'IswaryaController@trainingform_submit');

// Route::get('/', 'HomeController@index');
Route::post('/home/submit', 'HomeController@submit');
Route::get('/home/skin/{any?}', 'HomeController@getSkin');


Route::get('dashboard/import', 'DashboardController@getImport');
/* Auth & Profile */
Route::get('user/profile','UserController@getProfile');
Route::get('user/theme','UserController@getTheme');
Route::get('user/login','UserController@getLogin');
Route::get('user/register','UserController@getRegister');
Route::get('user/logout','UserController@getLogout');
Route::get('user/reminder','UserController@getReminder');
Route::get('user/reset/{any?}','UserController@getReset');
Route::get('user/reminder','UserController@getReminder');
Route::get('user/activation','UserController@getActivation');
// Social Login
Route::get('user/socialize/{any?}','UserController@socialize');
Route::get('user/autosocialize/{any?}','UserController@autosocialize');
//
Route::post('user/signin','UserController@postSignin');
Route::post('user/login','UserController@postSigninMobile');
Route::post('user/signup','UserController@postSignupMobile');
Route::post('user/create','UserController@postCreate');
Route::post('user/saveprofile','UserController@postSaveprofile');
Route::post('user/savepassword','UserController@postSavepassword');
Route::post('user/doreset/{any?}','UserController@postDoreset');
Route::post('user/request','UserController@postRequest');

/* Posts & Blogs */
Route::get('posts','HomeController@posts');
Route::get('posts/category/{any}','HomeController@posts');
Route::get('posts/read/{any}','HomeController@read');
Route::post('posts/comment','HomeController@comment');
Route::get('posts/remove/{id?}/{id2?}/{id3?}','HomeController@remove');
// Start Routes for Notification 
Route::resource('notification','NotificationController');
Route::get('home/load','HomeController@getLoad');
Route::get('home/lang/{any}','HomeController@getLang');

Route::get('/set_theme/{any}', 'HomeController@set_theme');

include('pages.php');


Route::resource('sximoapi','SximoapiController');
Route::resource('services/posts', 'Services\PostController');



// Routes for  all generated Module
include('module.php');
// Custom routes  
$path = base_path().'/routes/custom/';
$lang = scandir($path);
foreach($lang as $value) {
	if($value === '.' || $value === '..') {continue;} 
	include( 'custom/'. $value );	
	
}
// End custom routes
Route::group(['middleware' => 'auth'], function () {
	Route::resource('dashboard','DashboardController');
});


Route::group(['namespace' => 'Sximo','middleware' => 'auth'], function () {
	// This is root for superadmin
	include('sximo.php');
});

Route::group(['namespace' => 'Core','middleware' => 'auth'], function () {
	include('core.php');
});

$situations = Situation::get();
// Route::get('/situation-details/test', 'IswaryaController@situation_details');
foreach($situations as $key=>$value){
    Route::get('/situation-details/'.$value->link, 'IswaryaController@situation_details');
    // echo $value->link.'<br>';
}
$facilities = Facilitiespage::get();
foreach($facilities as $key=>$value){

    Route::get('/facilities/'.$value->link, 'IswaryaController@facilities');
    // echo $value->link.'<br>';
}

$expertdtl = Fertilityexpert::get();
foreach($expertdtl as $key=>$value){
    Route::get('/doctor-details/'.$value->link, 'IswaryaController@doctorDetails');
}

$treatmentdtl = Treatments::get();
foreach($treatmentdtl as $key=>$value){
    Route::get('/treatment/'.$value->link, 'IswaryaController@treatment');
}

$locationpg = Locations::get();
foreach($locationpg as $key=>$value){
    Route::get('/location/'.$value->link, 'IswaryaController@location');
}

$locationspg = Locations::get();
foreach($locationspg as $key=>$value){
    // echo 'sdf0'; exit;
    Route::get('/'.$value->link.'/'.$value->detailpage_link, 'IswaryaController@locationDetails');
}

// 
$postsdata = Post::where('pagetype','post')->get();
foreach($postsdata as $key=>$value){
    Route::get('/blogs/'.$value->alias, 'IswaryaController@blog_detail');
}

Route::get('/blognew', 'IswaryaController@blognew');

Route::post('/meta-action/save','IswaryaController@saveMeta');

// Route::get('/treatment/iui', 'IswaryaController@treatment');
// Route::get('/doctor-details/reshma-shri', 'IswaryaController@doctorDetails');





