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
          <div class="table-responsive py-3">
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
    <div class="form-group col-md-3 compliance">
      
    <label for="inputComplianceOnly"></label>
        <div class="custom-control custom-checkbox">
          <input class="custom-control-input" type="checkbox" id="inputComplianceOnly" name="inputComplianceOnly" value="1">
          <label class="custom-control-label" for="inputComplianceOnly">Compliance Only?
          </label>
        </div>
    <?php echo form_error('inputComplianceOnly'); ?>
    </div>
    <div class="col-md-3 domestic">
      
    <label for="inputForeign"></label>
        <div class="custom-control custom-checkbox">
          <input class="custom-control-input" type="checkbox" id="inputForeign" name="inputForeign" value="1">
          <label class="custom-control-label" for="inputForeign"> Foreign?
          </label>
        </div>
    <?php echo form_error('inputForeign'); ?>
    </div>
    <div class="form-group col-md-6">
      <label for="inputFillingState">Filling State <span class="steric">*</span></label>
      <select id="inputFillingState" class="form-control" name="inputFillingState">

        <option selected="true" value="">-- Select --</option>
        <option value="AK" >Alaska - AK</option>
        <option value="AL" >Alabama - AL</option>
        <option value="AR" >Arkansas - AR</option>
        <option value="AZ" >Arizona - AZ</option>
        <option value="CA" >California - CA</option>
        <option value="CO" >Colorado - CO</option>
        <option value="CT" >Connecticut - CT</option>
        <option value="DE" >Delaware - DE</option>
        <option value="FL" >Florida - FL</option>
        <option value="GA" >Georgia - GA</option>
        <option value="HI" >Hawaii - HI</option>
        <option value="IA" >Iowa - IA</option>
        <option value="ID" >Idaho - ID</option>
        <option value="IL" >Illinois - IL</option>
        <option value="IN" >Indiana - IN</option>
        <option value="KS" >Kansas - KS</option>
        <option value="KY" >Kentucky - KY</option>
        <option value="LA" >Louisiana - LA</option>
        <option value="MA" >Massachusetts - MA</option>
        <option value="MD" >Maryland - MD</option>
        <option value="ME" >Maine - ME</option>
        <option value="MI" >Michigan - MI</option>
        <option value="MN" >Minnesota - MN</option>
        <option value="MO" >Missouri - MO</option>
        <option value="MS" >Mississippi - MS</option>
        <option value="MT" >Montana - MT</option>
        <option value="NC" >North Carolina - NC</option>
        <option value="ND" >North Dakota - ND</option>
        <option value="NE" >Nebraska - NE</option>
        <option value="NH" >New Hampshire - NH</option>
        <option value="NJ" >New Jersey - NJ</option>
        <option value="NM" >New Mexico - NM</option>
        <option value="NV" >Nevada - NV</option>
        <option value="NY" >New York - NY</option>
        <option value="OH" >Ohio - OH</option>
        <option value="OK" >Oklahoma - OK</option>
        <option value="OR" >Oregon - OR</option>
        <option value="PA" >Pennsylvania - PA</option>
        <option value="RI" >Rhode Island - RI</option>
        <option value="SC" >South Carolina - SC</option>
        <option value="SD" >South Dakota - SD</option>
        <option value="TN" >Tennessee - TN</option>
        <option value="TX" >Texas - TX</option>
        <option value="UT" >Utah - UT</option>
        <option value="VA" >Virginia - VA</option>
        <option value="VT" >Vermont - VT</option>
        <option value="WA" >Washington WA - WA</option>
        <option value="DC" >Washington DC - DC</option>
        <option value="WI" >Wisconsin - WI</option>
        <option value="WV" >West Virginia - WV</option>
        <option value="WY" >Wyoming - WY</option>

      </select>
      <?php echo form_error('inputFillingState'); ?>

    </div>
    <div class="form-group col-md-6">
      <label for="inputFillingStructure">Filling Structure <span class="steric">*</span></label>
      <select id="inputFillingStructure" class="form-control" name="inputFillingStructure">
          <option selected="true" value="">-Select-</option>
          <option value="INC">Corporation</option>
          <option value="CCORP">C Corporation</option>
          <option value="LLC">Limited Liability Corp</option>
          <option value="LP">Limited Partnership</option>
          <option value="LLP">Limited Liability Partnerships</option>
          <option value="LLLP">Limited Liability Limited Partnership</option>
          <option value="NON">Non-Profit Corporation</option>
          <option value="PRO">Professional Corporation</option>
          <option value="PLLC">Professional Limited Liability Company</option>
          <option value="SCORP">S Corporation</option>
      </select>
      <?php echo form_error('inputFillingStructure'); ?>

    </div>
    <div class="form-group col-md-6">
      <label for="inputFormationDate">Formation Date <span class="steric">*</span></label>

        <div class="input-group" id="datetime">
            <div class="input-group-prepend">
              <label class="input-group-text" for="inputFormationDate"><span class="fa-calendar"></span></label>
            </div>
            <input type="text" class="form-control" name="inputFormationDate" id="inputFormationDate" value="<?=$this->input->post("inputFormationDate");?>" placeholder="Formation Date" data-datetimepicker="" />
            <!-- <span class="input-group-addon" for="datetime">
                <span class="input-group-text"><span class="fa fa-calendar"></span></span>
            </span> -->
        </div>
        <?php echo form_error('inputFormationDate'); ?>

    </div>
    <div class="form-group col-md-6">
      <label for="inputFiscalDate">Fiscal Year<span class="steric">*</span></label>
        
        <div class="input-group" id="datetimefiscal">
            <div class="input-group-prepend">
              <label class="input-group-text" for="inputFiscalDate"><span class="fa-calendar"></span></label>
            </div>
            <input type="text" class="form-control" name="inputFiscalDate" id="inputFiscalDate" value="<?=($this->input->post("inputFiscalDate")?:date("12/31/Y"));?>" placeholder="Fiscal Date" data-datetimepicker="" />
            <!-- <span class="input-group-addon" for="datetime">
                <span class="input-group-text"><span class="fa fa-calendar"></span></span>
            </span> -->
        </div>
        <?php echo form_error('inputFiscalDate'); ?>
      
>>>>>>> 40dc85a65bed48a728895c7c6526ddf2ef25a7e5
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
  <div class="form-group col-md-4">
    <label for="inputNotificationContactType">Contact Type </label>
    <select class="form-control" id="inputNotificationContactType" name="inputNotificationContactType">
      <option selected="true" value="">-- Select --</option>
      <option value="CEO">CEO</option>
      <option value="CFO">CFO</option>
      <option value="Director">Director</option>
      <option value="Manager">Manager</option>
      <option value="Member">Member</option>
      <option value="Officer">Officer</option>
      <option value="Partner">Partner</option>
      <option value="President">President</option>
      <option value="Secretary">Secretary</option>
      <option value="Shareholder">Shareholder</option>
      <option value="Tax">Tax</option>
    </select>
  </div>
  <div class="form-group col-md-4">
    <label for="inputNotificationCity">City <span class="steric">*</span></label>
    <input type="text" class="form-control" id="inputNotificationCity" name="inputNotificationCity" placeholder="City" value="<?=$this->input->post("inputNotificationCity");?>">
    <?php echo form_error('inputNotificationCity'); ?>
  </div>
    <div class="form-group col-md-4">
    <label for="inputNotificationState">State/Region/Province <span class="steric">*</span></label>
    <select class="form-control" id="inputNotificationState" name="inputNotificationState">
      <option selected="true" value="">-- Select --</option>
      <option value="AK">Alaska - AK</option>
      <option value="AL">Alabama - AL</option>
      <option value="AZ">Arizona - AZ</option>
      <option value="AR">Arkansas - AR</option>
      <option value="CA">California - CA</option>
      <option value="CO">Colorado - CO</option>
      <option value="CT">Connecticut - CT</option>
      <option value="DE">Delaware - DE</option>
      <option value="FL">Florida - FL</option>
      <option value="GA">Georgia - GA</option>
      <option value="HI">Hawaii - HI</option>
      <option value="ID">Idaho - ID</option>
      <option value="IL">Illinois - IL</option>
      <option value="IN">Indiana - IN</option>
      <option value="IA">Iowa - IA</option>
      <option value="KS">Kansas - KS</option>
      <option value="KY">Kentucky - KY</option>
      <option value="LA">Louisiana - LA</option>
      <option value="ME">Maine - ME</option>
      <option value="MD">Maryland - MD</option>
      <option value="MA">Massachusetts - MA</option>
      <option value="MI">Michigan - MI</option>
      <option value="MN">Minnesota - MN</option>
      <option value="MS">Mississippi - MS</option>
      <option value="MO">Missouri - MO</option>
      <option value="MT">Montana - MT</option>
      <option value="NE">Nebraska - NE</option>
      <option value="NV">Nevada - NV</option>
      <option value="NH">New Hampshire - NH</option>
      <option value="NJ">New Jersey - NJ</option>
      <option value="NM">New Mexico - NM</option>
      <option value="NY">New York - NY</option>
      <option value="NC">North Carolina - NC</option>
      <option value="ND">North Dakota - ND</option>
      <option value="OH">Ohio - OH</option>
      <option value="OK">Oklahoma - OK</option>
      <option value="OR">Oregon - OR</option>
      <option value="PA">Pennsylvania - PA</option>
      <option value="PR">Puerto Rico - PR</option>
      <option value="RI">Rhode Island - RI</option>
      <option value="SC">South Carolina - SC</option>
      <option value="SD">South Dakota - SD</option>
      <option value="TN">Tennessee - TN</option>
      <option value="TX">Texas - TX</option>
      <option value="UT">Utah - UT</option>
      <option value="VT">Vermont - VT</option>
      <option value="VA">Virginia - VA</option>
      <option value="WA">Washington WA - WA</option>
      <option value="DC">Washington DC - DC</option>
      <option value="WV">West Virginia - WV</option>
      <option value="WI">Wisconsin - WI</option>
      <option value="WY">Wyoming - WY</option>
    </select>
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
    <textarea class="form-control" id="inputBusinessPurpose" name="inputBusinessPurpose" rows="3"><?=$this->input->post("inputBusinessPurpose");?></textarea>
    <?php echo form_error('inputBusinessPurpose'); ?>
  </div>



  </div><!--form row div-->

  </div><!-- form col div-->

  </div>




  <button type="submit" class="btn btn-primary">Create New Entity</button>
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

<!-- Modal for popup box -->
<div class="modal_wrapper" id="modalpopup">
	<div class="popup_modal_body">
      <div class="popup_body">
        Are you sure to discard the data?
      </div>
      <div class="btn_area">
        <input type="submit" class="btn btn-secondary" value="Cancel" id="cancel_Btn">
        <input type="submit" class="btn btn-success" value="Ok" id="okBtn">
      </div>
	</div>
</div>
</section>

</section>

<script>
   jQuery(function($){
       $('#inputFillingState').val('<?php print !empty($this->input->post("inputFillingState")) ? $this->input->post("inputFillingState") : '-Select-'; ?>');
       $('#inputFillingStructure').val('<?php print !empty($this->input->post("inputFillingStructure")) ? $this->input->post("inputFillingStructure") : '-Select-' ?>');
       $('#inputNotificationContactType').val('<?php print !empty($this->input->post("inputNotificationContactType")) ? $this->input->post("inputNotificationContactType") : '-Select-' ?>');
       $('#inputNotificationState').val('<?php print !empty($this->input->post("inputNotificationState")) ? $this->input->post("inputNotificationState") : '-Select-' ?>');
    });
    jQuery("input#cancelBtn").on('click', function($){
      jQuery('#modalpopup').fadeIn();
    });
    jQuery("#okBtn").on('click', function($){
      jQuery('#modalpopup').fadeOut();
      location.replace("portal");
    });
    jQuery("#cancel_Btn").on('click', function($){
      jQuery("#modalpopup").fadeOut();
    });
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
