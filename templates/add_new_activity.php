<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">-->

<!-- Latest compiled and minified JavaScript -->
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>-->

<div class="modal fade" id="add_activity_modal" tabindex="-1" role="dialog" aria-labelledby="add_activity_modal_label"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Activity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="hook-name" value='<?php echo $this->hook ?>'>
                    <div class="form-group">
                        <label for="activity-name" class="col-form-label">Activity Name:</label>
                        <input type="text" class="form-control" name="activity-name" id="activity-name">
                    </div>
                    <div class="form-group">
                        <label for="activity-id" class="col-form-label">Activity ID:</label>
                        <input type="text" class="form-control" name="activity-id" id="activity-id">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="add_activity" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
</div>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_activity_modal"
        data-whatever="@getbootstrap">Add New
</button>

<script>
    // $('#add_activity').on('click', function(e) {
    //     $.ajax({
    //         url : yourUrl,
    //         type : 'POST',
    //         dataType : 'json',
    //         success : function(data) {
    //             $('#table_id tbody').append("<tr><td>" + data.column1 + "</td><td>" + data.column2 + "</td><td>" + data.column3 + "</td></tr>");
    //         },
    //         error : function() {
    //             console.log('error');
    //         }
    //     });
    // });
</script>