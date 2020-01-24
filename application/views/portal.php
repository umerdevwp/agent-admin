<?php if (isset($_SESSION['adminPermission'])) : ?>
  <div class="alert alert-danger" role="alert">
    <?= $_SESSION['adminPermission']; ?>
  </div>
<?php endif; ?>

<?php if ($this->session->flashdata("error")) : ?>
  <div class="alert alert-danger" role="alert">
    <?= $this->session->flashdata("error"); ?>
  </div>
<?php endif; ?>
<?php if ($this->session->flashdata("ok")) : ?>
  <div class="entitysuccessparent">
    <div class="alert alert-success success" role="alert">
      <?= $this->session->flashdata("ok"); ?>
    </div>
  </div>
<?php endif; ?>
<section class="topbar">
  <!-- Breadcrumbs-->
  <ul class="breadcrumbs">
    <li class="breadcrumbs-item"><a class="breadcrumbs-link" href="portal"><span class="breadcrumbs-icon fa-home"></span><span>Dashboard</span></a></li>
    <li class="breadcrumbs-item"><?php echo $entity->entity_name; ?><span style='display:none;' id="tempId"><?= $this->session->user["zohoId"]; ?></span></li>

  </ul>
  <!--<h2>Add Entity</h2>-->
</section>
	
	
<section class="Jumbotron"><!--Company Summary Info-->
<div class="container">
<div class="row">
  <div class="col"></div>
</div>
<div class="row">
	<div class="col-md-12">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title"><span class="panel-icon fa-tasks"></span><span>Entities</span> </div>
        </div>
        <div class="panel-body p-0">
          <div class="table-responsive scroller scroller-horizontal py-3">
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper dt-bootstrap4">
              <div class="row row-10">
                <div class="col-sm-12 col-md-6 pl-3"></div>
                <div class="col-sm-12 col-md-6 pr-3"> </div>
              </div>
              <div class="row">
                <div class="col-sm-12 entities-data">
                  <table class="table table-striped table-hover data-table dataTable" data-page-length="5"  id="DataTables_Table_3" role="grid" data-table-searching="true" aria-describedby="DataTables_Table_3_info">
                    <thead>
                      <tr role="row">
                        <th class="sorting_disabled" data-column-index="0" rowspan="1" colspan="1" style="width: 241.2px;">Name</th>
                        <th class="sorting_disabled" data-column-index="1" rowspan="1" colspan="1" style="width: 249.967px;">Entity Structure</th>
                        <th class="sorting_disabled" data-column-index="2" rowspan="1" colspan="1" style="width: 241.217px;">Filing State</th>
                        <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Formation Date</th>
                        <!-- <th class="sorting_disabled" data-column-index="4" rowspan="1" colspan="1" style="width: 241.217px;">Expiration Date</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php for($i = 0; $i < count($arChildEntity); $i++) { ?>
                          <tr role="row" class="odd" onclick="window.location = '/entity/<?php echo $arChildEntity[$i]->id; ?>';">
                            <td><?php echo $arChildEntity[$i]->entity_name ?></td>
                            <td><?php echo $arChildEntity[$i]->entity_structure; ?></td>
                            <td><?php echo $arChildEntity[$i]->filing_state; ?></td>
                            <td><?php echo $arChildEntity[$i]->formation_date; ?></td>
                            <!-- <td><?php echo $arChildEntity[$i]->expiration_date; ?></td> -->
                          </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Name"></th>
                          <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Type"></th>
                          <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search State"></th>
                          <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Seach Date"></th>
                          <!-- <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Exp Date"></th>  -->
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
  </div>
</section>
<script>
  function callMessagesApi() {
    var accessToken = oktaSignIn.tokenManager.get("accessToken");

    if (!accessToken) {
      return;
    }

    // Make the request using jQuery
    $.ajax({
      url: 'http://localhost:{serverPort}/api/messages',
      headers: {
        Authorization: 'Bearer ' + accessToken.accessToken
      },
      success: function(response) {
        // Received messages!
        console.log('Messages', response);
      },
      error: function(response) {
        console.error(response);
      }
    });
  }
</script>