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
          <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Notification Calendar:</span> 
        </div>
        </div>

        <div class="panel-body p-0">
          <div class="table-responsive scroller scroller-horizontal py-3">
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper dt-bootstrap4">
            


            <form method="post" action="/notification/calendar" enctype="multipart/form-data" name="formTestNotification" id="formTestNotification">
            <div class="row">
                  <div class="col-sm-12">

                <div class="form-row">
                    <div class="col-md-3">
          
          <label for="type">Corp. Type <span class="steric">*</span></label>
      <select id="type" class="form-control" name="type">
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
      <?php echo form_error('type'); ?>
                    </div>
                    <div class="col-md-3">

                <label for="state">State <span class="steric">*</span></label>
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
                <?php echo form_error('state'); ?>
                    </div>

                    <div class="col-md-3">

                <label for="inputFormationDate">Formation Date <span class="steric">*</span></label>
        <div class="input-group" id="datetime">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputFormationDate"><span class="fa-calendar"></span></label>
              </div>
              <input type="text" class="form-control" name="formation" id="inputFormationDate" value="<?=$this->input->post("formation");?>" placeholder="Formation Date" data-datetimepicker="" />
              <?php echo form_error('formation'); ?>
        </div>
                    </div>
                    
                    <div class="col-md-3">

        <label for="inputFiscal">Fiscal Date <span class="steric">*</span></label>
        <div class="input-group" id="datetime2">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputFiscal"><span class="fa-calendar"></span></label>
              </div>
              <input type="text" class="form-control" name="fiscal" id="inputFiscal" value="<?=$this->input->post("fiscal");?>" placeholder="Fiscal Date" data-datetimepicker="" />
              <?php echo form_error('fiscal'); ?>
        </div>
                    </div>


                   

                    <div class="form-group col-md-3">

                <label for="inputintervaldays">Interval Days <span class="steric">*</span></label>
        <div class="input-group">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputintervaldays"><span class="fa-signal"></span></label>
              </div>
              <input type="number" class="form-control ui-spinner-input" name="intervaldays" id="intervaldays" value="<?=$this->input->post("intervaldays")?:1;?>" placeholder="Interval Days" value="5" aria-valuemin="0" aria-valuenow="8" autocomplete="off" role="spinbutton" />
              <?php echo form_error('intervaldays'); ?>
        </div>
                    </div>

                    <div class="form-group col-md-3">

                <label for="inputintervalmonths">Interval Months <span class="steric">*</span></label>
        <div class="input-group">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputintervalmonths"><span class="fa-signal"></span></label>
              </div>
              <input type="number" class="form-control ui-spinner-input" name="intervalmonths" id="intervalmonths" value="<?=$this->input->post("intervalmonths")?:1;?>" placeholder="Before Days" value="5" aria-valuemin="0" aria-valuenow="8" autocomplete="off" role="spinbutton" />
              <?php echo form_error('intervalmonths'); ?>
        </div>
                    </div>

                     <div class="form-group col-md-3">

                <label for="inputbeforedays">Before Days <span class="steric">*</span></label>
        <div class="input-group">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputbeforedays"><span class="fa-signal"></span></label>
              </div>
              <input type="number" class="form-control ui-spinner-input" name="beforedays" id="beforedays" value="<?=$this->input->post("beforedays")?:1;?>" placeholder="Before Days" value="5" aria-valuemin="0" aria-valuenow="8" autocomplete="off" role="spinbutton" />
              <?php echo form_error('beforedays'); ?>
        </div>
                    </div>

                    <div class="form-group col-md-3">

                <label for="inputbeforemonths">Before Months <span class="steric">*</span></label>
        <div class="input-group">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputbeforemonths"><span class="fa-signal"></span></label>
              </div>
              <input type="number" class="form-control ui-spinner-input" name="beforemonths" id="beforemonths" value="<?=$this->input->post("beforemonths")?:1;?>" placeholder="Before Months" value="5" aria-valuemin="0" aria-valuenow="8" autocomplete="off" role="spinbutton" />
              <?php echo form_error('beforemonths'); ?>
        </div>
                    </div>

                    


                    
                <div class="form-group col-md-12">
                    <label for="daterange">Build Calendar</label>
                      <div class="input-group">
                        <input class="form-control" id="daterange" type="text" name="daterange"  value="<?=$this->input->post("daterange");?>">
                        <div class="input-group-append">
                          <label class="input-group-text" for="daterange"><span class="fa fa-calendar"></span></label>
                        </div>
                      </div>

                    </div>

                    

                    <div class="form-group col-md-6">
        <button type="submit" class="btn btn-primary">View Due Date</button>
                    </div>
            </div>

    </div><!-- col end -->
</div><!-- row end -->
</form>

              <div class="row row-10">
                <div class="col-sm-12 col-md-6 pl-3"></div>
                <div class="col-sm-12 col-md-6 pr-3"> </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                <div class="tabs tabs-vertical-top tabs-border">
                <ul class="nav nav-tabs justify-content-md-end scroller scroller-horizontal" role="tablist">
                  <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#panelTab5-1" role="tab" aria-selected="true"><span class="fa-bolt"></span>Calendar</a></li>
                  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#panelTab5-2" role="tab" aria-selected="false">Table</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="panelTab5-1" role="tabpanel">
                  
                  <div class="fullcalendar" data-fullcalendar-event='<?=$sCalendarEvents;?>'></div>
                  </div>
                  <div class="tab-pane fade" id="panelTab5-2" role="tabpanel">
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
                        <?php 
                            } 
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