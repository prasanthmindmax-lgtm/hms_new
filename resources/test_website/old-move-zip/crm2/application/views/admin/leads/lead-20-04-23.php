<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">
        <?php if (isset($lead)) {
    if (!empty($lead->name)) {
        $name = $lead->name;
    } elseif (!empty($lead->company)) {
        $name = $lead->company;
    } else {
        $name = _l('lead');
    }
    echo '#' . $lead->id . ' - ' . $name;
} else {
    echo _l('add_new', _l('lead_lowercase'));
}

if (isset($lead)) {
    echo '<div class="tw-ml-4 -tw-mt-2 tw-inline-block">';
    if ($lead->lost == 1) {
        echo '<span class="label label-danger">' . _l('lead_lost') . '</span>';
    } elseif ($lead->junk == 1) {
        echo '<span class="label label-warning">' . _l('lead_junk') . '</span>';
    } else {
        if (total_rows(db_prefix() . 'clients', [
          'leadid' => $lead->id, ])) {
            echo '<span class="label label-success">' . _l('lead_is_client') . '</span>';
        }
    }
    echo '</div>';
}
?>
    </h4>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($lead)) {
    echo form_hidden('leadid', $lead->id);
} ?>
            <div class="top-lead-menu">
                <?php if (isset($lead)) { ?>
                <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs">
                        <ul class="nav-tabs-horizontal nav nav-tabs<?php if (!isset($lead)) {
    echo ' lead-new';
} ?>" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab_lead_profile" aria-controls="tab_lead_profile" role="tab"
                                    data-toggle="tab">
                                    <?php echo _l('lead_profile'); ?>
                                </a>
                            </li>
                            <?php if (isset($lead)) { ?>
                            <?php if (count($mail_activity) > 0 || isset($show_email_activity) && $show_email_activity) { ?>
                            <li role="presentation">
                                <a href="#tab_email_activity" aria-controls="tab_email_activity" role="tab"
                                    data-toggle="tab">
                                    <?php echo hooks()->apply_filters('lead_email_activity_subject', _l('lead_email_activity')); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <li role="presentation" style="display:none">
                                <a href="#tab_proposals_leads"
                                    onclick="initDataTable('.table-proposals-lead', admin_url + 'proposals/proposal_relations/' + <?php echo $lead->id; ?> + '/lead','undefined', 'undefined','undefined',[6,'desc']);"
                                    aria-controls="tab_proposals_leads" role="tab" data-toggle="tab">
                                    <?php echo _l('proposals');
                        if ($total_proposals > 0) {
                            echo ' <span class="badge">' . $total_proposals . '</span>';
                        }
                        ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_tasks_leads"
                                    onclick="init_rel_tasks_table(<?php echo $lead->id; ?>,'lead','.table-rel-tasks-leads');"
                                    aria-controls="tab_tasks_leads" role="tab" data-toggle="tab">
                                    <?php echo _l('tasks');
                        if ($total_tasks > 0) {
                            echo ' <span class="badge">' . $total_tasks . '</span>';
                        }
                        ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                                    <?php echo _l('lead_attachments');
                        if ($total_attachments > 0) {
                            echo ' <span class="badge">' . $total_attachments . '</span>';
                        }
                        ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#lead_reminders"
                                    onclick="initDataTable('.table-reminders-leads', admin_url + 'misc/get_reminders/' + <?php echo $lead->id; ?> + '/' + 'lead', undefined, undefined,undefined,[1, 'asc']);"
                                    aria-controls="lead_reminders" role="tab" data-toggle="tab">
                                    <?php echo _l('leads_reminders_tab');
                           if ($total_reminders > 0) {
                               echo ' <span class="badge">' . $total_reminders . '</span>';
                           }
                           ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#lead_notes" aria-controls="lead_notes" role="tab" data-toggle="tab">
                                    <?php echo _l('lead_add_edit_notes');
                        if ($total_notes > 0) {
                            echo ' <span class="badge">' . $total_notes . '</span>';
                        }
                        ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#lead_activity" aria-controls="lead_activity" role="tab" data-toggle="tab">
                                    <?php echo _l('lead_add_edit_activity'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#marketing" aria-controls="marketing" role="tab" data-toggle="tab">
                                    Marketing
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#recordings" aria-controls="recordings" role="tab" data-toggle="tab">
                                    Call Recordings
                                </a>
                            </li>
							 <li role="presentation">
                                <a href="#sms" aria-controls="sms" role="tab" data-toggle="tab">
                                    SMS
                                </a>
                            </li>
                            <?php if (is_gdpr() && (get_option('gdpr_enable_lead_public_form') == '1' || get_option('gdpr_enable_consent_for_leads') == '1')) { ?>
                            <li role="presentation">
                                <a href="#gdpr" aria-controls="gdpr" role="tab" data-toggle="tab">
                                    <?php echo _l('gdpr_short'); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <?php } ?>
                            <?php hooks()->do_action('after_lead_lead_tabs', $lead ?? null); ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>
            </div>
            <!-- Tab panes -->
            <div class="tab-content mtop20">
                <!-- from leads modal -->
                <div role="tabpanel" class="tab-pane active" id="tab_lead_profile">
                    <?php $this->load->view('admin/leads/profile'); ?>
                </div>
                <?php if (isset($lead)) { ?>
                <?php if (count($mail_activity) > 0 || isset($show_email_activity) && $show_email_activity) { ?>
                <div role="tabpanel" class="tab-pane" id="tab_email_activity">
                    <?php hooks()->do_action('before_lead_email_activity', ['lead' => $lead, 'email_activity' => $mail_activity]); ?>
                    <?php foreach ($mail_activity as $_mail_activity) { ?>
                    <div class="lead-email-activity">
                        <div class="media-left">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="bold no-margin lead-mail-activity-subject">
                                <?php echo $_mail_activity['subject']; ?>
                                <br />
                                <small
                                    class="text-muted display-block mtop5 font-medium-xs"><?php echo _dt($_mail_activity['dateadded']); ?></small>
                            </h4>
                            <div class="lead-mail-activity-body">
                                <hr />
                                <?php echo $_mail_activity['body']; ?>
                            </div>
                            <hr />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <?php } ?>
                    <?php hooks()->do_action('after_lead_email_activity', ['lead_id' => $lead->id, 'emails' => $mail_activity]); ?>
                </div>
                <?php } ?>
                <?php if (is_gdpr() && (get_option('gdpr_enable_lead_public_form') == '1' || get_option('gdpr_enable_consent_for_leads') == '1' || (get_option('gdpr_data_portability_leads') == '1') && is_admin())) { ?>
                <div role="tabpanel" class="tab-pane" id="gdpr">
                    <?php if (get_option('gdpr_enable_lead_public_form') == '1') { ?>
                    <a href="<?php echo $lead->public_url; ?>" target="_blank" class="mtop5">
                        <?php echo _l('view_public_form'); ?>
                    </a>
                    <?php } ?>
                    <?php if ((get_option('gdpr_data_portability_leads') == '1' && is_admin()) || $staffData->export_lead == 1) { ?>
                    <?php
                  if (get_option('gdpr_enable_lead_public_form') == '1') {
                      echo ' | ';
                  }
                  ?>
                    <a href="<?php echo admin_url('leads/export/' . $lead->id); ?>">
                        <?php echo _l('dt_button_export'); ?>
                    </a>
                    <?php } ?>
                    <?php if (get_option('gdpr_enable_lead_public_form') == '1' || (get_option('gdpr_data_portability_leads') == '1' && is_admin())) { ?>
                    <hr class="-tw-mx-3.5" />
                    <?php } ?>
                    <?php if (get_option('gdpr_enable_consent_for_leads') == '1') { ?>
                    <h4 class="no-mbot">
                        <?php echo _l('gdpr_consent'); ?>
                    </h4>
                    <?php $this->load->view('admin/gdpr/lead_consent'); ?>
                    <hr />
                    <?php } ?>
                </div>
                <?php } ?>
                
                <div role="tabpanel" class="tab-pane" id="marketing">
                  <div class="col-md-6 col-xs-12 lead-information-col">
                    <div class="lead-info-heading">
                       <h4>
                           UTM Information
                       </h4>
                   </div>
                   <dl>
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           UTM Medium
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->utm_medium; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           UTM Campaign
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->utm_campaign; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           UTM ID
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->utm_id; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           UTM Term
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->utm_term; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           UTM Content
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->utm_content; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           URL
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->source_url; ?></dd>
                      </dl>
                  </div>
                  <div class="col-md-6 col-xs-12 lead-information-col">
                    <div class="lead-info-heading">
                       <h4>
                           Campagin Information
                       </h4>
                   </div>
                   <dl>
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           Ad Name
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->ad_name; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           Adset Name
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->adset_name; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           Campaign Name
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->campaign_name; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           Form Name
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->form_name; ?></dd>
                       
                       <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                           Page Name
                       </dt>
                       <dd class="tw-text-neutral-900 tw-mt-1 mbot15"><?php echo $lead->page_name; ?></dd>
                       
                      </dl>
                  </div>
                </div>
                
                <div role="tabpanel" class="tab-pane" id="recordings">
                    <div class="col-md-12 col-xs-12 lead-information-col">
                        <div class="lead-info-heading">
                            <h4> Call Recordings </h4>
                        </div>
                        <?php if($lead->sid!='') { ?>
                        <h4>Exotel</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Status</th>
                                    <th>Recording Url</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sids = explode(',', $lead->sid); 
                                foreach($sids as $sid){ $viewLog = getExotelLogs($sid); ?>
                                <tr>
                                    <td><?=$viewLog['Call']['StartTime']?></td>
                                    <td><?=$viewLog['Call']['EndTime']?></td>
                                    <td><?=$viewLog['Call']['Status']?></td>
                                    <td><?php if($viewLog['Call']['RecordingUrl']!=''){ ?><audio controls><source src="<?=$viewLog['Call']['RecordingUrl']?>" type="audio/mpeg"></audio><?php } ?>
                                    
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } 
                            $viewLogs = getKnowlarityLogs($lead->phonenumber, date('Y-m-d H:i:s')); /*print_r($viewLogs['objects']);*/
                            if(!empty($viewLogs['objects'])){ ?>
                        <h4>Knowlarity</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Start Time</th>
                                    <th>Recording Url</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($viewLogs['objects'] as $log){ ?>
                                <tr>
                                    <td><?=$log['start_time']?></td>
                                    <td><?php if($log['call_recording']!=''){ ?><audio controls><source src="<?=$log['call_recording']?>" type="audio/mpeg"></audio><?php } ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>
				
				
				 <div role="tabpanel" class="tab-pane" id="sms">
                    <div class="col-md-12 col-xs-12 lead-information-col">
                        <div class="lead-info-heading">
                            <h4> SMS </h4>
                        </div>
                        <?php if($lead->preferred_location!='') { ?>
                        <?php echo form_open(admin_url('leads/send_sms')); ?>
                            <input type="hidden" name="lead_id" value="<?=$lead->id?>" />
                            <input type="hidden" name="preferred_location" value="<?=$lead->preferred_location?>" />
							 <input type="hidden" name="name" value="<?=$lead->name?>" />
							 <input type="hidden" name="walk_in_date" value="<?=$lead->walk_in_date?>" />
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="language">Language</label>
                                    <select name="language" id="language" class="form-control" required>
                                        <option value="">Select Language</option>
										<option value="Tamil">Tamil</option>
                                        <option value="English">English</option>
										<option value="Malayalam">Malayalam</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="template">Template</label>
                                    <select name="template" id="template" class="form-control" required>
                                        <option value="">Select Template</option>
                                        <option value="Welcome">Welcome</option>
                                        <option value="Location">Location</option>
                                        <option value="Walk in">Walk in Sheduled</option>
                                    </select>
                                </div>
				
								
                                
                                <div class="col-md-2">
                                    <br />
                                    <input type="submit" class="btn btn-success" value="Send SMS" />
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                        <?php } ?>
						<br>
							<?php 
				$this->db->where('id', $lead->preferred_location);
				$query = $this->db->get('tblleads_locations');
				$location = $query->row()->name;
				
				?>
				<div id="template-data"></div>

<script>
$(document).ready(function() {
    $('#template').change(function() {
        var selectedLanguage = $('#language').val();
        var selectedTemplate = $(this).val();
        
        if (selectedTemplate === 'Welcome') {
            $.ajax({
                url: '<?php echo admin_url("leads/get_template_data"); ?>',
                type: 'post',
                dataType: 'json',
                data: {
                    language: selectedLanguage,
                    template: selectedTemplate
                },
                success: function(response) {
					console.log(response);
                    if (response.success) {
                        $('#template-data').html(response.message);
                    } else {
                       // alert("An error occurred while retrieving the template data: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert("An error occurred while retrieving the template data: " + error);
                }
            });
        } else {
            $('#template-data').html('');
        }
    });
});
</script>
<script>
  $(document).ready(function() {
    var lastClickedLanguage = ''; // keep track of last clicked language
    
    $('#language').change(function() {
      // Reset the selected template
      $('#template').val('');
      
      // Get the selected language and preferred location
      var selectedLanguage = $(this).val();
      var preferredLocation = '<?= $location; ?>';
	   var leadname = '<?= $lead->name; ?>';
	   var walkindate = '<?= $lead->walk_in_date; ?>';

      // Hide all language divs except the last clicked one
      $('div[id^=' + preferredLocation + '-]').not('#' + preferredLocation + ' - ' + lastClickedLanguage).hide();

      // Call the get_message function to retrieve the message
      $.ajax({
        url: "<?php echo admin_url('leads/get_message'); ?>",
        type: 'post',
        dataType: 'json',
        data: {
			language: selectedLanguage,
			template: '',
			location: preferredLocation,
			leadname:leadname,
			walkindate:walkindate
			},
        success: function(response) {
          if (response.success) {
			  
            // Display the message in the corresponding div
            $('#' + preferredLocation + '-' + selectedLanguage).html(response.message);
            $('#' + preferredLocation + '-' + selectedLanguage).show(); // Show the div after setting the content
            lastClickedLanguage = selectedLanguage; // update last clicked language
          } else {
            // Display the error message
            //alert("An error occurred while retrieving the message: " + response.message);
          }
        },
        error: function(xhr, status, error) {
          // Display an error message
          alert("An error occurred while retrieving the message: " + error);
        }
      });
    });

    $('#template').change(function() {
      // Get the selected language, template, and preferred location
      var selectedLanguage = $('#language').val();
      var selectedTemplate = $(this).val();
      var preferredLocation = '<?= $location; ?>';
	 //var preferredLocation = '<?= str_replace(" ", "", str_replace("-", " _ ", $location)); ?>';
	  
	  var leadname = '<?= $lead->name; ?>';
	   var walkindate = '<?= $lead->walk_in_date; ?>';
      
      // Hide all language divs except the last clicked one
      $('div[id^=' + preferredLocation + '-]').not('#' + preferredLocation + '-' + lastClickedLanguage).hide();

      // Call the get_message function to retrieve the message
      $.ajax({
        url: "<?php echo admin_url('leads/get_message'); ?>",
        type: 'post',
        dataType: 'json',
        data: {
			language: selectedLanguage, 
			template: selectedTemplate, 
			location: preferredLocation,
			leadname:leadname,
			walkindate:walkindate
			},
        success: function(response) {
          if (response.success) {
            // Display the message in the corresponding div
            $('#' + preferredLocation + '-' + selectedLanguage).html(response.message);
            $('#' + preferredLocation + '-' + selectedLanguage).show(); // Show the div after setting the content
          } else {
            // Display the error message
          //  alert(" " + response.message);
          }
        },
        error: function(xhr, status, error) {
          // Display an error message
          alert("An error occurred while retrieving the message: " + error);
        }
      });
    });
  });
</script> 


			<script>
			/*$(document).ready(function() {
  $('#language, #template').change(function() {
    // Get the selected language and template values
    var selectedLanguage = $('#language').val();
    var selectedTemplate = $('#template').val();
    var preferredLocation = '<?= $location; ?>';
                
    // Call the get_message function to retrieve the message
    $.ajax({
      url: "<?php echo admin_url('leads/get_message'); ?>",
      type: 'post',
      dataType: 'json',
      data: {language: selectedLanguage, template: selectedTemplate, location: preferredLocation},
      success: function(response) {
        if (response.success) {
          // Display the message in the corresponding div
          $('#' + preferredLocation + '-' + selectedLanguage).html(response.message);
          $('#' + preferredLocation + '-' + selectedLanguage).show(); // Show the div after setting the content
        } else {
          // Display the error message
          alert("An error occurred while retrieving the message: " + response.message);
        }
      },
      error: function(xhr, status, error) {
        // Display an error message
      //  alert("An error occurred while retrieving the message: " + error);
      }
    });
  });
}); */

</script>
    
    <div id="<?=$location ?>-Tamil" style="display: none;"></div>
    <div id="<?=$location ?>-English" style="display: none;"></div>
    <div id="<?= $location ?>-Malayalam" style="display: none;"></div>
						
                     <!--  <div id="tamil-div" style="display: none;">
						 டாக்டர் அரவிந்த் ஐவிஎஃப் வாடிக்கையாளர் சேவை மையத்தைத் தொடர்பு கொண்டதற்கு நன்றி. எங்கள் நிர்வாகி விரைவில் உங்களைத் தொடர்புகொள்வார். இந்த நாள் இனிதாகட்டும்!
						</div>
						<div id="english-div" style="display: none;">
						   Thank you for your interest in Dr. Aravind's IVF. We’d love to connect over a quick call if you’re available today regarding your enquiry.
						</div>
						<div id="malayalam-div" style="display: none;">
						  ഡോ. അരവിന്ദ് IVF ഫെർട്ടിലിറ്റി & പ്രെഗ്നൻസി സെന്ററുമായി ബന്ധപ്പെട്ടതിന് നന്ദി. ഞങ്ങളുടെ റിലേഷൻഷിപ്പ് എക്സിക്യൂട്ടീവ് ഉടൻ നിങ്ങളെ ബന്ധപ്പെടും. ഹെൽപ്പ് ലൈൻ 9020142014
						</div>  -->
						<script>
			/*var templateSelect = document.getElementById("template");
			var tamilDiv = document.getElementById("tamil-div");
			var englishDiv = document.getElementById("english-div");
			var malayalamDiv = document.getElementById("malayalam-div");
			
			document.getElementById("language").addEventListener("change", function() {
				var selectedOption = this.value;
				templateSelect.value = "";
				tamilDiv.style.display = "none";
				englishDiv.style.display = "none";
				malayalamDiv.style.display = "none";
			});

			document.getElementById("template").addEventListener("change", function() {
				var selectedOption = this.value;
				if (selectedOption === "Welcome") {
					if (document.getElementById("language").value === "Tamil") {
						tamilDiv.style.display = "block";
					} else if (document.getElementById("language").value === "English") {
						englishDiv.style.display = "block";
					} else if (document.getElementById("language").value === "Malayalam") {
						malayalamDiv.style.display = "block";
					}
				} else {
					tamilDiv.style.display = "none";
					englishDiv.style.display = "none";
					malayalamDiv.style.display = "none";
		}
		});
					document.getElementById("language").addEventListener("change", function() {
			templateSelect.value = "";
		});		 */
	</script>
				
    		
		
                    </div>
                </div>
                
                <div role="tabpanel" class="tab-pane" id="lead_activity">
                    <div>
                        <div class="activity-feed">
                            <?php foreach ($activity_log as $log) { ?>
                            <div class="feed-item">
                                <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip"
                                        data-title="<?php echo _dt($log['date']); ?>">
                                        <?php echo time_ago($log['date']); ?>
                                    </span>
                                </div>
                                <div class="text">
                                    <?php if ($log['staffid'] != 0) { ?>
                                    <a href="<?php echo admin_url('profile/' . $log['staffid']); ?>">
                                        <?php echo staff_profile_image($log['staffid'], ['staff-profile-xs-image pull-left mright5']);
                              ?>
                                    </a>
                                    <?php
                              }
                              $additional_data = '';
                              if (!empty($log['additional_data'])) {
                                  $additional_data = unserialize($log['additional_data']);
                                  echo ($log['staffid'] == 0) ? _l($log['description'], $additional_data) : $log['full_name'] . ' - ' . _l($log['description'], $additional_data);
                              } else {
                                  echo $log['full_name'] . ' - ';
                                  if ($log['custom_activity'] == 0) {
                                      echo _l($log['description']);
                                  } else {
                                      echo _l($log['description'], '', false);
                                  }
                              }
                              ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_textarea('lead_activity_textarea', '', '', ['placeholder' => _l('enter_activity')], [], 'mtop15'); ?>
                            <div class="text-right">
                                <button id="lead_enter_activity"
                                    class="btn btn-primary"><?php echo _l('submit'); ?></button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_proposals_leads">
                    <?php if (has_permission('proposals', '', 'create')) { ?>
                    <a href="<?php echo admin_url('proposals/proposal?rel_type=lead&rel_id=' . $lead->id); ?>"
                        class="btn btn-primary mbot25"><?php echo _l('new_proposal'); ?></a>
                    <?php } ?>
                    <?php if (total_rows(db_prefix() . 'proposals', ['rel_type' => 'lead', 'rel_id' => $lead->id]) > 0 && (has_permission('proposals', '', 'create') || has_permission('proposals', '', 'edit'))) { ?>
                    <a href="#" class="btn btn-primary mbot25" data-toggle="modal"
                        data-target="#sync_data_proposal_data"><?php echo _l('sync_data'); ?></a>
                    <?php $this->load->view('admin/proposals/sync_data', ['related' => $lead, 'rel_id' => $lead->id, 'rel_type' => 'lead']); ?>
                    <?php } ?>
                    <?php
                  $table_data = [
                   _l('proposal') . ' #',
                   _l('proposal_subject'),
                   _l('proposal_total'),
                   _l('proposal_date'),
                   _l('proposal_open_till'),
                   _l('tags'),
                   _l('proposal_date_created'),
                   _l('proposal_status'), ];
                  $custom_fields = get_custom_fields('proposal', ['show_on_table' => 1]);
                  foreach ($custom_fields as $field) {
                      array_push($table_data, [
                       'name'     => $field['name'],
                       'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                    ]);
                  }
                  $table_data = hooks()->apply_filters('proposals_relation_table_columns', $table_data);
                  render_datatable($table_data, 'proposals-lead', [], [
                      'data-last-order-identifier' => 'proposals-relation',
                      'data-default-order'         => get_table_last_order('proposals-relation'),
                  ]);
                  ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_tasks_leads">
                    <?php init_relation_tasks_table(['data-new-rel-id' => $lead->id, 'data-new-rel-type' => 'lead']); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="lead_reminders">
                    <a href="#" data-toggle="modal" class="btn btn-default"
                        data-target=".reminder-modal-lead-<?php echo $lead->id; ?>"><i class="fa-regular fa-bell"></i>
                        <?php echo _l('lead_set_reminder_title'); ?></a>
                    <hr />
                    <?php render_datatable([ _l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders-leads'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="attachments">
                    <?php echo form_open('admin/leads/add_lead_attachment', ['class' => 'dropzone mtop15 mbot15', 'id' => 'lead-attachment-upload']); ?>
                    <?php echo form_close(); ?>
                    <?php if (get_option('dropbox_app_key') != '') { ?>
                    <hr />
                    <div class=" pull-left">
                        <?php if (count($lead->attachments) > 0) { ?>
                        <a href="<?php echo admin_url('leads/download_files/' . $lead->id); ?>" class="bold">
                            <?php echo _l('download_all'); ?> (.zip)
                        </a>
                        <?php } ?>
                    </div>
                    <div class="pull-right">
                        <button class="gpicker">
                            <i class="fa-brands fa-google" aria-hidden="true"></i>
                            <?php echo _l('choose_from_google_drive'); ?>
                        </button>
                        <div id="dropbox-chooser-lead"></div>
                    </div>
                    <div class=" clearfix"></div>
                    <?php } ?>
                    <?php if (count($lead->attachments) > 0) { ?>
                    <div class="mtop20" id="lead_attachments">
                        <?php $this->load->view('admin/leads/leads_attachments_template', ['attachments' => $lead->attachments]); ?>
                    </div>
                    <?php } ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="lead_notes">
                    <?php echo form_open(admin_url('leads/add_note/' . $lead->id), ['id' => 'lead-notes']); ?>
                    <div class="form-group">
                        <textarea id="lead_note_description" name="lead_note_description" class="form-control"
                            rows="4"></textarea>
                    </div>
                    <div class="lead-select-date-contacted hide">
                        <?php echo render_datetime_input('custom_contact_date', 'lead_add_edit_datecontacted', '', ['data-date-end-date' => date('Y-m-d')]); ?>
                    </div>
                    <div class="radio radio-primary">
                        <input type="radio" name="contacted_indicator" id="contacted_indicator_yes" value="yes">
                        <label
                            for="contacted_indicator_yes"><?php echo _l('lead_add_edit_contacted_this_lead'); ?></label>
                    </div>
                    <div class="radio radio-primary">
                        <input type="radio" name="contacted_indicator" id="contacted_indicator_no" value="no" checked>
                        <label for="contacted_indicator_no"><?php echo _l('lead_not_contacted'); ?></label>
                    </div>
                    <button type="submit"
                        class="btn btn-primary pull-right"><?php echo _l('lead_add_edit_add_note'); ?></button>
                    <?php echo form_close(); ?>
                    <div class="clearfix"></div>
                    <hr />
                    <?php
                     $len = count($notes);
                     $i   = 0;
                     foreach ($notes as $note) { ?>
                    <div class="media lead-note">
                        <a href="<?php echo admin_url('profile/' . $note['addedfrom']); ?>" target="_blank">
                            <?php echo staff_profile_image($note['addedfrom'], ['staff-profile-image-small', 'pull-left mright10']); ?>
                        </a>
                        <div class="media-body">
                            <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                            <a href="#" class="pull-right text-danger"
                                onclick="delete_lead_note(this,<?php echo $note['id']; ?>, <?php echo $lead->id; ?>);return false;">

                                <i class="fa fa fa-times"></i></a>
                            <a href="#" class="pull-right mright5"
                                onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;">
                                <i class="fa-regular fa-pen-to-square"></i>
                                <?php } ?>

                                <a href="<?php echo admin_url('profile/' . $note['addedfrom']); ?>" target="_blank">
                                    <h5 class="media-heading tw-font-semibold tw-mb-0">
                                        <?php if (!empty($note['date_contacted'])) { ?>
                                        <span data-toggle="tooltip"
                                            data-title="<?php echo _dt($note['date_contacted']); ?>">
                                            <i class="fa fa-phone-square text-success" aria-hidden="true"></i>
                                        </span>
                                        <?php } ?>
                                        <?php echo get_staff_full_name($note['addedfrom']); ?>
                                    </h5>
                                    <span class="tw-text-sm tw-text-neutral-500">
                                        <?php echo _l('lead_note_date_added', _dt($note['dateadded'])); ?>
                                    </span>
                                </a>

                                <div data-note-description="<?php echo $note['id']; ?>" class="text-muted mtop10">
                                    <?php echo check_for_links(app_happy_text($note['description'])); ?>
                                </div>
                                <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide mtop15">
                                    <?php echo render_textarea('note', '', $note['description']); ?>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-default"
                                            onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                        <button type="button" class="btn btn-primary"
                                            onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                    </div>
                                </div>
                        </div>
                        <?php if ($i >= 0 && $i != $len - 1) {
                         echo '<hr />';
                     }
                        ?>
                    </div>
                    <?php $i++; } ?>
                </div>
                <?php } ?>
                <?php hooks()->do_action('after_lead_tabs_content', $lead ?? null); ?>
            </div>
        </div>
		
		

    </div>
</div>
<?php hooks()->do_action('lead_modal_profile_bottom', (isset($lead) ? $lead->id : '')); ?>
<?php 
function getExotelLogs($sid)
{
    $api_key = "c267dce32ef8a97a1c8b1a079eabb2422125db041c5c4a84";
    $api_token = "9ff83ebd9ad246462522675f57b428e1e813155a5dec0d31";
    $exotel_sid = "draravindsivf1";
    $url = "https://" . $api_key . ":" . $api_token . "@api.exotel.com/v1/Accounts/" . $exotel_sid . "/Calls/".$sid.".json";
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_VERBOSE, 1); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $http_result = curl_exec($ch); 
    curl_close($ch);
    return json_decode($http_result, true);
}

function getKnowlarityLogs($mobile, $startDate)
{
    if(strlen($mobile)==10){
        $mobile = '%2B91'.$mobile;
    } else {
        $mobile = '%2B'.str_replace('+','',$mobile);
    }
    $startDate = date('Y-m-d', strtotime($startDate)); $endDate = date('Y-m-d');
    $url = "https://kpi.knowlarity.com/Basic/v1/account/calllog?start_time=2023-01-01%2000%3A00%3A00%2B05%3A30&end_time=$endDate%2023%3A59%3A59%2B05%3A30&customer_number=$mobile";
    $headers = array(
        'Content-Type: application/json', 
        'Authorization: 0b5f527a-e2b1-4351-80c4-c14bf6273040',
        'x-api-key:1t3ZvFuzUD8kjxGvVfhtx2VWwEmp3EyC7hxHEkT8'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_VERBOSE, 1); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $http_result = curl_exec($ch);
    curl_close($ch);
    return json_decode($http_result, true);
} ?>