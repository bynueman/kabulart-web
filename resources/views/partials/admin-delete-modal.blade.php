<!-- DELETE CONFIRMATION MODAL -->
<div class="adm-modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
  <div class="adm-modal">
    <div class="adm-modal__title" id="deleteModalTitle">Hapus Data</div>
    <div class="adm-modal__body">Apakah Anda yakin ingin menghapus data ini? Gambar yang terkait juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</div>
    <div class="adm-modal__actions">
      <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="adm-btn adm-btn--danger">Ya, Hapus</button>
      </form>
      <button type="button" class="adm-btn adm-btn--ghost" id="cancelDelete">Batal</button>
    </div>
  </div>
</div>
