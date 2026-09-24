<!-- DELETE CONFIRMATION MODAL -->
<div class="adm-modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
  <div class="adm-modal">
    <div class="adm-modal__title" id="deleteModalTitle">{{ __('admin.delete_modal_title') }}</div>
    <div class="adm-modal__body">{{ __('admin.delete_modal_body') }}</div>
    <div class="adm-modal__actions">
      <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="adm-btn adm-btn--danger">{{ __('admin.delete_modal_confirm') }}</button>
      </form>
      <button type="button" class="adm-btn adm-btn--ghost" id="cancelDelete">{{ __('admin.delete_modal_cancel') }}</button>
    </div>
  </div>
</div>
