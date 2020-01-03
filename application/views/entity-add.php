<?php if($this->session->flashdata("error")): ?>
  <div class="alert alert-danger" role="alert">
  <?=$this->session->flashdata("error");?>
</div>
<?php endif; ?>
<section class="topbar"> 
  <!-- Breadcrumbs-->
  <ul class="breadcrumbs">
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal"><span class="breadcrumbs-icon fa-home"></span><span>Dashboard</span></a></li>
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal">Add New Entity</a> </li>
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
        <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>New RA Client</span> </div>
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
                  
                <form method="post" action="/entity/add" enctype="multipart/form-data" name="formAdd" id="formAdd">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputName">Entity Name <span class="steric">*</span></label>
      <input type="text" class="form-control"  name="inputName" id="inputName" value="<?=$this->input->post("inputName");?>" placeholder="Entity Name">
      <?php echo form_error('inputName'); ?>
    </div>
    <div class="form-group col-md-6 compliance">
    <label for="inputComplianceOnly">Compliance Only:</label>
    <input type="checkbox" class="form-control" id="inputComplianceOnly" name="inputComplianceOnly" value="1" <?=($this->input->post("inputComplianceOnly")?'checked':'');?>>
    <?php echo form_error('inputComplianceOnly'); ?>
    </div>
    <div class="form-group col-md-4">
      <label for="inputFillingState">Filling State <span class="steric">*</span></label>
      <select onchange="handleChange()" id="inputFillingState" class="form-control" name="inputFillingState">

        <option selected="true" value="-Select-">-Select-</option>
        <option value="AK" formula_val="">AK</option>
        <option value="AL" formula_val="">AL</option>
        <option value="AR" formula_val="">AR</option>
        <option value="AZ" formula_val="">AZ</option>
        <option value="CA" formula_val="">CA</option>
        <option value="CO" formula_val="">CO</option>
        <option value="CT" formula_val="">CT</option>
        <option value="DE" formula_val="">DE</option>
        <option value="FL" formula_val="">FL</option>
        <option value="GA" formula_val="">GA</option>
        <option value="HI" formula_val="">HI</option>
        <option value="IA" formula_val="">IA</option>
        <option value="ID" formula_val="">ID</option>
        <option value="IL" formula_val="">IL</option>
        <option value="IN" formula_val="">IN</option>
        <option value="KS" formula_val="">KS</option>
        <option value="KY" formula_val="">KY</option>
        <option value="LA" formula_val="">LA</option>
        <option value="MA" formula_val="">MA</option>
        <option value="MD" formula_val="">MD</option>
        <option value="ME" formula_val="">ME</option>
        <option value="MI" formula_val="">MI</option>
        <option value="MN" formula_val="">MN</option>
        <option value="MO" formula_val="">MO</option>
        <option value="MS" formula_val="">MS</option>
        <option value="MT" formula_val="">MT</option>
        <option value="NC" formula_val="">NC</option>
        <option value="ND" formula_val="">ND</option>
        <option value="NE" formula_val="">NE</option>
        <option value="NH" formula_val="">NH</option>
        <option value="NJ" formula_val="">NJ</option>
        <option value="NM" formula_val="">NM</option>
        <option value="NV" formula_val="">NV</option>
        <option value="NY" formula_val="">NY</option>
        <option value="OH" formula_val="">OH</option>
        <option value="OK" formula_val="">OK</option>
        <option value="OR" formula_val="">OR</option>
        <option value="PA" formula_val="">PA</option>
        <option value="RI" formula_val="">RI</option>
        <option value="SC" formula_val="">SC</option>
        <option value="SD" formula_val="">SD</option>
        <option value="TN" formula_val="">TN</option>
        <option value="TX" formula_val="">TX</option>
        <option value="UT" formula_val="">UT</option>
        <option value="VA" formula_val="">VA</option>
        <option value="VT" formula_val="">VT</option>
        <option value="WA" formula_val="">WA</option>
        <option value="WI" formula_val="">WI</option>
        <option value="WV" formula_val="">WV</option>
        <option value="WY" formula_val="">WY</option>

      </select>
      <?php echo form_error('inputFillingState'); ?>
      
    </div>
    <div class="form-group col-md-4">
      <label for="inputFillingStructure">Filling Structure <span class="steric">*</span></label>
      <select onchange='handleChange_Filling_Structure()' id="inputFillingStructure" class="form-control" name="inputFillingStructure">
        <option selected="true" value="-Select-">-Select-</option>
        <option value="Corporation" formula_val="">Corporation</option>
        <option value="LLC" formula_val="">LLC</option>
        <option value="Non-Profit Corporation" formula_val="">Non-Profit Corporation</option>
        <option value="Limited Partnership" formula_val="">Limited Partnership</option>
        <option value="LLP" formula_val="">LLP</option>
      </select>
      <?php echo form_error('inputFillingStructure'); ?>
      
    </div>
    <div class="form-group col-md-4">
      <label for="inputFormationDate">Formation Date <span class="steric">*</span></label>
        
        <div class="input-group" id="datetime">
            <input type="text" class="form-control" name="inputFormationDate" id="inputFormationDate" value="<?=$this->input->post("inputFormationDate");?>" placeholder="Formation Date" />
            <!-- <span class="input-group-addon" for="datetime">
                <span class="input-group-text"><span class="fa fa-calendar"></span></span>
            </span> -->
        </div>
        <?php echo form_error('inputFormationDate'); ?>
      
    </div>
    <div class="form-group col-md-6">
      <label for="inputFirstName">First Name <span class="steric">*</span></label>
      <input type="text" class="form-control" id="inputFirstName" name="inputFirstName" placeholder="First Name" value="<?=$this->input->post("inputFirstName");?>">
      <?php echo form_error('inputFirstName'); ?>
    </div>
	<div class="form-group col-md-6">
      <label for="inputLastName">Last Name <span class="steric">*</span></label>
      <input type="text" class="form-control" id="inputLastName" name="inputLastName" placeholder="Last Name" value="<?=$this->input->post("inputLastName");?>">
      <?php echo form_error('inputLastName'); ?>
    </div>
	<div class="form-group col-md-6">
      <label for="inputNotificationEmail">Notification Email <span class="steric">*</span></label>
      <input type="email" class="form-control" id="inputNotificationEmail" name="inputNotificationEmail" placeholder="Notification Email" value="<?=$this->input->post("inputNotificationEmail");?>">
      <?php echo form_error('inputNotificationEmail'); ?>
    </div>
	<div class="form-group col-md-6">
    <label for="inputNotificationPhone">Notification Phone <span class="steric">*</span></label>
    <input type="text" class="form-control" id="inputNotificationPhone" name="inputNotificationPhone" placeholder="Phone Number" value="<?=$this->input->post("inputNotificationPhone");?>">
    <?php echo form_error('inputNotificationPhone'); ?>
  </div>
  <div class="form-group col-md-12">
    <label for="inputNotificationAddress">Notification Address <span class="steric">*</span></label>
    <input type="text" class="form-control" id="inputNotificationAddress" name="inputNotificationAddress" placeholder="Street Address" value="<?=$this->input->post("inputNotificationAddress");?>">
    <?php echo form_error('inputNotificationAddress'); ?>
  </div>
  <div class="form-group col-md-6">
    <label for="inputNotificationCity">City <span class="steric">*</span></label>
    <input type="text" class="form-control" id="inputNotificationCity" name="inputNotificationCity" placeholder="City" value="<?=$this->input->post("inputNotificationCity");?>">
    <?php echo form_error('inputNotificationCity'); ?>
  </div>
    <div class="form-group col-md-6">
    <label for="inputNotificationState">State/Region/Province <span class="steric">*</span></label>
    <input type="text" class="form-control" id="inputNotificationState" name="inputNotificationState" placeholder="State/Region/Province" value="<?=$this->input->post("inputNotificationState");?>">
    <?php echo form_error('inputNotificationState'); ?>
  </div>
  <div class="form-group col-md-6">
    <label for="inputNotificationZip">Postal / Zip Code <span class="steric">*</span></label>
    <input type="text" class="form-control" id="inputNotificationZip" name="inputNotificationZip" placeholder="Postal / Zip Code" value="<?=$this->input->post("inputNotificationZip");?>">
    <?php echo form_error('inputNotificationZip'); ?>
  </div>
  
  <div class="input-group col-md-6 form-group">
    <label class="attachment">Attachment <span class="steric">*</span></label>
    <!-- <div class="input-group-prepend">
      <span class="input-group-text">Filing</span>
    </div> -->
    <div class="custom-file">
      <input type="file" class="custom-file-input" id="inputFiling" name="inputFiling">
      <label class="custom-file-label" for="inputFiling">Choose file</label>
    </div>
    <?php echo form_error('inputFiling'); ?>
   
  </div>
  <div class="form-group col-md-12">
    <label for="inputBusinessPurpose">Business Purpose</label>
    <?php echo form_error('inputBusinessPurpose'); ?>
    <textarea class="form-control" id="inputBusinessPurpose" name="inputBusinessPurpose" rows="3"><?=$this->input->post("inputBusinessPurpose");?></textarea>
  </div>
  


  </div><!--form row div-->
  
  </div><!-- form col div-->
    
  </div>
  
  


  <button type="submit" class="btn btn-primary">Create New Entity</button>
</form>

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
window.onload = setValue_inputFillingState;
function setValue_inputFillingState() {
  if (window.location.href.indexOf("entity") > -1 && window.location.href.indexOf("add") > -1) {
    localStorage.getItem("filling_state_selection") !== null ? 
    document.getElementById('inputFillingState').value = localStorage.getItem("filling_state_selection") : '';
    localStorage.getItem("filling_structure_selection") !== null ? 
    document.getElementById('inputFillingStructure').value = localStorage.getItem("filling_structure_selection") : '';
  }
}
handleChange = () => {
  var x = document.getElementById("inputFillingState").value;
  localStorage.setItem("filling_state_selection", x);
}
handleChange_Filling_Structure = () => {
  var x = document.getElementById("inputFillingStructure").value;
  localStorage.setItem("filling_structure_selection", x);
}
</script>
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

        //jQuery('#datepicker').datetimepicker();
}, 5000);
  

</script>
<style>
#formAdd p {
  color: red;
}
</style>