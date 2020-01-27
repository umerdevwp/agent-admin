<div class="container">
    <div class="row">
        <div class="col-md-12">
            <input type="submit" class="btn btn-success create-contacts" value="Create New Contacts">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 new-contactspanel">
            <div class="column">
                <div class="panel">
                    <div class="panel-header">
                       <div class="panel-title">
                           <span class="panel-icon fa-user"></span>Create Contact
                       </div>
                    </div>
                    <div class="panel-body">
                        <form method="POST" id="formAdmin">
                            <div class="row">
                            <div class="field col-md-4 form-group">
                                <label class="label">First Name</label>
                                <div class="control">
                                    <input id="first_name" name="first_name" class="input" type="text" placeholder="Type the First Name">
                                </div>
                            </div>
                            <div class="field col-md-4 form-group">
                                <label class="label">Last Name</label>
                                <div class="control">
                                    <input id="last_name" name="last_name" class="input" type="text" placeholder="Type the Last Name">
                                </div>
                            </div>
                            <div class="field col-md-4 form-group">
                                <label class="label">Email</label>
                                <div class="control">
                                    <input id="email" name="email" class="input" type="email" placeholder="Type the email address">
                                </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="field is-grouped form-group col-md-12">
                                <div class="control">
                                    <button class="button is-link btn btn-primary">Submit</button>
                                </div>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9 col-lg-12">
            
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title"><span class="panel-icon fa-tasks"></span><span><?php print isset($title) ? $title : ''  ?></span>
                    </div>
                </div>
                <div class="panel-body p-0">
                    <div class="table-responsive scroller scroller-horizontal py-3">
                        <div id="DataTables_Table_2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                            <div class="row row-10">
                                <div class="col-sm-12 col-md-6 pl-3"></div>
                                <div class="col-sm-12 col-md-6 pr-3"> </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 attachment-table">
                                    <table class="table table-striped table-hover data-table dataTable" data-page-length="5" data-table-mode="multi-filter" id="DataTables_Table_2_admin" role="grid" aria-describedby="DataTables_Table_2_info">
                                        <thead>
                                            <tr role="row">
                                                <th class="sorting_disabled" data-column-index="0" rowspan="1" colspan="1" style="width: 241.2px;">First Name</th>
                                                <th class="sorting_disabled" data-column-index="1" rowspan="1" colspan="1" style="width: 249.967px;">Last Name</th>
                                                <th class="sorting_disabled" data-column-index="2" rowspan="1" colspan="1" style="width: 241.217px;">Email</th>
                                                <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Last Activity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($admins)) : ?>
                                                <?php foreach ($admins as $admin) : ?>
                                                    <tr id="<?php print $admin->id; ?>" role="row" class="odd">
                                                        <td><?php print $admin->first_name; ?></td>
                                                        <td><?php print $admin->last_name; ?></td>
                                                        <td><?php print $admin->email; ?></td>
                                                        <td><?php print $admin->last_logged_time; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Name"></th>
                                                <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search By"></th>
                                                <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Added"></th>
                                                <th rowspan="1" colspan="1"><input class="form-control form-control-sm multiple-search" type="text" placeholder="Search Size"></th>
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

<script>
    $("#formAdmin").submit(function(event) {
        event.preventDefault();
        jQuery("input").removeClass('error');
        $(".errorMessage").remove();
        $.ajax({
            type: "POST",
            url: "<?= base_url('admin/create') ?>",
            data: $(this).serialize(),
            success: function(response) {
                var returnedData = JSON.parse(response);
                for (var key in returnedData.results) {
                    if (returnedData.results.hasOwnProperty(key)) {
                        $("#" + key).addClass('error');
                        var error = '<span class="errorMessage">' + returnedData.results[key] + '</span>';
                        $("#" + key).after(error);
                    }
                }

                if (returnedData.response == 'success') {
                    returnedData.markup !== '' ?
                        $("#DataTables_Table_2_admin tbody").append(returnedData.markup) :
                        console.log(returnedData.response);
                }
            }
        }); // you have missed this bracket
        return false;
    });
    $("input.create-contacts").click(function(){
        $(".new-contactspanel").toggle('fast');
    });
</script>