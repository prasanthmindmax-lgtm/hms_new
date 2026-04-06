<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\TicketModel;
use App\Models\TicketDetails;
use App\Models\ImageModel;
use App\Models\StatusModel;
use App\Models\PriorityModel;
use App\Models\CategoryModel;
use App\Models\TblUserDepartments;
use App\Models\LocationModel;
use App\Models\SubCategoryModel;
use App\Models\TicketActivitiesModel;
use App\Models\TicketActivityModel;
use App\Models\TblLocationModel;
use App\Models\User;
use App\Models\HrmUsers;
use App\Models\UserProfile;
use App\Models\UserDesignations;
use App\Models\UserDepartments;
use App\Models\AdminUserDepartments;
use DB;
use Log;

class AdminController extends Controller
{   
    
    public function ticketAdded(Request $request)
    {
		//echo "<pre>";print_r($request->all());exit;
        $validatedData = $request->validate([
            'location' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'sub_department_id' => 'required|string|max:255',
                    'target_date' => 'required|string|max:255',
                    'subject' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'priority' => 'required|string|max:255',
					'images.*' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        $status = '1';
        $ticketDetail = TicketDetails::latest()->first();
        if($ticketDetail) {
            $ticketNo = $ticketDetail->ticket_no + 1;
        } else {
            $ticketNo = 1000;
        }
        
        $location_id =  TblLocationModel::select('id','zone_id')->where('name', $request->location)->first();
        $department_id =  CategoryModel::select('id')->where('depart_name', $request->department)->first();
        $from_department_id =  CategoryModel::select('id')->where('depart_name', $request->from_department)->first();
        $ticAdmin =  AdminUserDepartments::where('depart_id', $department_id->id)->get();
        //echo "<pre>";print_r(count($ticAdmin));exit;
        if(count($ticAdmin) == 0){
            return response()->json(['status'=>"error",'errors'=>'No ticket handler for this Department!']);  
        }
        $removeDept = TblUserDepartments::where('depart_id', $ticAdmin[0]->depart_id)->delete();
        foreach($ticAdmin as $user){
            TblUserDepartments::updateOrCreate([
                'admin_user_departments_id' => $user->id,
                'user_id' => $user->user_id,
                'depart_id' => $user->depart_id,
            ]);
        }

            $data = TicketDetails::create($request->only(['sub_department_id']));
            //echo "<pre>";print_r($data);exit;
            $ticketCreate = TicketDetails::updateOrCreate(['id'   => $data['id']],array_merge($validatedData, [
                'created_by' => auth()->user()->id,	
                'ticket_no'     => $ticketNo,	
                'ticket_status' => $status,	
                'is_read' => '1',	
                'department_id' => $department_id->id,	
                'from_department_id' => $from_department_id->id,
                'location_id' => $location_id->id,	
                'zone_id' => $location_id->zone_id,	
            ]));

            $ticketupdate = TicketActivityModel::updateOrCreate(
                ['id'   => '0000'],
                [
                    'ticket_id' => $data['id'],
                    'ticket_status' => $status,
                    'staff_id' => auth()->user()->id,
                    'priotity_level' => $request->priority,
                    'description' => $request->description,
                    'department_id' => $department_id->id,
                    'sub_department_id' => $request->sub_department_id,
                    'created_by' => auth()->user()->id
                ]
            );
        
            return response()->json(['status'=>"success", 'user_id'=>$ticketupdate['id'], 'ticketId' => $ticketCreate['id']]);       
    }

    public function fetchticketfitter(Request $request)
    {  
        $fitterremovedataall = $request->input('morefilltersall'); 
        $datefiltervalue = $request->input('moredatefittervale');             
        $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
        $dept = array();
        foreach($depart_id as $depart){
            $dept[] = $depart->depart_id;
        }

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                ->where('admin_user_departments.user_id', auth()->user()->id)
                ->whereIn('admin_user_departments.depart_id',$dept);
            $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);

        if($fitterremovedataall){ 
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);           
                    $value = trim($value, "'");
                    //echo "<pre>";print_r($value);
                    $query->whereIn(trim($column), explode(',', $value));            
            }							
        }
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function fetchmyticketfitter(Request $request)
    {  
        $fitterremovedataall = $request->input('morefilltersall'); 
        $datefiltervalue = $request->input('moredatefittervale');             
        
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                    ->where('tbl_ticket_details.created_by', auth()->user()->id);
            $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);

        if($fitterremovedataall){ 
            // Split conditions by 'AND' and loop through them
            foreach (explode(' AND ', $fitterremovedataall) as $condition) {
                [$column, $value] = explode('=', $condition);           
                    $value = trim($value, "'");
                    //echo "<pre>";print_r($value);
                    $query->whereIn(trim($column), explode(',', $value));            
            }							
        }
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function myTicketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale'); 
        $statusid = $request->input('statusid'); 
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                        ->where('tbl_ticket_details.created_by', auth()->user()->id);
                    if($statusid == 2){                 
                        $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                    } 
                    $ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
                          
        return response()->json($ticketdetails);
    }

    public function ticketFetch(Request $request)
    {
        $datefiltervalue = $request->input('moredatefittervale'); 
        $statusid = $request->input('statusid'); 
        $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
        $dept = array();
        foreach($depart_id as $depart){
            $dept[] = $depart->depart_id;
        }
       
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                        ->where('admin_user_departments.user_id', auth()->user()->id)
                        ->whereIn('admin_user_departments.depart_id',$dept);
       if($statusid == 2){                 
             $query->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
       } 
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
       // echo "<pre>";print_r($ticketdetails);exit;
        return response()->json($ticketdetails);
    }

    public function myticketFillter(Request $request)
    {   
        $datefiltervalue = $request->input('dateVal');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";

        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
            ->where('tbl_ticket_details.created_by', auth()->user()->id)
            ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates])
            ->groupBy('tbl_ticket_details.id');
							
		if ($request->statusValues) {
                $query->whereIn('ticket_status', explode(',', $request->statusValues));
            }
            
		if ($request->priorityValues) {
			$query->whereIn('priority', explode(',', $request->priorityValues));
		}
		
		if ($request->location_id) {
                $query->where('location_id', $request->location_id);
            }
			
		if ($request->dateType) {
                $startDate = $request->startDate;
                $endDate = $request->endDate;
                if ($request->dateType == 1) {
                    $query->whereDate('tbl_ticket_details.created_at', '>=', $startDate)->where('tbl_ticket_details.created_at', '<=', $endDate);
                } else if($request->dateType == 2) {
                    $query->whereDate('tbl_ticket_details.updated_at', '>=', $startDate)->where('tbl_ticket_details.updated_at', '<=', $endDate);
                } else if($request->dateType == 3) {
                    $query->whereDate('tbl_ticket_details.target_date', '>=', $startDate)->where('tbl_ticket_details.target_date', '<=', $endDate);
                }
            }
		$ticketdetails = $query->orderBy('tbl_ticket_details.created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function ticketFillter(Request $request)
    {                
        $datefiltervalue = $request->input('dateVal');

        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";
        $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
        $dept = array();
        foreach($depart_id as $depart){
            $dept[] = $depart->depart_id;
        }
        //echo "<pre>";print_r($dept);exit;
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                ->where('admin_user_departments.user_id', auth()->user()->id)
                ->whereIn('admin_user_departments.depart_id',$dept)
                ->groupBy('tbl_ticket_details.id');
							
		if ($request->statusValues) {
                $query->whereIn('ticket_status', explode(',', $request->statusValues));
            }
            
		if ($request->priorityValues) {
			$query->whereIn('priority', explode(',', $request->priorityValues));
		}
		
		if ($request->location_id) {
                $query->where('location_id', $request->location_id);
            }
			
		if ($request->dateType) {
                $startDate = $request->startDate;
                $endDate = $request->endDate;
                if ($request->dateType == 1) {
                    $query->whereDate('tbl_ticket_details.created_at', '>=', $startDate)->where('tbl_ticket_details.created_at', '<=', $endDate);
                } else if($request->dateType == 2) {
                    $query->whereDate('tbl_ticket_details.updated_at', '>=', $startDate)->where('tbl_ticket_details.updated_at', '<=', $endDate);
                } else if($request->dateType == 3) {
                    $query->whereDate('tbl_ticket_details.target_date', '>=', $startDate)->where('tbl_ticket_details.target_date', '<=', $endDate);
                }
            }
		$ticketdetails = $query->orderBy('tbl_ticket_details.created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function fetchticketfitterremove(Request $request)
    {  
        $fitterremovedataall = $request->input('fitterremovedataall');  
        $datefiltervalue = $request->input('moredatefittervale');
        $dates = explode(' - ', $datefiltervalue);
        $startDate = $dates[0];  // "29/12/2024"
        $endDate = $dates[1];    // "04/01/2025"
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        $startdates=$startDateFormatted." 00:00:00";
        $enddates=$endDateFormatted." 23:59:59";            
        $depart_id = TblUserDepartments::select('depart_id')->where('user_id', auth()->user()->id)->get();
        $dept = array();
        foreach($depart_id as $depart){
            $dept[] = $depart->depart_id;
        }
        //echo "<pre>";print_r($dept);exit;
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                ->where('admin_user_departments.user_id', auth()->user()->id)
                ->whereIn('admin_user_departments.depart_id',$dept)
                ->whereBetween('tbl_ticket_details.created_at', [$startdates, $enddates]);
                
                             // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            if($column == 'tbl_locations.name'){
                $value = preg_replace("/(\w)-(\w)/", "$1 - $2", $value);
                $query->where(trim($column), trim($value, " '"));
            }else{ 
                $value = trim($value, "'");
                $query->whereIn(trim($column), explode(',', $value));
            }
        }							
		
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

    public function fetchmyticketfitterremove(Request $request)
    {  
        $fitterremovedataall = $request->input('fitterremovedataall');              
       
        $query = TicketDetails::select(
                            'tbl_ticket_details.*',
                            'tbl_locations.name',
                            'department.depart_name as depart_name',            // or: as department_name
                            'from_department.depart_name as from_department_name',
                            'sub_category.sub_category_name',
                            'ticket_priority.priority_name',
                            'ticket_status_master.status_name',
                            'users.user_fullname',
                            'ticket_priority.priority_color'
                        )
                        ->join('tbl_locations', 'tbl_ticket_details.location_id', '=', 'tbl_locations.id')
                        ->join('tbl_user_departments as department', 'tbl_ticket_details.department_id', '=', 'department.id')
                        ->join('tbl_user_departments as from_department', 'tbl_ticket_details.from_department_id', '=', 'from_department.id')
                        ->join('sub_category', 'department.id', '=', 'sub_category.category_id') // double-check this relation
                        ->join('ticket_status_master', 'tbl_ticket_details.ticket_status', '=', 'ticket_status_master.id')
                        ->join('ticket_priority', 'tbl_ticket_details.priority', '=', 'ticket_priority.id')
                        ->join('admin_user_departments', 'tbl_ticket_details.department_id', '=', 'admin_user_departments.depart_id')
                        ->join('users', 'tbl_ticket_details.created_by', '=', 'users.id')
                        ->join('tblzones', 'tbl_ticket_details.zone_id', '=', 'tblzones.id')
                    ->where('tbl_ticket_details.created_by', auth()->user()->id);

                             // Split conditions by 'AND' and loop through them
        foreach (explode(' AND ', $fitterremovedataall) as $condition) {
            [$column, $value] = explode('=', $condition);
            if($column == 'tbl_locations.name'){
                $value = preg_replace("/(\w)-(\w)/", "$1 - $2", $value);
                $query->where(trim($column), trim($value, " '"));
            }else{ 
                $value = trim($value, "'");
                $query->whereIn(trim($column), explode(',', $value));
            }
        }							
		
		$ticketdetails = $query->groupBy('tbl_ticket_details.id')->orderBy('created_at', 'desc')->get();
        return response()->json($ticketdetails);
    }

}
