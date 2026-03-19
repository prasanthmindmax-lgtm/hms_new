<div class="table-responsive">
   <table class="table table-bordered roles no-margin">
      <thead>
         <tr>
            <th>Feature</th>
            <th>Capabilities</th>
         </tr>
      </thead>
      <tbody>
         <?php
            if (isset($member)) {
                $is_admin = is_admin($member->staffid);
            }
         foreach (get_available_staff_permissions($funcData) as $feature => $permission) { ?>
         <tr data-name="<?php echo $feature; ?>">
            <td>
               <b><?php echo $permission['name']; ?></b>
            </td>
            <td>
               <?php
                  if (isset($permission['before'])) {
                      echo $permission['before'];
                  }
                  foreach ($permission['capabilities'] as $capability => $name) {
                      $checked  = '';
                      $disabled = '';
                      if ((isset($is_admin) && $is_admin) ||
                   (is_array($name) && isset($name['not_applicable']) && $name['not_applicable']) ||
                   (
                       ($capability == 'view_own' || $capability == 'view'
                          && array_key_exists('view_own', $permission['capabilities']) && array_key_exists('view', $permission['capabilities']))
                      &&
                        (
                            (isset($member)
                         && staff_can(($capability == 'view' ? 'view_own' : 'view'), $feature, $member->staffid))
                        ||
                        (isset($role)
                         && has_role_permission($role->roleid, ($capability == 'view' ? 'view_own' : 'view'), $feature))
                        )
                   )
                  ) {
                          $disabled = ' disabled ';
                      } elseif ((isset($member) && staff_can($capability, $feature, $member->staffid))
                    || isset($role) && has_role_permission($role->roleid, $capability, $feature)) {
                          $checked = ' checked ';
                      } ?>
               <div class="checkbox">
                  <input
                     <?php if ($capability == 'view') { ?> data-can-view <?php } ?>
                     <?php if ($capability == 'view_own') { ?> data-can-view-own <?php } ?>
                     <?php if (is_array($name) && isset($name['not_applicable']) && $name['not_applicable']) { ?> data-not-applicable="true" <?php } ?>
                     type="checkbox"
                     <?php echo $checked; ?>
                     class="capability"
                     id="<?php echo $feature . '_' . $capability; ?>"
                     name="permissions[<?php echo $feature; ?>][]"
                     value="<?php echo $capability; ?>"
                     <?php echo $disabled; ?>>
                  <label for="<?php echo $feature . '_' . $capability; ?>">
                  <?php echo !is_array($name) ? $name : $name['name']; ?>
                  </label>
                    <?php
                      if (isset($permission['help']) && array_key_exists($capability, $permission['help'])) {
                          echo '<i class="fa-regular fa-circle-question" data-toggle="tooltip" data-title="' . $permission['help'][$capability] . '"></i>';
                      } ?>
               </div>
               <?php
                  } 
                  if($permission['name']=='Leads'){ ?>
                  <div class="checkbox">
                     <input type="checkbox" class="capability" name="new_lead" value="1" id="new_lead" <?=$member->new_lead==1 ? 'checked': ''?>> <label for="new_lead">New Lead</label><br />
                     <input type="checkbox" class="capability" name="export_lead" value="1" id="export_lead" <?=$member->export_lead==1 ? 'checked': ''?>> <label for="export_lead">Export Lead</label><br />
                     <input type="checkbox" class="capability" name="import_lead" value="1" id="import_lead" <?=$member->import_lead==1 ? 'checked': ''?>> <label for="import_lead">Import Lead</label><br />
                     <input type="checkbox" class="capability" name="inbound_call" value="1" id="inbound_call" <?=$member->inbound_call==1 ? 'checked': ''?>> <label for="inbound_call">Inbound Call</label><br />
                     
                     <input type="checkbox" class="capability inbound_display" name="exotel" value="1" id="exotel_permission" <?=$member->exotel==1 ? 'checked': ''?> style="display:<?=$member->inbound_call==1 ? 'block' : 'none'?>" /> <label for="exotel_permission" class="inbound_display" style="display:<?=$member->inbound_call==1 ? 'block' : 'none'?>">Exotel</label>
                     
                     <input type="checkbox" class="capability inbound_display" name="knowlarity" value="1" id="knowlarity_permission" <?=$member->knowlarity==1 ? 'checked': ''?> style="display:<?=$member->inbound_call==1 ? 'block' : 'none'?>" /> <label for="knowlarity_permission" style="display:<?=$member->inbound_call==1 ? 'block' : 'none'?>" class="inbound_display">Knowlarity</label>
                  </div>
                     <?php $statusSelect = explode(',', $member->lead_status);
                     $sourceSelect = explode(',', $member->lead_source);
                     $locationSelect = explode(',', $member->lead_location);
                     $newSource = ['id'=>'empty', 'name'=>'Empty'];
                     array_push($sources, $newSource);
                     
                     echo render_select('lead_status[]', $statuses, ['id', 'name'], '', $statusSelect, ['data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                     
                     echo render_select('lead_source[]', $sources, ['id', 'name'], '', $sourceSelect, ['data-width' => '100%', 'data-none-selected-text' => _l('leads_source'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                     
                     echo render_select('lead_location[]', get_all_locations(), [ 'id', [ 'name']], '', $locationSelect, ['data-width' => '100%', 'data-none-selected-text' => _l('dropdown_non_selected_tex'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                  }
                  if (isset($permission['after'])) {
                      echo $permission['after'];
                  }
                  ?>
            </td>
         </tr>
         <?php } ?>
      </tbody>
   </table>
</div>
