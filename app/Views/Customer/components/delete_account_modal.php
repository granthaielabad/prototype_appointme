<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-3">
      <div class="modal-body">
        <div class="mb-3">
          <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:48px;"></i>
        </div>
        <p class="fw-semibold mb-2">Are you sure you want to <span class="text-danger">delete</span> your account?</p>
        <form method="POST" action="/profile/delete-account">
          <input type="hidden" name="_csrf" value="<?= App\Core\CSRF::getToken() ?>">
          <div class="d-flex justify-content-between gap-2 mt-4">
            <button type="submit" class="btn btn-danger w-50">Delete Account</button>
            <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">No</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
