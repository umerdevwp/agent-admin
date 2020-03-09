<?php if($this->session->flashdata("error")): ?>
  <div class="alert alert-danger" role="alert">
  <?=$this->session->flashdata("error");?>
</div>
<?php endif; ?>
<section class="topbar"> 
  <!-- Breadcrumbs-->
  <ul class="breadcrumbs">
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal"><span class="breadcrumbs-icon fa-home"></span><span>Dashboard</span></a></li>
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal">Add New Task</a> </li>
  </ul>
</section>
	
	
<section class="Jumbotron"><!--Company Summary Info-->
<div class="container">
	<div class="row">
	    <div class="col"> 


      

        </div>
    </div>

    <div class="row">
	<div class="col-md-12 col-lg-12 entity-form">
      <div class="panel-header">
        <div class="panel-title">Notification Task</div>
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
                  <div class="col-sm-12 client-form">
                <form method="post" action="/task/add" name="formAdd" id="formAdd">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputForEntity">For Entity:<span class="steric">*</span></label>
      <input type="text" class="form-control" name="inputForEntity" id="inputForEntity" value="<?=$this->input->post("inputForEntity");?>" placeholder="Type for lookup" />
      <?php echo form_error('inputForEntity'); ?>
    </div>
    <div class="col-md-6">
      <label for="inputDueDate">Due Date <span class="steric">*</span></label>
        <div class="input-group" id="datetime">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputDueDate"><span class="fa-calendar"></span></label>
              </div>
              <input type="text" class="form-control" name="inputDueDate" id="inputDueDate" value="<?=$this->input->post("inputDueDate");?>" placeholder="Due Date" data-datetimepicker="" />
        </div>
        <?php echo form_error('inputDueDate'); ?>
    </div>

  <div class="form-group col-md-6">
      <label for="inputStartDate">Start Notification<span class="steric">*</span></label>
        
        <div class="input-group" id="datetime">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputStartDate"><span class="fa-calendar"></span></label>
              </div>
            <input type="text" class="form-control" name="inputStartDate" id="inputStartDate" value="<?=$this->input->post("inputStartDate");?>" placeholder="Start Date"  data-datetimepicker="" />
            <!-- <span class="input-group-addon" for="datetime">
                <span class="input-group-text"><span class="fa fa-calendar"></span></span>
            </span> -->
        </div>
        <?php echo form_error('inputStartDate'); ?>
      
    </div>
    <!--<div class="form-group col-md-4">
        <label for="inputTaskPriority">Notifications Priority:<span class="steric">*</span></label>
        <div class="group-10">
          <div class="d-inline-block">
            <div class="custom-control custom-radio">
              <input class="custom-control-input" type="radio" id="mqqvxfgi" name="customRadio">
              <label class="custom-control-label" for="mqqvxfgi">High
              </label>
            </div>
          </div>
          <div class="d-inline-block">
            <div class="custom-control custom-radio">
              <input class="custom-control-input" type="radio" id="axhbyeui" name="customRadio">
              <label class="custom-control-label" for="axhbyeui">Normal
              </label>
            </div>
          </div>
          <div class="d-inline-block">
            <div class="custom-control custom-radio">
              <input class="custom-control-input" type="radio" id="bxqrkyhj" name="customRadio">
              <label class="custom-control-label" for="bxqrkyhj">Low
              </label>
            </div>
          </div>
        </div>
      </div>-->
    
    <div class="form-group col-md-6">
      <label for="checkTypeEmail">Type:<span class="steric">*</span></label>

      <div class="group-10">
      <div class="d-inline-block">
        <div class="custom-control custom-checkbox">
          <input class="custom-control-input" type="checkbox" name="checkTypeEmail" id="checkTypeEmail">
          <label class="custom-control-label" for="checkTypeEmail">E-Mail

          </label>
        </div>
</div>
<div class="d-inline-block">
        <div class="custom-control custom-checkbox">
          <input class="custom-control-input" type="checkbox" name="checkTypeSms" id="checkTypeSms">
          <label class="custom-control-label" for="checkTypeSms">SMS

          </label>
        </div>
</div>
<div class="d-inline-block">
        <div class="custom-control custom-checkbox">
          <input class="custom-control-input" type="checkbox" name="checkTypeBrowser" id="checkTypeBrowser">
          <label class="custom-control-label" for="checkTypeBrowser">Browser

          </label>
        </div>
</div>
      </div>
      <?php echo form_error('checkTypeEmail'); ?>
    </div>
    
      <div class="form-group col-md-3">
        Once Before Days Remaining:
        <div class="input-group form-group">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <input type="checkbox" name="checkBeforeDays" id="checkBeforeDays" aria-label="Checkbox for following text input">
            </div>
          </div>
          
          <input class="form-control" name="inputBeforeDays" id="inputBeforeDays" type="number" value="0" aria-label="Notify Before Days">
          <?php echo form_error('inputBeforeDays'); ?>
        </div>
      </div>
      <div class="form-group col-md-3">
        Once Before Months Remaining:
        <div class="input-group form-group">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <input type="checkbox" name="checkBeforeMonths" id="checkBeforeMonths" aria-label="Checkbox for following text input">
            </div>
          </div>
          <input class="form-control" name="inputBeforeMonths" id="inputBeforeMonths" type="number" value="0" aria-label="Text input with checkbox">
          <?php echo form_error('inputBeforeMonths'); ?>
        </div>
      </div>
      <div class="form-group col-md-3">
        After Interval of Days:
        <div class="input-group form-group">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <input type="checkbox" name="checkIntervalDays" id="checkIntervalDays" aria-label="Checkbox for following text input">
            </div>
          </div>
          <input class="form-control" name="inputIntervalDays" id="inputIntervalDays" type="number" value="0" aria-label="Text input with checkbox">
          <?php echo form_error('inputIntervalDays'); ?>
        </div>
      </div>
      <div class="form-group col-md-3">
        After Interval of Months:
        <div class="input-group form-group">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <input type="checkbox" name="checkIntervalMonths" id="checkIntervalMonths" aria-label="Checkbox for following text input">
            </div>
          </div>
          <input class="form-control" name="inputIntervalMonths" id="inputIntervalMonths" type="number" value="0" aria-label="Text input with checkbox">
          <?php echo form_error('inputIntervalMonths'); ?>
        </div>
      </div>
  
      <div class="form-group col-md-6">
      End Notification Date: <span class="steric">(optional)</span>
        <div class="input-group form-group">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <input type="radio" value="date" name="checkEndType" id="checkEndDate" aria-label="Checkbox for following text input">
            </div>
            <div class="input-group-append">
              <label class="input-group-text" for="inputEndDate"><span class="fa-calendar"></span></label>
            </div>
          </div>
          <input class="form-control" name="inputEndDate" id="inputEndDate" type="text" aria-label="Text input with checkbox" data-datetimepicker="">
        </div>
        
      </div>
      <div class="form-group col-md-6">
      After Notifications Counts: <span class="steric">(optional)</span>
        <div class="input-group form-group">
          <div class="input-group-prepend">
            <div class="input-group-text">
              <input type="radio" value="limit" name="checkEndType" id="checkTotalNotifications" aria-label="Checkbox for following text input">
            </div>
          </div>
          <input class="form-control" name="inputTotalNotifications" id="inputTotalNotifications" value="0" type="number" aria-label="Text input with checkbox">
        </div>
      </div>
      <div class="form-group col-md-12">
    <label for="inputDesc">Description:</label>
    <textarea class="form-control" id="inputDesc" name="inputDesc" rows="3"><?=$this->input->post("inputDesc");?></textarea>
    <?php echo form_error('inputDesc'); ?>
  </div>


  </div><!--form row div-->
  
  </div><!-- form col div-->
    
  </div>

  <button type="submit" class="btn btn-primary">Create New Notification</button>
  <input type="button" class="btn btn-secondary" id="cancelBtn" value="Cancel">
</form>
</div>

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
    jQuery('input#cancelBtn').on('click', function($){
      if(confirm("Are you sure to discard the data?")){
        //alert("this is portal page");
        location.replace("portal");
      }else{
        return false;
      }

    });
    
</script>

<script>
jQuery( "#inputForEntity" ).autocomplete({
  select: function( event, ui ) {console.log(event);},
  source: [ <?php echo implode(",",$aEntity);?> ]
});
</script>
<style>
#formAdd p {
  color: red;
}
</style>