<?php if($this->session->flashdata("error")): ?>
  <div class="alert alert-danger" role="alert">
  <?=$this->session->flashdata("error");?>
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
	<div class="row">
	<div class="col"> 
	<h2><?php echo $entity->entity_name; ?><br>
      <span class="badge badge-primary"><?php echo $entity->entity_type; ?></span> <span class="badge badge-secondary"><?php echo $entity->filing_state; ?></span></h2>
		</div></div>
	<div class="row">
		<div class="col-md-3">
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><span class="panel-icon fa-map-marker"></span><span>Forwarding Address</span></div>
          </div>
          <div class="panel-body">
            <address>
                <?php echo $entity->billing_street; ?><br>
                <?php echo $entity->billing_city; ?>, <?php echo $entity->billing_state; ?> <?php echo $entity->billing_code; ?><br>
            <abbr title="Phone">E:</abbr> <?php echo $entity->billing_email; ?>
            </address>
          </div>
        </div>
      </div>
		<div class="col-md-3">
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><span class="panel-icon fa-map-marker"></span><span> RA Address </span><span class="badge badge-success">Active</span> </div>
          </div>
          <div class="panel-body">
            <address>
            <strong>United Agent Services LLC</strong><br>
            2 East Congress<br>
            Suite 900-126<br>
            City, ST 99999
            </address>
          </div>
        </div>
      </div>
		<div class="col-md-4">
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><span class="panel-icon fa-address-card-o"></span><span>Company Info</span></div>
          </div>
          <div class="panel-body">
              <dl class="dl-horizontal">
              <dt>State ID</dt>
              <dd><?php echo $entity->entity_number; ?></dd>
            </dl>
            <dl class="dl-horizontal">
              <dt>Formation Date</dt>
              <dd><?php echo $entity->formation_date; ?></dd>
            </dl>
            <dl class="dl-horizontal">
              <dt>Expiration Date</dt>
              <dd><?php echo $entity->expiration_date; ?></dd>
            </dl>
			  <dl class="dl-horizontal"><dt>Tax Id</dt><dd><?php echo $entity->ein; ?></dd></dl>
          </div>
        </div>
      </div>
    
	<div class="row" style="width:100%">
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
                <?php for($i = 0; $i < count($tasks); $i++){ ?>
                <li class="list-sortable-item-primary">
                  <div class="custom-control custom-checkbox custom-check custom-checkbox-primary">
                    <input class="custom-control-input taskListInput" type="checkbox" id="taskCheck<?php echo $i; ?>"  data-toggle="modal" data-target="#sure" onclick="setTaskId(<?=$tasks[$i]->id;?>)" />
                      <label class="custom-control-label" for="taskCheck<?php echo $i; ?>"><?php echo date_format(date_create($tasks[$i]->due_date), "m/d/Y"); ?> - <?php echo $tasks[$i]->subject; ?></label>
                  </div>
                </li>
                <?php } ?>
              </ul>
            </div>
          </div>

          <?php } ?>

        </div>
	</div>
    
<div class="row">
	<div class="col-md-9 col-lg-12">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Attachments</span> </div>
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
                        <td><a href="portal/attachments/<?=$attachments[$i]->owner;?>/<?=$attachments[$i]->id;?>"><?php echo $attachments[$i]->file_name; ?></a></td>
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
	  </div></div></div>


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
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Name"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Type"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Email"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Address"></th>
                        <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Phone"></th>
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
	  </div></div></div>


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
  console.log(iTaskId);
  if(iTaskId>0){
    document.location = 'update/task/'+iTaskId;
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