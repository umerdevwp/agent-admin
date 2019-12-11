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
          <div class="panel-title"><span class="panel-icon fa-map-marker"></span><span> RA Address </span><span class="badge badge-success">Active</span> </div>
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
  <div class="col-md-12 col-lg-12">
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">Compliance Check List <span class="span badge badge-danger"><?php echo count($tasks); ?></span></div>
      </div>
    
      <?php if(count($tasks) > 0){ ?>

      <?php
      // TODO: check tasks date to show warning on due tasks
      if($entity->PastDue){ ?>
      <div class="alert alert-danger alert-darker alert-lg"><span class="alert-icon fa-remove"></span><span>WARNING! Open Compliance Tasks May Be Past Due</span></div>
      <?php } ?>
      <div class="panel-body">
        <h3 class="list-sortable-title">Compliance Tasks</h3>
        <ul class="list-sortable sortable sortable-current" data-connect-group=".sortable-completed">
          <?php for($i = 0; $i < count($tasks); $i++)
                { 
                  if(!in_array($tasks[$i]->id,$this->session->temp["tasks_complete"]))
                  {
            ?>
          <li class="list-sortable-item-primary">
            <div class="custom-control custom-checkbox custom-check custom-checkbox-primary">
              <input class="custom-control-input taskListInput" type="checkbox" id="taskCheck<?php echo $i; ?>"  data-toggle="modal" data-target="#sure" onclick="setTaskId('<?=$tasks[$i]->id;?>')" />
                <label class="custom-control-label" for="taskCheck<?php echo $i; ?>"><?php echo date_format(date_create($tasks[$i]->due_date), "m/d/Y"); ?> - <?php echo $tasks[$i]->subject; ?></label>
            </div>
          </li>
          <?php }
              } ?>
        </ul>
        <div>
      </div>
    <?php } ?>
  </div>
</div>


<!-- THIS IS FOR ATTACHMENT -->
<div class="row">
	<div class="col-md-9 col-lg-12">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Attachments</span></div>
        </div>
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
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Contacts</span> </div>
        </div>
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
                    <tbody>
                      <?php 
                        if(count($contacts)>0) {
                            for($i = 0; $i < count($contacts); $i++){ ?>
                      <tr role="row" class="odd">
                        <td><?php echo $contacts[$i]->full_name; ?></td>
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
</section>
<script>

var iTaskId = 0;
function setTaskId(id)
{
  iTaskId = id;
}
function updateTask()
{
  if(iTaskId>0){
    document.location = 'task/update/'+iTaskId;
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