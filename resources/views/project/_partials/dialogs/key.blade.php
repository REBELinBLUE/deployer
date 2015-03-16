<div class="modal fade" id="key">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="fa fa-key"></i> Public SSH Key</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    This key must be added to the server's <strong>.ssh/authorized_keys</strong> for each user you wish to run commands as.
                </div>

                <pre>ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAIEAt9MokW5ue1N/Ss3bdBKTlO0LaXBDd+G99oJgBabkIV6GSVQBjAEaSx9GxgQnjs0NKGWvPdpanshia+PKvoPbwEpLUOX84Raq9rbckuavXIdEW/SehQ7eIfXIiO5lo5CvlxQkszSsDGj3btlOEp9zrSyKOcoVp6PP8A0wxkF6Bx0= user@host</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>