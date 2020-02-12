<?php if($this->session->flashdata("error")): ?>
  <div class="alert alert-danger" role="alert">
  <?=$this->session->flashdata("error");?>
</div>
<?php endif; ?>
<section class="topbar"> 
  <!-- Breadcrumbs-->
  <ul class="breadcrumbs">
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal"><span class="breadcrumbs-icon fa-home"></span><span>Dashboard</span></a></li>
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/contacts">Test Notifications Due Dates</a> </li>
    <li class="breadcrumbs-item"><?php echo $entity->entity_name; ?><span style="display:none;"><?php echo $entity->id; ?></style></li>
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
          <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Test Notification:</span> 
        </div>
        </div>

        <div class="panel-body p-0">
          <div class="table-responsive scroller scroller-horizontal py-3">
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper dt-bootstrap4">
            


            <form method="post" action="/notification/cron?XDEBUG_SESSION_START" enctype="multipart/form-data" name="formTestNotification" id="formTestNotification">
            <div class="row">
                  <div class="col-sm-12">

                <div class="form-row">
                    <div class="col-md-6">
          
          <label for="type">Corp. Type</label>
      <select id="type" class="form-control" name="type">
        <option selected="true" value="">-Select-</option>
        <option value="Corporation" >Corporation</option>
        <option value="LLC" >LLC</option>
        <option value="Non-Profit Corporation" >Non-Profit Corporation</option>
        <option value="Limited Partnership" >Limited Partnership</option>
        <option value="LLP" >LLP</option>
      </select>
                    </div>
                    <div class="col-md-6">

                <label for="state">State </label>
                <!-- <input type="text" class="form-control" id="inputContactState" name="inputContactState" placeholder="State/Region/Province" value="" tabindex="7"> -->
                <select class="form-control" id="state" name="state" tabindex="7">
                  <option selected value="">-- Select State --</option>
                  <option value="AL">Alabama - AL</option>
                  <option value="AK">Alaska - AK</option>
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
                  <option value="WA">Washington - WA</option>
                  <option value="DC">Washington DC - DC</option>
                  <option value="WV">West Virginia - WV</option>
                  <option value="WI">Wisconsin - WI</option>
                  <option value="WY">Wyoming - WY</option>
                </select>
                    </div>

                    <div class="form-group col-md-6">

                <label for="inputFormationDate">Formation Date <span class="steric">*</span></label>
        <div class="input-group" id="datetime">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputFormationDate"><span class="fa-calendar"></span></label>
              </div>
              <input type="text" class="form-control" name="formation" id="inputFormationDate" value="<?=$this->input->post("formation");?>" placeholder="Formation Date" data-datetimepicker="" />
              <?php echo form_error('formation'); ?>
        </div>
                    </div>
                    <div class="form-group col-md-6">

        <label for="inputDueDate">Set Today</label>
        <div class="input-group" id="datetime">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputDueDate"><span class="fa-calendar"></span></label>
              </div>
              <input type="text" class="form-control" name="now" id="inputDueDate" value="<?=$this->input->post("now");?>" placeholder="Optional" data-datetimepicker="" />
        </div>
                    </div>

                    <div class="form-group col-md-6">
        <button type="submit" class="btn btn-primary">View Due Date</button>
                    </div>
            </div>

    </div><!-- col end -->
</div><!-- row end -->
</form>
<div class="alert alert-info alert-lighter mt-2" role="alert">
                <span class="alert-icon fa-info"></span>
                <span>Leave <b>Corp. Type</b> and <b>State</b> blank to view all available ruled states.</span>
            </div>
              <div class="row row-10">
                <div class="col-sm-12 col-md-6 pl-3"></div>
                <div class="col-sm-12 col-md-6 pr-3"> </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <table class="table table-striped table-hover data-table dataTable" data-page-length="5" data-table-mode="multi-filter" id="DataTables_Table_3" role="grid" aria-describedby="DataTables_Table_3_info">
                    <thead>
                      <tr role="row">
                        <th class="sorting_disabled" data-column-index="0">Formation</th>
                        <th class="sorting_disabled" data-column-index="1">State</th>
                        <th class="sorting_disabled" data-column-index="2">Type</th>
                        <th class="sorting_disabled" data-column-index="3">Due On</th>
                        <th class="sorting_disabled" data-column-index="3">Period</th>
                        <th class="sorting_disabled" data-column-index="4">Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        if(count($aNotification)>0) {
                            for($i = 0; $i < count($aNotification); $i++){ ?>
                      <tr role="row" class="odd">
                        <td><?php echo $aNotification[$i]->formation; ?></td>
                        <td><?php echo $aNotification[$i]->state; ?></td>
                        <td><?php echo $aNotification[$i]->type; ?></td>
                        <td><?php echo $aNotification[$i]->duedate; ?></td>
                        <td><?php echo $aNotification[$i]->period; ?></td>
                        <td><?php echo $aNotification[$i]->description; ?></td>
                      </tr>
                      <?php } 
                        } ?>
                    </tbody>
                  </table>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      </div></div></div>
      
      <script>
             jQuery(function($){
       $('#state').val('<?php print !empty($this->input->post("state")) ? $this->input->post("state") : '-Select-'; ?>');
       $('#type').val('<?php print !empty($this->input->post("type")) ? $this->input->post("type") : '-Select-' ?>');
             });
      </script>