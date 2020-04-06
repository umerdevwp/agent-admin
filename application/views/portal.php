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
<<<<<<< HEAD
    <li class="breadcrumbs-item"><?php echo $entity->entity_name; ?><span style='display:none;' id="tempId"><?= $this->session->user["zohoId"]; ?></span></li>

  </ul>
  <!--<h2>Add Entity</h2>-->
</section>


<section class="Jumbotron">
  <!--Company Summary Info-->
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
                    <table class="table table-striped table-hover data-table dataTable" data-page-length="5" id="DataTables_Table_3" role="grid" data-table-searching="true" aria-describedby="DataTables_Table_3_info">
                      <thead>
                        <tr role="row">
                          <th class="sorting_disabled" data-column-index="0" rowspan="1" colspan="1" style="width: 241.2px;">Name</th>
                          <th class="sorting_disabled" data-column-index="1" rowspan="1" colspan="1" style="width: 249.967px;">Entity Structure</th>
                          <th class="sorting_disabled" data-column-index="2" rowspan="1" colspan="1" style="width: 241.217px;">Filing State</th>
                          <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Formation Date</th>
                          <?php if ($this->session->user["isAdmin"]) : ?>
                            <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Status</th>
                            <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Actions</th>
                          <?php endif; ?>
                          <!-- <th class="sorting_disabled" data-column-index="4" rowspan="1" colspan="1" style="width: 241.217px;">Expiration Date</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i = 0; $i < count($arChildEntity); $i++) : ?>
                          <tr role="row" class="odd" id="row_<?= $arChildEntity[$i]->id ?>">
                            <td onclick="window.location = '/entity/<?php echo $arChildEntity[$i]->id; ?>';"><?php echo $arChildEntity[$i]->entity_name ?></td>
                            <td onclick="window.location = '/entity/<?php echo $arChildEntity[$i]->id; ?>';"><?php echo $arChildEntity[$i]->entity_structure; ?></td>
                            <td onclick="window.location = '/entity/<?php echo $arChildEntity[$i]->id; ?>';"><?php echo $arChildEntity[$i]->filing_state; ?></td>
                            <td onclick="window.location = '/entity/<?php echo $arChildEntity[$i]->id; ?>';"><?php echo $arChildEntity[$i]->formation_date; ?></td>
                            <?php if ($this->session->user["isAdmin"]) : ?>
                              <td data-name="status" class="editable"><?= !empty($arChildEntity[$i]->entity_status) ? $arChildEntity[$i]->entity_status : $arChildEntity[$i]->status; ?></td>
                              <td>
                                <button class="edit_<?= $arChildEntity[$i]->id; ?> edit" onclick="updateHandler('<?= $arChildEntity[$i]->id; ?>');" data-toggle="tooltip" title="Edit"></button>
                                <button style="display: none" class="update_<?= $arChildEntity[$i]->id; ?> update" onclick="submitHandler('<?= $arChildEntity[$i]->id; ?>');" data-toggle="tooltip" title="Update"></button>
                                <button style="display: none" class="reset_<?= $arChildEntity[$i]->id; ?> reset" onclick="resetHandler('<?= $arChildEntity[$i]->id; ?>');" data-toggle="tooltip" title="Reset"></button>
                              </td>

                            <?php endif; ?>
                            <!-- <td><?php echo $arChildEntity[$i]->expiration_date; ?></td> -->
=======
    <li class="breadcrumbs-item"><?php echo $entity->account_name; ?><span style='display:none;' id="tempId"><?=$this->session->user["zohoId"];?></span></li>
    
  </ul>
  <!--<h2>Add Entity</h2>-->
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
                            <td><?php echo $arChildEntity[$i]->account_name; ?></td>
                            <td><?php echo $arChildEntity[$i]->entity_type; ?></td>
                            <td><?php echo $arChildEntity[$i]->filing_state; ?></td>
                            <td><?php echo $arChildEntity[$i]->formation_date; ?></td>
                           <!-- <td><?php echo $arChildEntity[$i]->expiration_date; ?></td> -->
>>>>>>> 40dc85a65bed48a728895c7c6526ddf2ef25a7e5
                          </tr>
                        <?php endfor; ?>
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
  updateHandler = (id) => {
    $('button.edit_' + id).css('display', 'none');
    $('button.update_' + id).css('display', 'inherit');
    $('button.reset_' + id).css('display', 'inherit');
    var perviousData = {};
    $('tr#row_' + id).find('td.editable').each(function() {
      var html = $(this).html();
      perviousData[$(this).data('name')] = html
      //  var input = $('<input id="' + $(this).data('name') + '_' + id + '" name="' + $(this).data('name') + '" class="editableColumnsStyle" type="text" />');
      var input = $('<select id="' + $(this).data('name') + '_' + id + '" name="' + $(this).data('name') + '" ></select>');
      <?php foreach ($formStatus['results'] as $status) : ?>
        input.append(new Option('<?= $status->status ?>', '<?= $status->status ?>'));
      <?php endforeach; ?>
      input.val(html);
      $(this).html(input);
<<<<<<< HEAD

    });
    localStorage.setItem(id, JSON.stringify(perviousData));

  }

=======

    });
    localStorage.setItem(id, JSON.stringify(perviousData));

  }

>>>>>>> 40dc85a65bed48a728895c7c6526ddf2ef25a7e5
  submitHandler = (id) => {
    $('tr#' + id + ' > input').removeClass('error');
        $('tr#' + id + ' > .tableErrorMessage').remove();
        var $tr = $('#row_' + id);
        var data = {},
            name, value;
        var datas = $tr.find(':input, select').serialize();
        datas += '&id=' + id;
        $.ajax({
            type: "POST",
            url: "<?= base_url('portal/addStatusForEntity'); ?>",
            data: datas,
            success: function(response) {
                var returnedData = JSON.parse(response);
                if (returnedData.results !== undefined) {
                    for (var key in returnedData.results) {
                        if (returnedData.results.hasOwnProperty(key)) {
                            $("#" + key + '_' + id).addClass('error');
                            var error = '<span class="tableErrorMessage">' + returnedData.results[key] + '</span>';
                            $("#" + key + '_' + id).after(error);
                        }
                    }
                }
                if (returnedData.response == 'success') {
                    $tr.find('td.editable').each(function() {
                        var $td = $(this);
                        value = $td.find('select option:selected').val();
                        name = $td.data('name');
                        $td.html(value);
                    });
                    $('button.update_' + id).css('display', 'none');
                    $('button.edit_' + id).css('display', 'inherit');
                    $('button.reset_' + id).css('display', 'none');
                }
            }
        }); // you have missed this bracket
<<<<<<< HEAD
  }

  resetHandler = (id) => {
    var parsedData = localStorage.getItem(id);
        var result = JSON.parse(parsedData);
        var $tr = $('#row_' + id);
        $tr.find('td.editable').each(function() {
            var $td = $(this);
            var value = $td.find('select option:selected').val();
            name = $td.data('name');
            $td.html(result[name]);

        });
        $('button.edit_' + id).css('display', 'inherit');
        $('button.update_' + id).css('display', 'none');
        $('button.reset_' + id).css('display', 'none');
         localStorage.removeItem(id);
  }

=======
  }

  resetHandler = (id) => {
    var parsedData = localStorage.getItem(id);
        var result = JSON.parse(parsedData);
        var $tr = $('#row_' + id);
        $tr.find('td.editable').each(function() {
            var $td = $(this);
            var value = $td.find('select option:selected').val();
            name = $td.data('name');
            $td.html(result[name]);

        });
        $('button.edit_' + id).css('display', 'inherit');
        $('button.update_' + id).css('display', 'none');
        $('button.reset_' + id).css('display', 'none');
         localStorage.removeItem(id);
  }

>>>>>>> 40dc85a65bed48a728895c7c6526ddf2ef25a7e5

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