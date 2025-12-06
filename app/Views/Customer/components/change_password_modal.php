<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modalmain modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="/profile/change-password">
        <div class="modal-body">

          <div class="row">
            
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input required name="current_password" class="form-control" type="password">
              </div>

              <div class="mb-3">
                <label class="form-label">New Password</label>
                <input required name="new_password" class="form-control" type="password">
              </div>

              <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input required name="confirm_password" class="form-control" type="password">
              </div>
            </div>

            <div class="col-md-6">
              <div class="p-2">
                <h6>Password criteria:</h6>
                <p class="text-success mb-2">
                  The password should be at least 8–12 characters long.
                </p>

                <p class="mb-2">
                  Use a mix of uppercase letters, lowercase letters, numbers, and
                  special characters (like !, @, #, $, etc.).
                </p>

                <p class="mb-0">
                  Avoid using easily guessable information such as your name,
                  birthday, or common words.
                </p>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button class="password btn btn-primary">Change Password</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>

      </form>
    </div>
  </div>
</div>
