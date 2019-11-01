<section class="topbar"> 
  <!-- Breadcrumbs-->
  <ul class="breadcrumbs">
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal"><span class="breadcrumbs-icon fa-home"></span><span>Dashboard</span></a></li>
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="/portal">Entities</a> </li>
    <li class="breadcrumbs-item"><?php echo $account->AccountData->getFieldValue('Account_Name'); ?></li>
  </ul>
</section>
	
	
<section class="Jumbotron"><!--Company Summary Info-->
<div class="container">
	<div class="row">
	<div class="col"> 
	<h2><?php echo $account->AccountData->getFieldValue('Account_Name'); ?><br>
      <span class="badge badge-primary"><?php echo $account->AccountData->getFieldValue('Entity_Type'); ?></span> <span class="badge badge-secondary"><?php echo $account->AccountData->getFieldValue('Filing_State'); ?></span></h2>
		</div></div>
	<div class="row">
		<div class="col-md-3">
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><span class="panel-icon fa-map-marker"></span><span>Forwarding Address</span></div>
          </div>
          <div class="panel-body">
            <address>
                <?php echo $account->AccountData->getFieldValue('Billing_Street'); ?><br>
                <?php echo $account->AccountData->getFieldValue('Billing_City'); ?>, <?php echo $account->AccountData->getFieldValue('Billing_State'); ?> <?php echo $account->AccountData->getFieldValue('Billing_Code'); ?><br>
            <abbr title="Phone">E:</abbr> <?php echo $account->AccountData->getFieldValue('Email'); ?>
            </address>
          </div>
        </div>
      </div>
		<div class="col-md-3">
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><span class="panel-icon fa-map-marker"></span><span>Registered Address </span><span class="badge badge-success">Active</span> </div>
          </div>
          <div class="panel-body">
            <address>
            <strong>United Agent Services LLC</strong><br>
            2 East Congress<br>
            Suite 900-126<br>
            Tucson, AZ 85701
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
              <dt>Account Number</dt>
              <dd><?php echo $account->AccountData->getFieldValue('Account_Number'); ?></dd>
            </dl>
            <dl class="dl-horizontal">
              <dt>Formation Date</dt>
              <dd><?php echo $account->AccountData->getFieldValue('Formation_Date'); ?></dd>
            </dl>
            <dl class="dl-horizontal">
              <dt>Expiration Date</dt>
              <dd><?php echo $account->AccountData->getFieldValue('Expiration_Date'); ?></dd>
            </dl>
			  <dl class="dl-horizontal"><dt>Tax Id</dt><dd><?php echo $account->AccountData->getFieldValue('EIN'); ?></dd></dl>
          </div>
        </div>
      </div>
	<div class="row">
        <div class="col-md-9 col-lg-12">
          <div class="panel">
            <div class="panel-header">
              <div class="panel-title">Compliance Check List <span class="span badge badge-danger">1</span></div>
            </div>
          <div class="alert alert-danger alert-darker alert-lg"><span class="alert-icon fa-remove"></span><span>WARNING! Open Compliance Tasks May Be Past Due</span></div>
           <div class="panel-body">
              <h3 class="list-sortable-title">Compliance Tasks</h3>
              <ul class="list-sortable sortable sortable-current" data-connect-group=".sortable-completed">
                <li class="list-sortable-item-primary">
                  <div class="custom-control custom-checkbox custom-check custom-checkbox-primary">
                    <input class="custom-control-input" type="checkbox" id="taskCheck1"/>
                    <label class="custom-control-label" for="taskCheck1"><b>June 16, 2018: Annual Report Notification - AZ</b><br>
                      The annual report filing is due yearly by the entity's formation date. Specific instruction can be found on the <a href="http:\\ecorp.azcc.fov/Entity#acc-annual-report-detail-container1.">state's website</a>. You can also contact the commission at (602) 542-3285. </label>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
	</div>
	
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
                        <th class="sorting_disabled" data-column-index="5" rowspan="1" colspan="1" style="width: 241.2px;">Edit</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        if($account->Contacts) {
                            for($i = 0; $i < count($account->Contacts); $i++){ ?>
                      <tr role="row" class="odd">
                        <td><?php echo $account->Contacts[$i]->getFieldValue('Full_Name'); ?></td>
                        <td><?php echo $account->Contacts[$i]->getFieldValue('Title'); ?></td>
                        <td><?php echo $account->Contacts[$i]->getFieldValue('Email'); ?></td>
                        <td><?php echo $account->Contacts[$i]->getFieldValue('Mailing_Street'); ?>, <?php echo $account->Contacts[$i]->getFieldValue('Mailing_City'); ?> <?php echo $account->Contacts[$i]->getFieldValue('Mailing_State'); ?> <?php echo $account->Contacts[$i]->getFieldValue('Mailing_Zip'); ?></td>
                        <td><?php echo $account->Contacts[$i]->getFieldValue('Phone'); ?></td>
                        <td><span class="panel-icon fa-pencil"></span></td>
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
</section>