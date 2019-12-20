<?php if($this->session->flashdata("error")): ?>
  <div class="alert alert-danger" role="alert">
  <?=$this->session->flashdata("error");?>
</div>
<?php endif; ?>
<?php if($this->session->flashdata("ok")): ?>
  <div class="alert alert-success" role="alert">
  <?=$this->session->flashdata("ok");?>
</div>
<?php endif; ?>
<section class="topbar"> 
  <!-- Breadcrumbs-->
  <ul class="breadcrumbs">
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal"><span class="breadcrumbs-icon fa-home"></span><span>Dashboard</span></a></li>
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal">Entities</a> </li>
    <li class="breadcrumbs-item"><?php echo $entity->entity_name; ?><span style="display:none;"><?php echo $entity->id; ?></style></li>
  </ul>
</section>
	
	
<section class="Jumbotron"><!--Company Summary Info-->
<div class="container">
  <!-- THIS IS THE HEADING FOR ENTITY -->
	<div class="row">
	<div class="col"> 
	<h2><?php echo $entity->entity_name; ?><br>
      <span class="badge badge-primary"><?php echo $entity->entity_structure; ?></span> <span class="badge badge-secondary"><?php echo $entity->filing_state; ?></span></h2>
    </div></div>
    
  <!-- THIS IS FOR ADDRESS AND BILLING DETAIL BOXES -->
	<div class="row address-billing">
    <div class="col-md-12 four-boxes">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-address-card-o"></span><span>Company Info</span></div>
        </div>
    
        <div class="panel">
          
          <div class="panel-body">
            <div class="row">
              <dl class="dl-horizontal col-md-3">
                <div class="border-section">
                <dt>State ID</dt>
                <dd><?php echo $entity->entity_number; ?></dd>
                </div>
              </dl>
              <dl class="dl-horizontal col-md-3">
                <div class="border-section">
                <dt>Formation Date</dt>
                <dd><?php echo $entity->formation_date; ?></dd>
                </div>
              </dl>
              <dl class="dl-horizontal col-md-3">
                <div class="border-section">
                <dt>Expiration Date</dt>
                <dd><?php echo $entity->expiration_date; ?></dd>
                </div>
              </dl>
			        <dl class="dl-horizontal col-md-3">
                <div class="border-section">
                <dt>Tax Id</dt>
                <dd><?php echo $entity->ein; ?></dd>
                </div>
              </dl>
            </div>  
          </div>
        </div>
    </div>
  
    <div class="col-md-12 four-boxes">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-map-marker"></span><span> RA Address </span><!--<span class="badge badge-success">Active</span>--> </div>
        </div>
        <div class="panel">
          
          <div class="panel-body">
            <address>
            <div class="row">  
            <div class="col-md-6">
              <div class="border-section"><strong><?php echo $AgentAddress['file_as']; ?></strong></div>
            </div>
            <!-- <div class="col-md-6"><?php //echo $AgentAddress['address']; ?></div>
            <?php //echo $AgentAddress['address2']; ?><br> -->
            <div class="col-md-2">
              <div class="border-section"><?php echo $AgentAddress['city']; ?></div>
            </div>
            <div class="col-md-2">
              <div class="border-section"><?php echo $AgentAddress['state']; ?></div> 
            </div>  
            <div class="col-md-2">
              <div class="border-section"><?php echo $AgentAddress['zip_code']; ?></div>
            </div>  
            </div>
            <div class="row">
            <div class="col-md-3">
              <div class="border-section"><?php echo $AgentNumber['phone_number']; ?></div>
            </div>
            <div class="col-md-3">
              <div class="border-section"><?php echo $AgentEmail['email']; ?></div>
            </div>
            </div>
            </address>
          </div>
      </div>
    </div>
		<div class="col-md-12 four-boxes">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-map-marker"></span><span>Forwarding Address</span></div>
        </div>
        <div class="panel">
          
          <div class="panel-body">
            <address>
              <div class="row">
                <div class="col-md-6">
                  <div class="border-section">
                  <?php echo $entity->shipping_street; ?>
                  <?php echo $entity->shipping_city; ?>, <?php echo $entity->shipping_state; ?> <?php echo $entity->shipping_code; ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="border-section">
                  <abbr title="Phone">E:</abbr> <?php echo $entity->shipping_email; ?>
                  </div>
                </div>
              </div>
            </address>
        </div>
      </div>
    </div>
    <!--<div class="col-md-3 four-boxs">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-address-card-o"></span><span>Billing Address</span></div>
        </div>
        <div class="panel-body">
          <address>
                <?php //echo $entity->billing_street; ?><br>
                <?php //echo $entity->billing_city; ?>, <?php //echo $entity->billing_state; ?> <?php //echo $entity->billing_code; ?><br>
            <abbr title="Phone">E:</abbr> <?php //echo $entity->billing_email; ?>
            </address>
        </div>
      </div>
    </div>-->    
</div>

<!-- THIS IS COMPLIANCE CHECK LIST -->
<div class="row">
  <div class="col-md-12 col-lg-12 four-boxes">
    <div class="panel-header">
      <div class="panel-title">Compliance Check List <span class="span badge badge-danger"><?php echo count($tasks) - count($this->session->temp["tasks_complete"]); ?></span></div>
    </div>
    <div class="panel">
      <?php
      // TODO: check tasks date to show warning on due tasks
      if($entity->PastDue){ ?>
      <div class="alert alert-danger alert-darker alert-lg"><span class="alert-icon fa-remove"></span><span>WARNING! Open Compliance Tasks May Be Past Due</span></div>
      <?php } ?>
      <div class="panel-body">
        <h3 class="list-sortable-title">Compliance Tasks</h3>
        <?php if(count($tasks) > 0){ ?>
        <ul class="list-sortable sortable sortable-current" data-connect-group=".sortable-completed">
          <?php for($i = 0; $i < count($tasks); $i++)
                { 
                  if(!in_array($tasks[$i]->id,$this->session->temp["tasks_complete"]))
                  {
            ?>
          <li class="list-sortable-item-primary">
            <div class="custom-control custom-checkbox custom-check custom-checkbox-primary ">
              <input class="custom-control-input taskListInput" type="checkbox" id="taskCheck<?php echo $i; ?>"  data-toggle="modal" data-target="#sure" onclick="setTaskId('<?=$tasks[$i]->id;?>')" <?=($tasks[$i]->status=="Completed"||in_array($tasks[$i]->id,$tasks_completed)?"checked":"");?> />
                <label class="custom-control-label" for="taskCheck<?php echo $i; ?>"><?php echo date_format(date_create($tasks[$i]->due_date), "m/d/Y"); ?> - <?php echo $tasks[$i]->subject; ?></label>
            </div>
          </li>
          <?php }
              } ?>
        </ul>
        
    <?php } ?>
        <div>
      </div>
  </div>
</div>


<!-- THIS IS FOR ATTACHMENT -->
<div class="row">
	<div class="col-md-9 col-lg-12">
      <div class="panel-header">
        <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Attachments</span></div>
      </div>
      <div class="panel">
        
        <div class="panel-body p-0">
          <div class="table-responsive scroller scroller-horizontal py-3">
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper dt-bootstrap4">
              <div class="row row-10">
                <div class="col-sm-12 col-md-6 pl-3"></div>
                <div class="col-sm-12 col-md-6 pr-3"> </div>
              </div>
              <div class="row">
                <div class="col-sm-12 attachment-table">
                  <table class="table table-striped table-hover data-table dataTable" data-page-length="5" data-table-mode="multi-filter" id="DataTables_Table_3" role="grid" aria-describedby="DataTables_Table_3_info">
                    <thead>
                      <tr role="row">
                        <th class="sorting_disabled" data-column-index="0" rowspan="1" colspan="1" style="width: 241.2px;">File Name</th>
                        <th class="sorting_disabled" data-column-index="1" rowspan="1" colspan="1" style="width: 249.967px;">Attached By</th>
                        <th class="sorting_disabled" data-column-index="2" rowspan="1" colspan="1" style="width: 241.217px;">Date Added</th>
                        <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Size</th>
                       <!-- <th class="sorting_disabled" data-column-index="5" rowspan="1" colspan="1" style="width: 241.2px;">Edit</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        if(count($attachments)>0) {
                            for($i = 0; $i < count($attachments); $i++){ ?>
                      <tr role="row" class="odd">
                        <td>
                        <?php if($attachments[$i]->link_url=="Attachment"): ?>
                          <a href="portal/attachments/<?=$attachments[$i]->parent_id;?>/<?=$attachments[$i]->id;?>"><?php echo $attachments[$i]->file_name; ?></a>
                        <?php else: ?>
                          <a target="_blank" href="<?=$attachments[$i]->link_url;?>"><?php echo $attachments[$i]->file_name; ?></a>
                        <?php endif; ?>
                        </td>
                        <td><?php echo $attachments[$i]->created_by_name; ?></td>
                        <td><?php echo date_format(date_create($attachments[$i]->create_time), "m/d/Y H:i"); ?></td>
                        <td><?php echo getFileSize($attachments[$i]->size); ?></td>
                        <!-- <td><span class="panel-icon fa-pencil"></span></td> -->
                      </tr>
                      <?php } 
                        } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Name"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search By"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Added"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Size"></th>
                        <!--<th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Phone"></th>-->
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<!-- THIS IS CONTACT LIST -->
<div class="row">
	<div class="col-md-9 col-lg-12">
      <div class="panel-header">
        <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Contacts</span> <a href="#"   data-toggle="modal" data-target="#addMultiple" class="pull-right add-contact"><span class="fa-user-plus"></span> Add Contact</a></div>
      </div>
      <div class="panel">
        
        <div class="panel-body p-0">
          <div class="table-responsive scroller scroller-horizontal py-3">
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper dt-bootstrap4">
              <div class="row row-10">
                <div class="col-sm-12 col-md-6 pl-3"></div>
                <div class="col-sm-12 col-md-6 pr-3"> </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <table class="table table-striped table-hover data-table dataTable" data-page-length="5" data-table-mode="multi-filter" id="DataTables_Table_3" role="grid" aria-describedby="DataTables_Table_3_info">
                    <thead>
                      <tr role="row">
                        <th class="sorting_disabled" data-column-index="0" rowspan="1" colspan="1" style="width: 241.2px;">Name</th>
                        <th class="sorting_disabled" data-column-index="1" rowspan="1" colspan="1" style="width: 249.967px;">Contact Type</th>
                        <th class="sorting_disabled" data-column-index="2" rowspan="1" colspan="1" style="width: 241.217px;">Email</th>
                        <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Address</th>
                        <th class="sorting_disabled" data-column-index="4" rowspan="1" colspan="1" style="width: 241.217px;">Phone</th>
                       <!-- <th class="sorting_disabled" data-column-index="5" rowspan="1" colspan="1" style="width: 241.2px;">Edit</th> -->
                      </tr>
                    </thead>
                    <tbody id="contactTableTbody">
                      <?php 
                        if(count($contacts)>0) {
                            for($i = 0; $i < count($contacts); $i++){ ?>
                      <tr role="row" class="odd">
                        <td><?php echo $contacts[$i]->first_name . ' ' . $contacts[$i]->last_name; ?></td>
                        <td><?php echo $contacts[$i]->title; ?></td>
                        <td><?php echo $contacts[$i]->email; ?></td>
                        <td><?php echo $contacts[$i]->mailing_street; ?>, <?php echo $contacts[$i]->mailing_city; ?> <?php echo $contacts[$i]->mailing_state; ?> <?php echo $contacts[$i]->mailing_zip; ?></td>
                        <td><?php echo $contacts[$i]->phone; ?></td>
                        <!-- <td><span class="panel-icon fa-pencil"></span></td> -->
                      </tr>
                      <?php } 
                        } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <!-- <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Name"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Type"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Email"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Address"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Phone"></th> -->
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</div>


<!-- Modal -->
<div class="modal fade" id="sure" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Alert! Are you sure?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Do you want to update the task as complete?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="no">NO</button>
        <button type="button" class="btn btn-success" id="yes">YES</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal For Add Multiple Contacts-->
<div class="modal fade" id="addMultiple" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="exampleModalLabel"> Add Contact</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="contact-form" action="post" id="formContactMultiple">
          <div class="row">
            <div class="col-md-12 note">
              <p>Note: All fields are required to fill.</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
                <label>First Name <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactFirstName" name="inputContactFirstName" placeholder="First Name" value="" tabindex="1">
                <p id="inputContactFirstNameReq" class="errorMsg"></p>
                
                <label>Contact Type <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactType" name="inputContactType" placeholder="Contact Type" value="" tabindex="3">
                <p id="inputContactTypeReq" class="errorMsg"></p>
                
                <label>Address <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactStreet" name="inputContactStreet" placeholder="Street Address" value="" tabindex="5">
                <p id="inputContactStreetReq" class="errorMsg"></p>
                <label>State <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactState" name="inputContactState" placeholder="State/Region/Province" value="" tabindex="7">
                <p id="inputContactStateReq" class="errorMsg"></p>
                <label>Phone <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactPhone" name="inputContactPhone" placeholder="Phone Number" value="" tabindex="9">
                <p id="inputContactPhoneReq" class="errorMsg"></p>
            </div>
            <div class="col-md-6">
                <label>Last Name <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactLastName" name="inputContactLastName" placeholder="Last Name" value="" tabindex="2">
                <p id="inputContactLastNameReq" class="errorMsg"></p>
                <label>Email <span class="require">*</span></label>
                <input type="email" class="form-control" id="inputContactEmail" name="inputContactEmail" placeholder="Notification Email" value="" tabindex="4">
                <p id="inputContactEmailReq" class="errorMsg"></p>
                <label>City <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactCity" name="inputContactCity" placeholder="City" value="" tabindex="6">
                <p id="inputContactCityReq" class="errorMsg"></p>
                
                <label>Zipcode <span class="require">*</span></label>
                <input type="text" class="form-control" id="inputContactZipcode" name="inputContactZipcode" placeholder="Postal / Zip Code" value="" tabindex="8">
                <p id="inputContactZipcodeReq" class="errorMsg"></p>
                <input type="hidden" name="entityId" value="<?=$iEntityId;?>">
            </div>
          </div>  
        
          <div id="serverError" class="servererror">
            <span>Sorry we are unable to process your request, please try again.</span>
          </div>
          <div id="validateAddress" class="validaddress">
           <span>Sorry we are unable to validate contact address.</span>
            <div class="custom-control custom-switch custom-switch-sm">
            <input class="custom-control-input" type="checkbox" id="customSwitch21" name="acceptInvalidAddress" value="1"/>
            <label class="custom-control-label" for="customSwitch21"> Are you sure, you want to add this address?</label>
            
            </div>
           <!--<input type="checkbox" name="acceptInvalidAddress" value="1" >  Are you sure, you want to add this address?-->
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success save" id="saveClose" >Save/Close</button>
        <button type="button" class="btn btn-primary save" id="saveClose" >Save/Add New</button>
        
      </div>
    </div>
  </div>
</div>
</section>
<div class="successMessage" id="successMessageBox">
  <div class="smessage">Your information is successfully save!.</div>
</div>
<div class="editMessage" id="successEditMessageBox">
  <div class="semessage">Contact edit successfully update.</div>
</div>

<script src="components/base/jquery-3.4.1.min.js"></script>
<script>

var iTaskId = 0;
function setTaskId(id)
{
  iTaskId = id;
}
function updateTask()
{
  if(iTaskId>0){
    document.location = 'task/update/'+iTaskId+'?eid=<?=$iEntityId;?>';
  }
}
function uncheckTaskId()
{
  iTaskId = 0;
  jQuery(".taskListInput").prop("checked",false);
}

setTimeout(() => {
  jQuery("#no").on("click",uncheckTaskId);
  jQuery("#yes").on("click",updateTask);
}, 2000);
  

</script>
<script>
  $('#saveClose, #saveAddNew').click(function(ev){
    //console.log($(ev.target).attr("id"));
    // empty all error messages to send form again
    $(".errorMsg").text("");
    $('#contactLoader').show();

    $.ajax({
      type:"POST",
      url:"ajax/contact/save/",
      data:$('#formContactMultiple').serialize(),
      success: function(response){
        var returnedData = JSON.parse(response);
        $(".errorMsg").text("");
        $('#contactLoader').hide();

        // check there are any errors in response
        if(returnedData.type=='error'){
          for(field in returnedData.results)
          {
            $('#'+field+'Req').text(returnedData.results[field]);
          } 
          if(returnedData.results.length == 0){
            $('#validateAddress').show();
            $('#addMultiple').modal('hide');
            //$('.modal-backdrop').hide();
            //$('#successEditMessageBox').show().delay(10000).fadeOut();
            //console.log("Sorry we are unable to validate contact address.");
            //console.log(returnedData);
          }else if(returnedData.results == "Add contact failed"){
            $('#serverError').show();
          }
          
        // save or exit / save or reset
        } else if(returnedData.type=='ok'){
          
          $('#formContactMultiple')[0].reset();
          $('#validateAddress').hide();
          $('#successMessageBox').show().delay(10000).fadeOut();
          $('#addMultiple').modal('hide');
          
            var row = '<tr><td>';
            for(field in returnedData.results)
            {
               row += $(field).val() + "</td><td>";
            }
            row += "</td></tr>";
            $('#contactTableTbody').append(row);
          //console.log("Close modal or reset for new entries");
        } else {
          console.log("Server not responding, please try again later");
          console.log(returnedData);
        }

      }
    });
  });
</script>
<!-- <script>
$('#saveAddNew').on('click', function(){
  var fname = $('#inputContactFirstName').val();
  var lname = $('#inputContactLastName').val();
  var ctype = $('#inputContactType').val();
  var email = $('#inputContactEmail').val();
  var street = $('#inputContactStreet').val();
  var city = $('#inputContactCity').val();
  var state = $('#inputContactState').val();
  var zipcode = $('#inputContactZipcode').val();
  var phone = $('#inputContactPhone').val();
  var markup = "<tr><td>" + fname.lname + "</td><td>" + ctype + "</td><td>" + email + "</td><td>" + street.city.state.zipcode + "</td><td>" + phone + "</td>";
  $('table#DataTables_Table_3 tbody').append(markup);
});
</script> -->