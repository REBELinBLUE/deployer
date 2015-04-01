<div class="modal fade" id="group">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <span>Add a new group</span></h4>
            </div>
            <form role="form">
                <input type="hidden" id="group_id" name="id" />
                <div class="modal-body">

                    <div class="callout callout-danger">
                        <i class="icon fa fa-warning"></i> The group could not be saved, please check the form below.
                    </div>

                    <div class="form-group">
                        <label for="group_name">Name</label>
                        <input type="text" class="form-control" name="name" id="group_name" placeholder="Projects" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-right btn-save"><i class="fa fa-save"></i> Save Group</button>
                </div>
            </form>
        </div>
    </div>
</div>