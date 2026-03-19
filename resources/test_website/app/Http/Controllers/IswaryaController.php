<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Homepage;
use App\Models\Situation;
use App\Models\Situationsdetails;
use App\Models\Successstories;
use App\Models\Trainingpage;
use App\Models\Careerspage;
use App\Models\Faqdetails;
use App\Models\Faqpage;
use App\Models\Facilitiespage;
use App\Models\Awardspage;
use App\Models\Awardsgallery;
use App\Models\Gallerypage;
use App\Models\Aboutus;
use App\Models\Fertilityexpert;
use App\Models\Expertslistpage;
use App\Models\Treatments;
use App\Models\Treatmentpage;
use App\Models\Locations;
use App\Models\Locationdetailpage;
use App\Models\Post;
use App\Models\Bloglistpage;
use App\Models\Testimonialvideos;
use App\Models\Careerform;
use App\Models\Trainingform;
use App\Models\Pagemeta;
use App\Models\NursingPage;


use DB;

use Validator, Input, Redirect;



class IswaryaController extends Controller
{

	public function home(Request $request)
	{

		$this->data = array();

		$this->data['inner_header'] = 0;

		$this->data['pagename'] = 'home';

		$this->data['homepage'] = Homepage::first();

		$this->data['situations'] = Situation::get();

		$this->data['meta'] = Homepage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.home', $this->data);
	}
	public function home1(Request $request)
	{

		$this->data = array();

		$this->data['inner_header'] = 0;

		$this->data['pagename'] = 'home';

		$this->data['homepage'] = Homepage::first();

		$this->data['situations'] = Situation::get();

		$this->data['meta'] = Homepage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.home1', $this->data);
	}
	
	
	public function bestIvfCenter(Request $request)	{

		$this->data = array();

		$this->data['inner_header'] = 0;

		$this->data['pagename'] = 'home';

		$this->data['homepage'] = Homepage::first();

		$this->data['situations'] = Situation::get();

		$this->data['meta'] = Homepage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.landing_page', $this->data);
	}


	public function aboutus(Request $request)
	{

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'about-us';

		$this->data['aboutus'] = Aboutus::first();

		$this->data['meta'] = Aboutus::select('meta_title', 'meta_desc')->first();


		return view('iswarya.aboutus', $this->data);
	}

	public function jobs() {}


	public function treatment(Request $request)
	{

		$slug = $request->segment(2);

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = $slug;

		$this->data['treatment'] = Treatments::where('link', $slug)->first();

		$this->data['all_treatments'] = Treatments::where('side_bar_status', 1)->get();

		$this->data['treatment_page'] = Treatmentpage::first();

		// dd($this->data['treatment_page']);
		$this->data['meta'] = Treatments::select('meta_title', 'meta_desc')->where('link', $slug)->first();

		return view('iswarya.treatment', $this->data);
	}



	public function blogrecentold(Request $request)
	{



		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'blog';

		return view('iswarya.blog', $this->data);
	}

	public function blog(Request $request)
	{

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'blog';

		$this->data['blogpage_data'] = Bloglistpage::first();

		$this->data['blog_categories'] = \DB::table('tb_categories')->where('active', '1')->get();

		$this->data['meta'] = Bloglistpage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.blog', $this->data);
	}

	public function blog_detail(Request $request)
	{

		$this->data = array();

		$slug = $request->segment(2);

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = $slug;

		$this->data['blog_data'] = $blog_data = Post::where('pagetype', 'post')->where('alias', $slug)->where('status', 'enable')->first();

		$this->data['all_blogs'] = Post::where('pagetype', 'post')->where('cid', $blog_data->cid)->where('status', 'enable')->get();

		$meta = new \stdClass();
		$meta->meta_title = $blog_data->metakey;
		$meta->meta_desc = $blog_data->metadesc;

		$this->data['meta'] = $meta;

		return view('iswarya.blog-detail', $this->data);
	}



	public function blogPreview(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'blog-preview';

		return view('iswarya.blog-preview', $this->data);
	}

	public function internationalNav()
	{
		$this->data['inner_header'] = 1;
		return view('iswarya.international_nav', $this->data);
	}


	public function career(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['careers'] = Careerspage::first();

		$this->data['pagename'] = 'career';

		$this->data['meta'] = Careerspage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.career', $this->data);
	}

	public function career1(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['careers'] = Careerspage::first();

		$this->data['pagename'] = 'career';

		$this->data['meta'] = Careerspage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.career1', $this->data);
	}



	public function contactus(Request $request){
		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'contact-us';

		return view('iswarya.contact-us', $this->data);
	}



	public function doctorDetails(Request $request){



		$this->data = array();

		$slug = $request->segment(2);

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'doctor-details';

		$this->data['experts'] = Fertilityexpert::where('link', $slug)->first();

		$this->data['success_story'] = Successstories::get();

		return view('iswarya.doctor-details', $this->data);
	}

	public function doctorDetail(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'doctor-detail';

		return view('iswarya.doctor-detail', $this->data);
	}


	public function event(Request $request){



		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'event';

		return view('iswarya.event', $this->data);
	}



	public function eventDetails(Request $request){



		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'event-details';

		return view('iswarya.event-details', $this->data);
	}



	public function faq(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'faq';

		$this->data['faq_details'] = Faqdetails::get();

		$this->data['faq_page'] = Faqpage::first();

		return view('iswarya.faq', $this->data);
	}



	public function international(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'international';

		return view('iswarya.international', $this->data);
	}



	public function internationalDetails(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'international-details';

		return view('iswarya.international-details', $this->data);
	}



	public function internationalSrilanka(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'international-srilanka';

		return view('iswarya.international-srilanka', $this->data);
	}



	public function patientGuide(Request $request)
	{

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'patient-guide';

		return view('iswarya.patient-guide', $this->data);
	}



	public function location(Request $request)
	{

		// echo 'sdf'; exit;

		$slug = $request->segment(2);

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = $slug;

		$this->data['locations'] = Locations::where('link', $slug)->first();

		$this->data['meta'] = Locations::select('meta_title', 'meta_desc')->where('link', $slug)->first();

		return view('iswarya.location', $this->data);
	}



	public function locationDetails(Request $request)
	{
		$slug = $request->segment(2);

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = $slug;

		$this->data['locations'] = $locations = Locations::where('detailpage_link', $slug)->first();
		// dd($this->data['locations'] );

		$this->data['locationDetail'] = Locationdetailpage::where('location_id', $locations->id)->first();

		// dd($this->data['locationDetail'] );

		$this->data['meta'] = Locationdetailpage::select('meta_title', 'meta_desc')->where('location_id', $locations->id)->first();
		// dd($this->data['meta'] );

		return view('iswarya.location-details', $this->data);
	}



	public function payment(Request $request)
	{

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'payment';

		return view('iswarya.payment', $this->data);
	}



	public function testimonial(Request $request)
	{

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'testimonial';

		return view('iswarya.testimonial', $this->data);
	}

	public function training(Request $request)
	{

		$this->data = array();

		$this->data['training'] = Trainingpage::first();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'training';

		$this->data['meta'] = Trainingpage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.training', $this->data);
	}
	public function clinicalEmbryology()
	{
		$this->data = array();
		$this->data['inner_header'] = 1;
		return view('iswarya.clinical_embryology', $this->data);
	}
	public function traningEmbryology()
	{
		$this->data = array();
		$this->data['inner_header'] = 1;
		return view('iswarya.traning_embryology', $this->data);
	}
	public function traningAndrology()
	{
		$this->data = array();
		$this->data['inner_header'] = 1;
		return view('iswarya.traning_andrology', $this->data);
	}
	public function instituteOfParamedical()
	{
		$this->data = array();
		$this->data['inner_header'] = 1;
		return view('iswarya.institute_of_paramedical', $this->data);
	}

	public function fertilityAcademy(){
		
		$this->data = array();
		$this->data['inner_header'] = 1;
		return view('iswarya.fertility_academy', $this->data);

	}

	public function nursing(Request $request)
	{

		$this->data = array();

		$this->data['nursing'] = NursingPage::first();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'nursing';

		$this->data['meta'] = NursingPage::select('meta_title', 'meta_desc')->first();

		//print_r($this->data); exit;

		return view('iswarya.nursing', $this->data);
	}


	public function courseRegistration(Request $request)
	{

		$this->data = array();

		$this->data['training'] = Trainingpage::first();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'course register';

		$this->data['meta'] = Trainingpage::select('meta_title', 'meta_desc')->first();

		return view('iswarya.course-registration', $this->data);
	}

	public function saveCourseRegistration(Request $request){

		$validatedData = $request->validate([
			'name_of_course' => 'required',
			'name_of_applicant' => 'required',
			'gender' => 'required',
			'mobile' => 'required',
			'dob' => 'required|date',
			'age' => 'required|numeric',
			'fathername' => 'required',
			'mothername' => 'required',
			'present_address' => 'required',
			'permanent_address' => 'required',
			'education' => 'required',
			'institution' => 'required',
			'year_of_completion' => 'required',
			'photo' => 'required|image',
		]);


		// generate a unique filename for the photo
		$filename = time() . '_' . $validatedData['photo']->getClientOriginalName();

		// store the photo in the 'photos' folder inside the storage/app/public directory
		$validatedData['photo']->storeAs('public/photos', $filename);

		// insert the course registration record into the database using a DB query
		DB::connection('course_register')->table('tblcourse_registrations')->insert([
			'name_of_course' => $validatedData['name_of_course'],
			'name_of_applicant' => $validatedData['name_of_applicant'],
			'gender' => $validatedData['gender'],
			'dob' => $validatedData['dob'],
			'age' => $validatedData['age'],
			'mobile' => $validatedData['mobile'],
			'fathername' => $validatedData['fathername'],
			'mothername' => $validatedData['mothername'],
			'present_address' => $validatedData['present_address'],
			'permanent_address' => $validatedData['permanent_address'],
			'education' => $validatedData['education'],
			'institution' => $validatedData['institution'],
			'year_of_completion' => $validatedData['year_of_completion'],
			'photo' => $filename,
		]);


		/*    // set a success message in the session
    $request->session()->flash('success', 'Course registration saved successfully!');

    // redirect back to the registration form
    return redirect()->back(); */
		return view('iswarya.thankuregister');
	}

	public function courseRegistrationList(Request $request){

		$data = DB::connection('course_register')->table('tblcourse_registrations')->get();
		return view('iswarya.courseregistertable', compact('data'));
	}

	public function experts(Request $request){
	
		$this->data = array();
		
		$this->data['inner_header'] = 1;
	
		$this->data['pagename'] = 'fertility-experts';

		$this->data['expertslist'] = Expertslistpage::first();

		$this->data['meta'] = Expertslistpage::select('meta_title', 'meta_desc')->first();

		// dd($this->data);
	
		return view('iswarya.fertility-experts', $this->data);
	}


	















	
	public function gallery(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'gallery';

		$this->data['galleryimgs'] = Gallerypage::get();

		$this->data['meta'] = Pagemeta::where('pagename', 'gallerypage')->first();

		return view('iswarya.gallery', $this->data);
	}

	public function award(Request $request){

		$this->data = array();

		$this->data['inner_header'] = 1;

		$this->data['pagename'] = 'award-and-recognition';

		$this->data['awards_page'] = Awardspage::first();

		$this->data['awards_gallery'] = Awardsgallery::get();

		$this->data['meta'] = Awardspage::select('meta_title', 'meta_desc')->first();


		return view('iswarya.award-and-recognition', $this->data);
	}


	public function book_your_appointment(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'book-your-appointment';
		return view('iswarya.book-your-appointment', $this->data);
	}

	public function save_appointments(Request $request){
		// dd($request->all());
		$input['name'] = $request->Last_Name;
		$input['source'] = 4;
		$input['preferred_location'] = $request->LEADCF1;
		$input['status'] = 2;
		$input['phonenumber'] = $request->Phone;
		$input['preferred_time'] = $request->preferred_time;
		$input['treat_type'] = $request->treat_type;
		$input['dateadded'] = date('Y-m-d H:i:s');
		$input['title'] = 'Website';
		$input['urlvalue'] = 'https://www.draravindsivf.com/book-your-appointment';
		
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://www.draravindsivf.com/campaign/book-an-appointment/web_appointment.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $input,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: multipart/form-data",
			),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		//dd($response);
		curl_close($curl);
		
		return redirect('thankyou');
	}

	// newly added
	public function blog_1(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-1';
		return view('iswarya.blog-1', $this->data);
	}

	public function blog_2(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-2';
		return view('iswarya.blog-2', $this->data);
	}

	public function blog_3(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-3';
		return view('iswarya.blog-3', $this->data);
	}

	public function blog_4(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-4';
		return view('iswarya.blog-4', $this->data);
	}
	public function blog_5(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-5';
		return view('iswarya.blog-5', $this->data);
	}
	public function blog_6(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-6';
		return view('iswarya.blog-6', $this->data);
	}
	public function blog_7(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-7';
		return view('iswarya.blog-7', $this->data);
	}
	public function blog_8(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-8';
		return view('iswarya.blog-8', $this->data);
	}
	public function blog_9(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-9';
		return view('iswarya.blog-9', $this->data);
	}
	public function blog_10(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-10';
		return view('iswarya.blog-10', $this->data);
	}
	public function blog_11(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-11';
		return view('iswarya.blog-11', $this->data);
	}
	public function blog_12(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-12';
		return view('iswarya.blog-12', $this->data);
	}
	public function blog_13(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-13';
		return view('iswarya.blog-13', $this->data);
	}
	public function blog_14(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'blog-14';
		return view('iswarya.blog-14', $this->data);
	}

	public function operation(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'operation';
		return view('iswarya.operation', $this->data);
	}
	public function lab(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'lab';
		return view('iswarya.lab', $this->data);
	}

	public function radio(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'radio';
		return view('iswarya.radio', $this->data);
	}

	public function accommodation(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'accommodation';
		return view('iswarya.accommodation', $this->data);
	}

	public function pharmacy(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'pharmacy';
		return view('iswarya.pharmacy', $this->data);
	}

	public function ambulatory(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'ambulatory';
		return view('iswarya.ambulatory', $this->data);
	}
	public function bookonline(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'book-online';
		return view('iswarya.book-online', $this->data);
	}


	public function naturalplusoi(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'naturalplusoi';
		return view('iswarya.naturalplusoi', $this->data);
	}

	public function iui(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'iui';
		return view('iswarya.iui', $this->data);
	}
	public function ivf(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'ivf';
		return view('iswarya.ivf', $this->data);
	}

	public function icsi(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'icsi';
		return view('iswarya.icsi', $this->data);
	}

	public function imsi(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'imsi';
		return view('iswarya.imsi', $this->data);
	}

	public function pgs(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'pgs';
		return view('iswarya.pgs', $this->data);
	}

	public function pgd(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'pgd';
		return view('iswarya.pgd', $this->data);
	}

	public function surrogacy(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'surrogacy';
		return view('iswarya.surrogacy', $this->data);
	}

	public function azoospermia(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'azoospermia';
		return view('iswarya.azoospermia', $this->data);
	}

	public function eggdonor(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'eggdonor';
		return view('iswarya.eggdonor', $this->data);
	}

	public function andrology(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'andrology';
		return view('iswarya.andrology', $this->data);
	}


	public function locationCoimbatore(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-coimbatore';
		return view('iswarya.location-coimbatore', $this->data);
	}

	public function locationErode(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-erode';
		return view('iswarya.location-erode', $this->data);
	}

	public function locationTrippur(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-trippur';
		return view('iswarya.location-trippur', $this->data);
	}

	public function locationSalem(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-salem';
		return view('iswarya.location-salem', $this->data);
	}
	public function locationChennai(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-chennai';
		return view('iswarya.location-chennai', $this->data);
	}
	public function locationHosur(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-hosur';
		return view('iswarya.location-hosur', $this->data);
	}

	public function location_kerala_palakkad(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-kerala-palakkad';
		return view('iswarya.location-kerala-palakkad', $this->data);
	}

	public function location_kerala_kozhikode(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'location-kerala-kozhikode';
		return view('iswarya.location-kerala-kozhikode', $this->data);
	}

	public function coimbatoreDetail(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'coimbatore-detail';
		return view('iswarya.coimbatore-detail', $this->data);
	}

	public function erodeDetail(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'erode-detail';
		return view('iswarya.erode-detail', $this->data);
	}

	public function trippurDetail(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'trippur-detail';
		return view('iswarya.trippur-detail', $this->data);
	}

	public function salemDetail(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'salem-detail';
		return view('iswarya.salem-detail', $this->data);
	}

	public function kozhikodeDetail(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'kozhikode-detail';
		return view('iswarya.kozhikode-detail', $this->data);
	}

	public function palakkadDetail(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'palakkad-detail';
		return view('iswarya.palakkad-detail', $this->data);
	}

	public function sholinganallurDetail(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'sholinganallur-detail';
		return view('iswarya.sholinganallur-detail', $this->data);
	}


	public function urapakkamDetail(Request $request){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'urapakkam-detail';
		return view('iswarya.urapakkam-detail', $this->data);
	}

	public function hosurDetail(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'hosur-detail';
		return view('iswarya.hosur-detail', $this->data);
	}
	public function thankyou(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'thankyou';
		return view('iswarya.thankyou', $this->data);
	}

	public function situation_details(Request $request){
		$slug = $request->segment(2);
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = $slug;
		$this->data['situation'] = $situation = Situation::where('link', $slug)->first();
		$this->data['situations'] = $situations = Situation::get();
		$this->data['situation_details'] = $situation_details = Situationsdetails::where('situation_id', $situation->id)->get();

		// echo count($this->data['situation_details']);
		// echo '<pre>';
		// print_r($this->data['situations']);
		// echo '</pre>';

		return view('iswarya.situation-detail', $this->data);
	}

	public function facilities(Request $request){
		$segment_val = $request->segment(1);
		$slug = $request->segment(2);


		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = $slug;
		$this->data['segment_val'] = $segment_val;
		$this->data['all_facilities'] = $all_facilities = Facilitiespage::get();
		$this->data['facilities'] = $facilities = Facilitiespage::where('link', $slug)->first();

		$this->data['meta'] = Facilitiespage::select('meta_title', 'meta_desc')->where('link', $slug)->first();

		return view('iswarya.facilities', $this->data);
	}



	public function iwantababy(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'i-want-a-baby';
		return view('iswarya.i-want-a-baby', $this->data);
	}
	public function irregularmenses(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'irregular-menses';
		return view('iswarya.irregular-menses', $this->data);
	}
	public function pregnancyfailures(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'pregnancy-failures';
		return view('iswarya.pregnancy-failures', $this->data);
	}
	public function treatmentoptions(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'treatment-options';
		return view('iswarya.treatment-options', $this->data);
	}
	public function diagnosedwithpcos(Request $request){

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'diagnosed-with-pcos';
		return view('iswarya.diagnosed-with-pcos', $this->data);
	}
	// newly added


	public function get_video_modal(Request $request){

		$id = $request->id;
		$succ_stories = Successstories::where('id', $id)->first();
		$html = '';
		$html .= '<div class="modal-body">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>        
		<div class="embed-responsive embed-responsive-16by9">
		<iframe class="embed-responsive-item" src="' . $succ_stories->embedded_url . '" id="video"  allowscriptaccess="always" allow="autoplay"></iframe>
		</div> 
		</div>';
		return $html;
	}

	public function get_video_modal_testimonial(Request $request){

		$id = $request->id;
		$succ_stories = Testimonialvideos::where('id', $id)->first();
		$html = '';
		$html .= '<div class="modal-body">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>        
		<div class="embed-responsive embed-responsive-16by9">
		<iframe class="embed-responsive-item" src="' . $succ_stories->embedded_url . '" id="video"  allowscriptaccess="always" allow="autoplay"></iframe>
		</div> 
		</div>';
		return $html;
	}



	public function save_appointment(Request $request){

		$this->data = array();
		// echo "save appointment called";
		$ins_arr = array();
		$ins_arr = $request->all();
		// echo '<pre>';
		// print_r($ins_arr);
		// echo '</pre>';

		$curl = curl_init();



		curl_setopt_array($curl, array(

			CURLOPT_URL => "https://www.draravindsivf.com/campaign/book-an-appointment/save_appointment.php",

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => "",

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 30,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => "POST",

			CURLOPT_POSTFIELDS => $ins_arr,

			CURLOPT_HTTPHEADER => array(


				"cache-control: no-cache",

				"content-type: multipart/form-data",


			),


		));



		$response = curl_exec($curl);

		$err = curl_error($curl);


		curl_close($curl);



		if ($err) {

			echo "cURL Error #:" . $err;
		} else {

			//echo $response;

		}

		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['pagename'] = 'book-your-appointment';
		return view('iswarya.book-your-appointment', $this->data);
	}

	// newly added

	public function careerform_submit(Request $request){


		$data['name'] = $request->name;
		$data['email'] = $request->email;
		$data['mobile'] = $request->mobile;
		$data['location'] = $request->location;
		$data['years_of_practice'] = $request->years_of_practice;
		$data['speciality'] = $request->speciality;
		$data['message'] = $request->message;

		$insert_id = Careerform::insertGetId($data);

		return 1;
	}

	public function trainingform_submit(Request $request){


		$data['name'] = $request->name;
		$data['email'] = $request->email;
		$data['phone'] = $request->phone;
		$data['message'] = $request->message;

		$insert_id = Trainingform::insertGetId($data);

		return 1;
	}


	// Save Meta

	public function saveMeta(Request $request){


		$data['pagename'] = $request->pagename;
		$data['meta_title'] = $request->meta_title;
		$data['meta_desc'] = $request->meta_desc;

		if (!empty($request->metaid)) {
			$data['updated_at'] = date('Y-m-d H:i:s');
			Pagemeta::where('id', $request->metaid)->update($data);
		} else {
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			Pagemeta::insert($data);
		}

		return Redirect::back()->with('message', 'Data updated successfully');
	}

	public function patientTestimonial(){
		$this->data = array();
		$this->data['inner_header'] = 1;
		$this->data['testimonial'] = Testimonialvideos::get();

		// dd($this->data);

		return view('iswarya.patient_testimonial', $this->data);
	}
}
