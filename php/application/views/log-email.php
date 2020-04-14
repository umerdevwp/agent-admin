<?php if($this->session->flashdata("error")): ?>
  <div class="alert alert-danger" role="alert">
  <?=$this->session->flashdata("error");?>
</div>
<?php endif; ?>
<section class="topbar"> 
  <!-- Breadcrumbs-->
  <ul class="breadcrumbs">
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal"><span class="breadcrumbs-icon fa-home"></span><span>Dashboard</span></a></li>
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/contacts">Notification Email Log</a> </li>
    <li class="breadcrumbs-item"><?php echo $entity->account_name; ?><span style="display:none;"><?php echo $entity->id; ?></style></li>
  </ul>
</section>
	
	
<section class="Jumbotron"><!--Company Summary Info-->
<div class="container">
<div class="row">
  <div class="col"></div>
</div>
<div class="row">
	<div class="col-md-9 col-lg-12">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Logs</span> </div>
        </div>
        <div class="panel-body p-0">
          <div class="table-responsive scroller scroller-horizontal py-3">
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper dt-bootstrap4">
              <div class="row row-10">
                <div class="col-sm-12 col-md-6 pl-3">
                <form name="frmLogMail" id="frmLogMail" method="post" action="">
                <input class="form-control" id="daterange" type="text" name="daterange"  value="<?=$this->input->post("daterange");?>">
                
                </form>
                </div>
                <div class="col-sm-12 col-md-6 pr-3"> </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <table class="table table-striped table-hover data-table dataTable" data-page-length="5" data-table-mode="multi-filter" id="DataTables_Table_3" role="grid" aria-describedby="DataTables_Table_3_info">
                    <thead>
                      <tr role="row">
                        <th class="sorting_disabled" data-column-index="0" rowspan="1" colspan="1" style="width: 241.2px;">Entity Name</th>
                        <th class="sorting_disabled" data-column-index="1" rowspan="1" colspan="1" style="width: 249.967px;">To Email</th>
                        <th class="sorting_disabled" data-column-index="2" rowspan="1" colspan="1" style="width: 241.217px;">Subject</th>
                        <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Status</th>
                        <th class="sorting_disabled" data-column-index="4" rowspan="1" colspan="1" style="width: 241.217px;">Send Time</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        if(count($aEmailLogs)>0) {
                            for($i = 0; $i < count($aEmailLogs); $i++){ ?>
                      <tr role="row" class="odd">
                        <td><a href="/entity/<?=$aEmailLogs[$i]->entity_id;?>"><?php echo $aEmailLogs[$i]->entity_name; ?></a></td>
                        <td><?php echo $aEmailLogs[$i]->to; ?></td>
                        <td><?php echo $aEmailLogs[$i]->subject; ?></td>
                        <td><?php echo $aEmailLogs[$i]->status; ?></td>
                        <td><?php echo $aEmailLogs[$i]->send_time; ?></td>
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
  jQuery(".applyBtn").on('click',function(){
    setTimeout(()=>{$("#frmLogMail")[0].submit();},500);
  });
}, 2000);
  

</script>