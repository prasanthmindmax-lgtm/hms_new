<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inbound extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
    }

//   public function get_locations_by_zone() {
//     $zone_id = $this->input->post('zone_id');
//     $locations = [];

//     if ($zone_id) {
//         $this->db->select('id, name');
//         $this->db->from('tblleads_locations');
//         $this->db->where('zone_id', $zone_id);
//         $this->db->order_by('name', 'asc');
//         $query = $this->db->get();
//         $locations = $query->result();
//     }

//     echo json_encode($locations);
// }


public function get_locations_by_zone() {
    $zone_id = $this->input->post('zone_id');
    $this->db->select('id, name');
    $this->db->from('tblleads_locations');

    if (!empty($zone_id)) {
        $this->db->where_in('zone_id', $zone_id); // OR use where_in() if it's multiple
    }

    $this->db->order_by('name', 'asc');
    $query = $this->db->get();

    echo json_encode($query->result());
}





    public function exotel()
    {
        close_setup_menu();
        $data['title']         = 'Exotel Data';
        $data['fromDate'] = $this->input->get('from_date');
        $data['toDate'] = $this->input->get('to_date');
        $data['status'] = $this->input->get('status');
        $data['direction'] = $this->input->get('direction');

        // $this->db->select('*');
        // $this->db->from('tblcall_records');
        // if (isset($data['fromDate']) && isset($data['toDate'])) {
        //     $this->db->where("DATE(DateCreated)>='" . $data['fromDate'] . "' AND DATE(DateUpdated) <='" . $data['toDate'] . "'");
        // }
        // if (isset($data['status']) && ($data['status'] != '')) {
        //     $this->db->where('Status', $data['status']);
        // }
        // if (isset($data['direction']) && ($data['direction'] != '')) {
        //     $this->db->where('Direction', $data['direction']);
        // }
        // $query = $this->db->get();
        // //echo $this->db->last_query();exit;
        // $data['records'] = $query->result_array();
        $this->load->view('admin/inbound/exotel', $data);
    }

    public function exotel_data()
{
    $get = $this->input->get();
    $draw = $get['draw'] ?? 1;
    $start = $get['start'] ?? 0;
    $rowperpage = $get['length'] ?? 10;
    $searchValue = $get['search']['value'] ?? '';

    $filters = [];
    if (!empty($searchValue)) {
        $filters[] = "(cr.Direction LIKE '%$searchValue%'
                    OR cr.From LIKE '%$searchValue%'
                    OR cr.To LIKE '%$searchValue%'
                    OR cr.staff_name LIKE '%$searchValue%'
                    OR cr.staff_location LIKE '%$searchValue%'
                    OR z.name LIKE '%$searchValue%'
                    OR cr.lead_status LIKE '%$searchValue%')";
    }

    // Date Range
    if (!empty($get['from_date']) && !empty($get['to_date'])) {
        $filters[] = "DATE(cr.DateCreated) >= '{$get['from_date']}' AND DATE(cr.DateUpdated) <= '{$get['to_date']}'";
    }

    // Quick date filter
    if (!empty($get['date_filter'])) {
        $today = date('Y-m-d');
        switch ($get['date_filter']) {
            case 'today': $from = $to = $today; break;
            case '1week': $from = date('Y-m-d', strtotime('-7 days')); $to = $today; break;
            case '1month': $from = date('Y-m-d', strtotime('-1 month')); $to = $today; break;
            case '2month': $from = date('Y-m-d', strtotime('-2 months')); $to = $today; break;
            case '3month': $from = date('Y-m-d', strtotime('-3 months')); $to = $today; break;
            case '6month': $from = date('Y-m-d', strtotime('-6 months')); $to = $today; break;
            case '1year': $from = date('Y-m-d', strtotime('-1 year')); $to = $today; break;
            default: $from = $to = null;
        }
        if ($from && $to) {
            $filters[] = "DATE(cr.DateCreated) >= '{$from}' AND DATE(cr.DateUpdated) <= '{$to}'";
        }
    }

    // Other filters
    if (!empty($get['status'])) $filters[] = "cr.Status = '{$get['status']}'";
    if (!empty($get['direction'])) $filters[] = "cr.Direction = '{$get['direction']}'";
    if (!empty($get['staff_name'])) $filters[] = "cr.staff_id = '{$get['staff_name']}'";
    if (!empty($get['staff_location'])) $filters[] = "cr.location = '{$get['staff_location']}'";
    if (!empty($get['staff_zone'])) $filters[] = "z.id = '{$get['staff_zone']}'";
    if (!empty($get['lead_status'])) $filters[] = "cr.lead_status = '{$get['lead_status']}'";

    if (!empty($get['no_lead']) && $get['no_lead'] == 1) $filters[] = "cr.lead_id = 0";
    if (!empty($get['no_staff_name']) && $get['no_staff_name'] == 2) $filters[] = "cr.staff_id = 0";
    if (!empty($get['no_location']) && $get['no_location'] == 3) $filters[] = "cr.location = 0";
    if (!empty($get['no_lead_status']) && $get['no_lead_status'] == 4) $filters[] = "cr.lead_status = 0";
    if (!empty($get['no_rating']) && $get['no_rating'] == 5) $filters[] = "cr.rating = 0";

    $ratings = [];
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($get['rating' . $i])) {
            $ratings[] = intval($get['rating' . $i]);
        }
    }
    if (!empty($ratings)) {
        $ratings = implode(',', $ratings);
        $filters[] = "cr.rating IN ($ratings)";
    }

    $whereSQL = '';
    if (!empty($filters)) {
        $whereSQL = implode(' AND ', $filters);
    }

    $this->db->select('count(*) as allcount');
    $this->db->from('tblcall_records cr');
    $this->db->join('tblcall_record rc', 'rc.lead_id = cr.lead_id', 'left');
    $this->db->join('tblleads_locations ll', 'll.id = cr.location', 'left');
    $this->db->join('tblzones z', 'z.id = ll.zone_id', 'left');
    $totalRecords = $this->db->count_all_results();

    $this->db->select('count(*) as allcount');
    $this->db->from('tblcall_records cr');
    $this->db->join('tblcall_record rc', 'rc.lead_id = cr.lead_id', 'left');
    $this->db->join('tblleads_locations ll', 'll.id = cr.location', 'left');
    $this->db->join('tblzones z', 'z.id = ll.zone_id', 'left');
    if ($whereSQL) {
        $this->db->where($whereSQL, null, false); // Raw SQL where
    }
    $filteredCountResult = $this->db->get()->row();
    $totalRecordwithFilter = $filteredCountResult->allcount;

    // Fetch paginated data
    $this->db->select('cr.*, rc.file_url');
    $this->db->from('tblcall_records cr');
    $this->db->join('tblcall_record rc', 'rc.lead_id = cr.lead_id', 'left');
    $this->db->join('tblleads_locations ll', 'll.id = cr.location', 'left');
    $this->db->join('tblzones z', 'z.id = ll.zone_id', 'left');
    if ($whereSQL) {
        $this->db->where($whereSQL, null, false);
    }
    $this->db->order_by('cr.id', 'DESC');
    $this->db->limit($rowperpage, $start);
    $result = $this->db->get()->result_array();

    // Format rows (your existing logic stays same)
    $data = [];
   $data = [];

foreach ($result as $call) {
    // Staff name
     $StaffName = '';
            if ($call['staff_id'] != 0) {
                $query = $this->db->where('staffid', $call['staff_id'])->get(db_prefix() . 'staff')->row_array();
                $full_name = preg_replace('/=\\\\/m', "=''", $query['firstname'] . ' ' . $query['lastname']);

                $StaffName = '<a data-toggle="tooltip" title="' . $full_name . '" href="' . admin_url('profile/' . $call['staff_id']) . '">' . staff_profile_image($call['staff_id'], [
                    'staff-profile-image-small',
                ]) . '</a>';

                // For exporting
                $StaffName .= '<span class="hide">' . $full_name . '</span>';
            }

                 $location_name = '';
                $zone_name = '';

                if ($call['location'] != 0) {
                    $query_lead = $this->db
                        ->select('ll.name as location_name, z.name as zone_name')
                        ->from(db_prefix() . 'leads_locations ll')
                        ->join('tblzones z', 'z.id = ll.zone_id', 'left')
                        ->where('ll.id', $call['location'])
                        ->get()
                        ->row_array();

                    if ($query_lead) {
                        $location_name = preg_replace('/=\\\\/m', "=''", $query_lead['location_name']);
                        $zone_name = preg_replace('/=\\\\/m', "=''", $query_lead['zone_name']);
                    }
                }
                $location = "<span>{$location_name}<br><span style='font-size:10px;'>{$zone_name}</span></span>";

          	   if ($call['lead_status'] != 0) {

                    $lead_status = $this->db
                    ->where('id', $call['lead_status'])
                    ->get(db_prefix() . 'leads_status')
                    ->row_array();
                                     // Fix the column reference and apply preg_replace
                    $lead_status = preg_replace('/=\\\\/m', "=''", $lead_status['name']);
            }else{
                $lead_status="";

            }
$file_link = '-';
if (!empty($call['file_url']) && filter_var($call['file_url'], FILTER_VALIDATE_URL)) {
    $file_link = '<audio controls style="width:180px;">
                    <source src="' . htmlspecialchars($call['file_url']) . '" type="audio/mpeg">
                    Your browser does not support the audio element.
                 </audio>';
}

            $wife_mrd=$call['wife_mrd'];
            $walk_in_date=$call['walk_in_date'];

            $color = 'black';
            if ($call['Status'] == 'completed') {
                $color = 'green';
            } else if ($call['Status'] == 'failed') {
                $color = 'red';
            } else if ($call['Status'] == 'busy') {
                $color = 'blue';
            } else if ($call['Status'] == 'no-answer') {
                $color = 'brown';
            }
            $direction_name = $call['Direction'];
             if ($call['Direction'] === 'inbound') {
                $direction_name = '<img src="/crm/uploads/directions/inbound.png" alt="Inbound" />';
            }
             else if ($call['Direction'] == 'outbound-dial') {
                $direction_name = 'Outbound from Exotel';
                $direction_name = '<img src="/crm/uploads/directions/outbound_from_exotel.png" alt="outbound from exotel" />';
            } else if ($call['Direction'] == 'outbound-api') {
                $direction_name = '<img src="/crm/uploads/directions/outbound_api.png" alt="outbound api" />';
            }
          $rating = $call['rating'];

            $Lead = '';
            if ($call['lead_id'] != 0) {
                $lead_data = $this->db->where('id', $call['lead_id'])->get(db_prefix() . 'leads')->row_array();
                $hrefAttr = 'href="' . admin_url('leads/index/' . $lead_data['id']) . '" onclick="init_lead(' . $lead_data['id'] . ');return false;"';
                $Lead = '<a data-toggle="tooltip" data-title="' . $lead_data['lead_description'] . '" ' . $hrefAttr . '>' . preg_replace('/=\\\\/m', "=''", $lead_data['id']) . '</a>';
            }
            $call_status = '<b style="color:' . $color . '">' . ucfirst($call['Status']) . '</b>';

            if (!empty($call['RecordingUrl']) && filter_var($call['RecordingUrl'], FILTER_VALIDATE_URL)) {
        $duration = '<audio controls style="width:180px;">
        <source src="https://app.draravindsivf.com/crm/api_exotel_issue.php?url=' . urlencode($call['RecordingUrl']) . '" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>';
} else {
    $duration = "-";
}
// if (!empty($call['RecordingUrl'])) {
//                 $duration = ' <br /><a target="_blank" href="' . $call['RecordingUrl'] . '">' . $call['Duration'] . '</a>';
//                // $duration = ' <br /><a target="_blank" href="https://app.draravindsivf.com/crm/api_exotel_issue.php?url=' . $call['RecordingUrl'] . '">' . $call['Duration'] . '</a>';
//             } else {
//                 $duration = "";
//             }

          	 $sid = '<br />
<p class="playaudio" data-audioUrl="sid: ' . htmlspecialchars($call['Sid']) . '<br /> From NO: ' . htmlspecialchars($call['From']) . '<br /> To NO: ' . htmlspecialchars($call['To']) . '">
    <i style="color:blue" class="fa fa-eye"></i>
</p>';

         /* if ($call['rating'] != 0) {
    $des_rating = '<br />
    <p class="descrip" data-desUrl="' . htmlspecialchars($call['description']) . '" style="color:blue;">
        ' . $rating . '
    </p>';

    }else{
        $des_rating=$rating;
    }*/
      $feedback_staff_name = $this->db->where('staffid', $call['rating_staff_id'])->get(db_prefix() . 'staff')->row_array();

                    $full_name = preg_replace('/=\\\\/m', "=''", $feedback_staff_name['firstname'] . ' ' . $feedback_staff_name['lastname']);

                        $full_name = preg_replace('/=\\\\/m', "=''", $feedback_staff_name['firstname'] . ' ' . $feedback_staff_name['lastname']);

                        $rating = $call['rating'];

                    $des_rating = '<br />
                    <p class="playaudio" data-audioUrl="Description: ' . htmlspecialchars($call['description']) . '<br /> Staff Name: ' . htmlspecialchars($full_name) .'" style="color:blue">
                         ' . $rating . '
                    </p>';
           $feedback = '<br />
    <p class="feedback"
       data-Url="' . htmlspecialchars($call['id']) . '"
       rating="' . htmlspecialchars($call['rating']) . '" des="' . htmlspecialchars($call['description']) . '">
        <i style="color:blue" class="fa fa-edit"></i>
    </p>';
          	$phone_from=substr($call['From'], 0, 2) . '******' . substr($call['From'], -2);
            $phone_to=substr($call['To'], 0, 2) . '******' . substr($call['To'], -2);
          	 if (empty($Lead)) {
                    $call['id'] = '<a href="#" onclick="init_lead(); return false;" class="btn btn-primary mright5 pull-left display-block">'
                                . $call['id'] . '</a>';
                }
    // Build final row
    $data[] = [
        $call['id'],
        $Lead ?: '-',
            $direction_name,
            // $call['From'],
            // $call['To'],
            $phone_from,
            $phone_to,
            $call_status,
            $location,
            $lead_status,
            $file_link,
            $StaffName,
            $des_rating,
            date('d-m-y H:i:s', strtotime($call['StartTime'])),
            date('d-m-y H:i:s', strtotime($call['EndTime'])),
            $duration,
            $sid,
            // $feedback,
            $wife_mrd,
            $walk_in_date
    ];
}
echo json_encode([
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
]);
}

    // Add this method to your Inbound controller
    public function smartflo_feedback_data()
{
    $rating       = $this->input->get('rating');
    $description  = $this->input->get('description');
    $flag         = $this->input->get('flag');
    $feedback_id  = $this->input->get('feedback_id');

    $response = array();

    if ($flag == 1 && $feedback_id) {
        $staff_id = get_staff_user_id();

        $data = [
            'smart_rating'          => $rating,
            'smart_description'     => $description,
            'smart_rating_staff_id' => $staff_id,
        ];

        $this->db->where('smart_id', $feedback_id);
        $this->db->update('tblsmartcall_records', $data);

        if ($this->db->affected_rows() > 0 || $this->db->where('smart_id', $feedback_id)->get('tblsmartcall_records')->num_rows() > 0) {
            // Fetch the updated row with proper joins
            // Replace these JOINs with your actual table relationships
            $this->db->select('tblsmartcall_records.*');
            $this->db->from('tblsmartcall_records');

            // Add your actual JOIN statements here - examples:
            // $this->db->join('tbllocations', 'tbllocations.id = tblsmartcall_records.location_id', 'left');
            // $this->db->join('tblzones', 'tblzones.id = tblsmartcall_records.zone_id', 'left');
            // $this->db->join('tblleads', 'tblleads.id = tblsmartcall_records.lead_id', 'left');
            // $this->db->join('tblstaff', 'tblstaff.staffid = tblsmartcall_records.staff_id', 'left');

            $this->db->where('smart_id', $feedback_id);
            $updatedRow = $this->db->get()->row_array();

            $response['status'] = 'success';
            $response['message'] = 'Feedback updated successfully';
            $response['data'] = $updatedRow;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Record not found';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid request parameters';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

 	public function feedback_data()
    {
        extract($_GET);

        close_setup_menu();
        $data['title']         = 'Exotel Data';
        $flag = $this->input->get('flag');
        $feedback_id = $this->input->get('feedback_id');
         // Pass feedback_id to the view

        if($flag==1)
        $staff_id= get_staff_user_id();
            $data = array(
                'rating' => $rating,
                'description' => $description, // Replace with your actual value
                'rating_staff_id' => $staff_id // Replace with your actual value
            );
            //print_r($data );exit;

            $this->db->where('id', $feedback_id);
            $this->db->update('tblcall_records', $data);
            //echo $this->db->last_query(); exit;
            $data['feedback_id'] =$feedback_id;
            //echo $feedback_id;exit;
        $this->load->view('admin/inbound/exotel', $data);
        }

    public function knowlarity()
    {
        close_setup_menu();
        $date = date('Y-m-d');

        $headers = array('Content-Type: application/json', 'Authorization: 0b5f527a-e2b1-4351-80c4-c14bf6273040', 'x-api-key:1t3ZvFuzUD8kjxGvVfhtx2VWwEmp3EyC7hxHEkT8');
        $url = "https://kpi.knowlarity.com/Basic/v1/account/calllog?start_time=2023-04-01%2000%3A00%3A00%2B05%3A30&end_time=$date%2023%3A59%3A59%2B05%3A30&call_type=0&limit=200";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        $data['responses']      = json_decode($result, true);
        $data['title']          = 'Knowlarity Inbound Data';

        $this->load->view('admin/inbound/knowlarity', $data);
    }

function mask_number($number) {
    $clean = preg_replace('/\D/', '', $number); // remove non-digits
    if (strlen($clean) >= 4) {
        return substr($clean, 0, 2) . str_repeat('*', 6) . substr($clean, -2);
    }
    return $number; // if too short, return as-is
}

public function smartflo_all_data()
{
    $get = $this->input->get();
    $filters = [];

    if (!empty($get['from_date']) && !empty($get['to_date'])) {
        $from = $get['from_date'];
        $to   = $get['to_date'];
    } elseif (!empty($get['date_filter'])) {
        $today = date('Y-m-d');
        switch($get['date_filter']){
            case 'today':
                $from = $to = $today;
                break;
            case '1week':
                $from = date('Y-m-d', strtotime('-7 days'));
                $to   = $today;
                break;
            case '1month':
                $from = date('Y-m-d', strtotime('-1 month'));
                $to   = $today;
                break;
            default:
                $from = $to = $today;
        }
    } else {
        $from = $to = date('Y-m-d');
    }
    $filters[] = "DATE(cr.smart_StartTime) >= '{$from}' AND DATE(cr.smart_EndTime) <= '{$to}'";
    if (!empty($get['status'])) $filters[] = "cr.smart_Status = '{$get['status']}'";
    if (!empty($get['direction'])) $filters[] = "cr.smart_Direction = '{$get['direction']}'";
    if (!empty($get['staff_name'])) $filters[] = "st.staffid = '{$get['staff_name']}'";
    if (!empty($get['staff_zone'])) $filters[] = "z.id = '{$get['staff_zone']}'";
    if (!empty($get['staff_location'])) $filters[] = "ll.id = '{$get['staff_location']}'";
    if (!empty($get['lead_status'])) $filters[] = "ls.id = '{$get['lead_status']}'";
    if (!empty($get['no_lead'])) $filters[] = "cr.smart_lead_id = 0";
    if (!empty($get['no_staff_name'])) $filters[] = "l.assigned = 0";
    if (!empty($get['no_location'])) $filters[] = "l.preferred_location = 0";
    if (!empty($get['no_lead_status'])) $filters[] = "l.status = 0";
    if (!empty($get['no_rating'])) $filters[] = "cr.smart_rating = 0";
    $ratings = [];
    for($i=1;$i<=5;$i++){
        if(!empty($get['rating'.$i])) $ratings[] = intval($get['rating'.$i]);
    }
    if(!empty($ratings)) $filters[] = "cr.smart_rating IN (".implode(',', $ratings).")";
    $whereSQL = !empty($filters) ? implode(' AND ', $filters) : '';
    $this->db->select('cr.smart_id, cr.smart_From, cr.smart_To, cr.smart_Status, cr.smart_Direction,
                       cr.smart_StartTime, cr.smart_EndTime, cr.smart_Duration, cr.smart_rating,
                       l.id as lead_id, l.MDR as wife_mrd, l.walk_in_date,
                       ll.name as location_name, z.name as zone_name,
                       CONCAT(st.firstname," ",st.lastname) as staff_name, st.staffid as staff_id,
                       ls.name as lead_status_name, cr.smart_RecordingUrl, cr.smart_description, cr.smart_rating_staff_id');
    $this->db->from('tblsmartcall_records cr');
    $this->db->join('tblleads l', 'RIGHT(l.phonenumber, 10) = RIGHT(cr.smart_To, 10)', 'left');
    $this->db->join('tblleads_locations ll', 'll.id = l.preferred_location', 'left');
    $this->db->join('tblzones z', 'z.id = ll.zone_id', 'left');
    $this->db->join('tblstaff st', 'st.staffid = l.assigned', 'left');
    $this->db->join('tblleads_status ls', 'ls.id = l.status', 'left');
    if($whereSQL) $this->db->where($whereSQL, null, false);
    $result = $this->db->get()->result_array();
    $staff_audit = $this->db->where('lastname', 'Audit')->get(db_prefix().'staff')->row_array();

    foreach ($result as &$row) {
        $row['staff_image'] = '';
        if (!empty($row['staff_id'])) {
            $staff = $this->db->where('staffid', $row['staff_id'])->get(db_prefix().'staff')->row_array();
            if ($staff) {
                $full_name = $staff['firstname'].' '.$staff['lastname'];
                $row['staff_image']  = '<a data-toggle="tooltip" title="'.$full_name.'" href="'.admin_url('profile/'.$row['staff_id']).'">'
                                     . staff_profile_image($row['staff_id'], ['staff-profile-image-small']).'</a>';
                $row['staff_image'] .= '<span class="hide">'.$full_name.'</span>';
            }
        }
        $row['audit_image'] = '';
        if ($staff_audit) {
            $full_name_Audit = $staff_audit['firstname'].' '.$staff_audit['lastname'];
            $row['audit_image']  = '<a data-toggle="tooltip" title="'.$full_name_Audit.'" href="'.admin_url('profile/'.$staff_audit['staffid']).'">'
                                 . staff_profile_image($staff_audit['staffid'], ['staff-profile-image-small']).'</a>';
            $row['audit_image'] .= '<span class="hide">'.$full_name_Audit.'</span>';
        }
    }
    echo json_encode(['data'=>$result]);
}


public function smartflo(){
        $data['smart_fromDate'] = $this->input->get('from_date');
        $data['smart_toDate'] = $this->input->get('to_date');
        $data['smart_status'] = $this->input->get('status');
        $data['smart_direction'] = $this->input->get('direction');
       $this->load->view('admin/inbound/smartflo', $data);
}



public function mastercall()
{
    $this->load->view('admin/inbound/master_call_data');
}
public function mastercall_data() {
   $from_date = $this->input->get('from_date');    $to_date   = $this->input->get('to_date');
    $direction_filter = $this->input->get('call_data_type'); // Smartflo, Exotel, Manual    $status        = $this->input->get('status');
    $direction     = $this->input->get('direction');    $staff_name    = $this->input->get('staff_name');
    $staff_zone    = $this->input->get('staff_zone');    $staff_location= $this->input->get('staff_location');
    $lead_status   = $this->input->get('lead_status');    $no_lead       = $this->input->get('no_lead');
    $no_staff_name = $this->input->get('no_staff_name');    $no_location   = $this->input->get('no_location');
    $no_lead_status= $this->input->get('no_lead_status');    $no_rating     = $this->input->get('no_rating');
    $search        = $this->input->get('search')['value'] ?? '';
     if (empty($from_date) || empty($to_date)) {
        $from_date = date("Y-m-d");        $to_date   = date("Y-m-d");
    }
    $query = "
    SELECT * FROM (        -- Smartflo Call Data
        SELECT cr.smart_id AS record_id, l.id AS lead_id, cr.smart_Direction AS Direction,               cr.smart_From AS call_from, cr.smart_To AS call_to, cr.smart_Status AS Status,
               cr.smart_StartTime AS StartTime, cr.smart_EndTime AS EndTime, cr.smart_Duration AS Duration,               'Smartflo Call Data' AS source_table, cr.smart_RecordingUrl AS RecordingUrl,
               l.MDR AS wife_mrd, l.walk_in_date,                ll.name AS location_name,
               z.id AS zone_id,               CONCAT(st.firstname,' ',st.lastname) AS staff_name,
               CONCAT(ast.firstname,' ',ast.lastname) AS audit_staff,               ls.name AS lead_status_name
        FROM tblsmartcall_records cr        LEFT JOIN tblleads l ON RIGHT(l.phonenumber, 10) = RIGHT(cr.smart_To, 10)
        LEFT JOIN tblleads_locations ll ON ll.id = l.preferred_location        LEFT JOIN tblzones z ON z.id = ll.zone_id
        LEFT JOIN tblstaff st ON st.staffid = l.assigned        LEFT JOIN tblstaff ast ON ast.staffid = cr.smart_rating_staff_id
        LEFT JOIN tblleads_status ls ON ls.id = l.status        WHERE DATE(cr.smart_StartTime) BETWEEN ".$this->db->escape($from_date)." AND ".$this->db->escape($to_date)."
        UNION ALL
        -- Exotel Call Data
        SELECT cr.id AS record_id, cr.lead_id, cr.Direction, cr.`From` AS call_from, cr.`To` AS call_to, cr.Status,               cr.StartTime, cr.EndTime, cr.Duration,
               'Exotel Call Data' AS source_table, cr.RecordingUrl,               l.MDR AS wife_mrd, l.walk_in_date,
               ll.name AS location_name,                z.id AS zone_id,
               CONCAT(st.firstname,' ',st.lastname) AS staff_name,                NULL AS audit_staff,
               ls.name AS lead_status_name        FROM tblcall_records cr
        LEFT JOIN tblleads l ON l.id = cr.lead_id        LEFT JOIN tblleads_locations ll ON ll.id = cr.location
        LEFT JOIN tblzones z ON z.id = ll.zone_id        LEFT JOIN tblstaff st ON st.staffid = l.assigned
        LEFT JOIN tblleads_status ls ON ls.id = l.status        WHERE DATE(cr.StartTime) BETWEEN ".$this->db->escape($from_date)." AND ".$this->db->escape($to_date)."
        UNION ALL
        -- Manual Call Data
        SELECT cr.id AS record_id, cr.lead_id, NULL AS Direction, NULL AS call_from, NULL AS call_to, NULL AS Status,               cr.uploaded_on AS StartTime, NULL AS EndTime, NULL AS Duration,
               'Manual Call Data' AS source_table, cr.file_url AS RecordingUrl,               l.MDR AS wife_mrd, l.walk_in_date,
               ll.name AS location_name,               z.id AS zone_id,
               CONCAT(st.firstname,' ',st.lastname) AS staff_name,               NULL AS audit_staff,
               ls.name AS lead_status_name        FROM tblcall_record cr
        LEFT JOIN tblleads l ON l.id = cr.lead_id        LEFT JOIN tblleads_locations ll ON ll.id = l.preferred_location
        LEFT JOIN tblzones z ON z.id = ll.zone_id        LEFT JOIN tblstaff st ON st.staffid = l.assigned
        LEFT JOIN tblleads_status ls ON ls.id = l.status
  WHERE DATE(cr.uploaded_on) BETWEEN ".$this->db->escape($from_date)." AND ".$this->db->escape($to_date)."
    ) AS all_calls    WHERE 1=1
    ";
    $data = $this->db->query($query)->result_array();
 $staff_audit = $this->db->where('lastname', 'Audit')->get(db_prefix().'staff')->row_array();
 foreach ($data as &$row) {
    if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND DATE(all_calls.StartTime) BETWEEN ".$this->db->escape($from_date)." AND ".$this->db->escape($to_date);
}
if (!empty($status) && $row['Status'] !== $status) {
        $row['_remove'] = true;
    }
    if (!empty($direction_filter) && $row['source_table'] !== $direction_filter) {
        $row['_remove'] = true;
    }
    if (!empty($direction) && strtolower($row['Direction'] ?? '') !== strtolower($direction)) {
        $row['_remove'] = true;
    }

    if (!empty($staff_name) && stripos($row['staff_name'] ?? '', $staff_name) === false) {
        $row['_remove'] = true;
    }

    if (!empty($staff_zone) && isset($row['zone_id']) && $row['zone_id'] != $staff_zone) {
        $row['_remove'] = true;
    }
    if (!empty($staff_location) && isset($row['location_name']) && $row['location_name'] != $staff_location) {
        $row['_remove'] = true;
    }

    if (!empty($lead_status) && isset($row['lead_status_name']) && $row['lead_status_name'] != $lead_status) {
        $row['_remove'] = true;
    }

    if (!empty($no_lead) && empty($row['lead_id'])) {
        $row['_remove'] = true;
    }

    if (!empty($no_staff_name) && empty($row['staff_name'])) {
        $row['_remove'] = true;
    }

    if (!empty($no_location) && empty($row['location_name'])) {
        $row['_remove'] = true;
    }

    if (!empty($no_lead_status) && empty($row['lead_status_name'])) {
        $row['_remove'] = true;
    }
        if (!empty($row['call_from'])) {
            $row['call_from'] = substr($row['call_from'], 0, 2) . '******' . substr($row['call_from'], -2);
        }
        if (!empty($row['call_to'])) {
            $row['call_to'] = substr($row['call_to'], 0, 2) . '******' . substr($row['call_to'], -2);
        }
        if (!empty($row['StartTime'])) {
            $row['StartTime'] = date("d-m-Y H:i:s", strtotime($row['StartTime']));
        }
        if (!empty($row['EndTime'])) {
            $row['EndTime'] = date("d-m-Y H:i:s", strtotime($row['EndTime']));
        }
   if (!empty($row['RecordingUrl']) && filter_var($row['RecordingUrl'], FILTER_VALIDATE_URL)) {
    if (strpos($row['RecordingUrl'], 'exotel.com') !== false) {
        $row['recording'] = '<audio controls style="width:180px;">
            <source src="https://app.draravindsivf.com/crm/api_exotel_issue.php?url=' . urlencode($row['RecordingUrl']) . '" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>';
    } else {
        $row['recording'] = '<audio controls style="width:170px;">
            <source src="' . htmlspecialchars($row['RecordingUrl']) . '" type="audio/mpeg">
        </audio>';
    }
} else {
    $row['recording'] = '-';
}

$row['staff_image'] = '';
    if (!empty($row['staff_name']) && !empty($row['lead_id'])) {
        $staff = $this->db->like("CONCAT(firstname, ' ', lastname)", $row['staff_name'])
                          ->get(db_prefix().'staff')->row_array();
        if ($staff) {
            $full_name = $staff['firstname'].' '.$staff['lastname'];
            $row['staff_image']  = '<a data-toggle="tooltip" title="'.$full_name.'" href="'.admin_url('profile/'.$staff['staffid']).'">'
                                 . staff_profile_image($staff['staffid'], ['staff-profile-image-small']).'</a>';
            $row['staff_image'] .= '<span class="hide">'.$full_name.'</span>';
        }
    }

    $row['audit_image'] = '';

    if ($staff_audit) {
        $full_name_Audit = $staff_audit['firstname'].' '.$staff_audit['lastname'];
        $row['audit_image']  = '<a data-toggle="tooltip" title="'.$full_name_Audit.'" href="'.admin_url('profile/'.$staff_audit['staffid']).'">'
                             . staff_profile_image($staff_audit['staffid'], ['staff-profile-image-small']).'</a>';
        $row['audit_image'] .= '<span class="hide">'.$full_name_Audit.'</span>';
    }
    }
    $data = array_filter($data, function($row) {
        return !isset($row['_remove']);
    });

    if ($search) {
        $data = array_filter($data, function($row) use ($search) {
            return str_contains(strtolower($row['lead_id']), strtolower($search)) ||
                   str_contains(strtolower(strip_tags($row['Direction'] ?? '')), strtolower($search)) ||
                   str_contains(strtolower($row['call_from'] ?? ''), strtolower($search)) ||
                   str_contains(strtolower($row['call_to'] ?? ''), strtolower($search)) ||
                   str_contains(strtolower($row['Status'] ?? ''), strtolower($search)) ||
                   str_contains(strtolower($row['source_table']), strtolower($search));
        });
    }
    echo json_encode(["data" => array_values($data)]);
}


}
