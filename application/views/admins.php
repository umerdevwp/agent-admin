<div class="successmsg-wrapper" id="success-wrapper">
    <div class="successMesgBox alert alert-success">Added successfully</div>
</div>
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
                            <span class="panel-icon fa-user"></span>Add New Admin
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
                                        <div class="input-group form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">@</span>
                                            </div>
                                            <input id="email" name="email" class="input form-group" type="email" placeholder="Type the email address">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="field is-grouped form-group col-md-12">
                                    <div class="control">
                                        <button class="button is-link btn btn-primary">Submit</button>
                                    </div>
                                    <div class="loader" id="adminLoader"><img src="images/loader.svg" alt=""></div>
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
                                    <table class="table table-striped table-hover data-table dataTable" data-page-length="5" id="DataTables_Table_2_admin" role="grid" aria-describedby="DataTables_Table_2_info">
                                        <thead>
                                            <tr role="row">
                                                <th class="sorting_disabled" data-column-index="0" rowspan="1" colspan="1" style="width: 241.2px;">First Name</th>
                                                <th class="sorting_disabled" data-column-index="1" rowspan="1" colspan="1" style="width: 249.967px;">Last Name</th>
                                                <th class="sorting_disabled" data-column-index="2" rowspan="1" colspan="1" style="width: 241.217px;">Email</th>
                                                <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Last Activity</th>
                                                <th class="sorting_disabled" data-column-index="3" rowspan="1" colspan="1" style="width: 241.2px;">Actions</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($admins)) : ?>
                                                <?php foreach ($admins as $admin) : ?>
                                                    <tr id="row_<?php print $admin->id; ?>" role="row" class="odd">
                                                        <td data-name="first_name" class="editable"><?php print $admin->first_name; ?></td>
                                                        <td data-name="last_name" class="editable"><?php print $admin->last_name; ?></td>
                                                        <td><?php print $admin->email; ?></td>
                                                        <td><?php print $admin->last_logged_time; ?></td>
                                                        <td>
                                                            <button style="display: none" class="update_<?php print $admin->id; ?> btn btn-success update" onclick="submitHandler('<?php print $admin->id; ?>');">Update</button>
                                                            <button class="edit_<?php print $admin->id; ?> btn btn-primary edit" onclick="updateHandler('<?php print $admin->id; ?>');">Edit</button>
                                                            <button style="display: none" class="reset_<?php print $admin->id; ?> btn btn-secondary reset" onclick="resetHandler('<?php print $admin->id; ?>');">Reset</button>
                                                            <button onclick="deleteHandler('<?php print $admin->id; ?>')" class="btn btn-danger delete">Delete</button>
                                                        </td>
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
    function updateHandler(id) {
        $('button.edit_' + id).css('display', 'none');
        $('button.update_' + id).css('display', 'inherit');
        $('button.reset_' + id).css('display', 'inherit');

        var perviousData = {};

        $('tr#row_' + id).find('td.editable').each(function() {
            var html = $(this).html();
            perviousData[$(this).data('name')] = html
            var input = $('<input id="' + $(this).data('name') + '_' + id + '" name="' + $(this).data('name') + '" class="editableColumnsStyle" type="text" />');
            input.val(html);
            $(this).html(input);

        });
        localStorage.setItem(id, JSON.stringify(perviousData));
    }

    function submitHandler(id) {
        $('tr#' + id +' td input').removeClass('error');
        $('tr#' + id +' td .tableErrorMessage').remove();
        var $tr = $('#row_' + id);
        var data = {},
            name, value;
        var datas = $tr.find(':input, select').serialize();
        datas += '&id=' + id;
        $.ajax({
            type: "POST",
            url: "<?= base_url('admin/update'); ?>",
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
                        value = $td.find('input').val();
                        name = $td.data('name');
                        $td.html(value);
                    });
                    $('button.update_' + id).css('display', 'none');
                    $('button.edit_' + id).css('display', 'inherit');
                    $('button.reset_' + id).css('display', 'none');
                }
                // if (returnedData.response == 'error') {
                //     $tr.find('td.editable').each(function() {
                //         var $td = $(this);
                //         value = $td.find('input').val();
                //         name = $td.data('name');
                //         $td.html(value);
                //     });
                //     $('button.update_' + id).css('display', 'none');
                //     $('button.edit_' + id).css('display', 'inherit');
                //     $('button.reset_' + id).css('display', 'none');
                // }
            }
        }); // you have missed this bracket
    }

    function deleteHandler(id) {
        $("#contactLoader").show(); // Show loader when delete button click
        $.ajax({
            type: "POST",
            url: "<?= base_url('admin/delete'); ?>",
            data: {
                'id': id
            },
            success: function(response) {
                var returnedData = JSON.parse(response);
                $("#contactLoader").hide(); // Hide loader when account delete successfully
                if (returnedData.response == 'success') {
                    $('tr#row_' + id).remove();
                }
            }
        }); // you have missed this bracket

    }

    function resetHandler(id) {
        var parsedData = localStorage.getItem(id);
        var result = JSON.parse(parsedData);
        var $tr = $('#row_' + id);
        $tr.find('td.editable').each(function() {
            var $td = $(this);
            value = $td.find('input').val();
            name = $td.data('name');
            $td.html(result[name]);

        });
        $('button.update_' + id).css('display', 'none');
        $('button.edit_' + id).css('display', 'inherit');
        $('button.reset_' + id).css('display', 'none');
        localStorage.removeItem(id);
    }
    $("#formAdmin").submit(function(event) {
        event.preventDefault();
        $("input").removeClass('error');
        $(".errorMessage").remove();
        $("#adminLoader").show(); // Show loader when submit button click
        $("button.is-link").attr("disabled", true); // Make Submit button disable when click to add admin
        $.ajax({
            type: "POST",
            url: "<?= base_url('admin/create'); ?>",
            data: $(this).serialize(),
            success: function(response) {
                var returnedData = JSON.parse(response);
                $("#adminLoader").hide(); // Hide loader after ajax call
                $("button.is-link").attr("disabled", false); // Make Submit button enable when click to add admin
                if (returnedData.results !== undefined) {
                    for (var key in returnedData.results) {
                        if (returnedData.results.hasOwnProperty(key)) {
                            $("#" + key).addClass('error');
                            var error = '<span class="errorMessage">' + returnedData.results[key] + '</span>';
                            $("#" + key).after(error);
                        }
                    }
                }
                if (returnedData.response == 'success') {
                    if (returnedData.markup !== '') {
                        $("#success-wrapper").fadeIn(); // Success message will display after form field submitted
                        $("#DataTables_Table_2_admin tbody").append(returnedData.markup)
                        document.getElementById("formAdmin").reset();

                    }
                }
            }
        }); // you have missed this bracket
        return false;
    });
    // This function is made for toggle the contact form and also reset fields after toggle the form
    $("input.create-contacts").click(function() {
        $(".new-contactspanel").toggle('fast').promise().done(function() {
            if ($(this).is(':visible')) {

            } else {
                document.getElementById("formAdmin").reset();
                $("#formAdmin input").removeClass('error');
                $("#formAdmin .errorMessage").remove();
            }
        });
    });
    $("input.input").click(function(){
        $("#success-wrapper").fadeOut();
    });
</script>