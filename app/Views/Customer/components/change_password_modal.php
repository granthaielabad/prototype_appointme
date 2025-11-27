<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="/profile/change-password">
        <div class="modal-body">
          <div class="mb-2"><input required name="current_password" class="form-control" placeholder="Current password" type="password"></div>
          <div class="mb-2"><input required name="new_password" class="form-control" placeholder="New password" type="password"></div>
          <div class="mb-2"><input required name="confirm_password" class="form-control" placeholder="Confirm new password" type="password"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">Change password</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
