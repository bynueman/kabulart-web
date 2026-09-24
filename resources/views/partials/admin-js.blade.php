<script>
(function() {
  var sidebar  = document.getElementById('admSidebar');
  var hamburger= document.getElementById('admHamburger');
  var overlay  = document.getElementById('admOverlay');

  function openSidebar() {
    sidebar.classList.add('is-open');
    overlay.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    hamburger.setAttribute('aria-expanded', 'true');
  }
  function closeSidebar() {
    sidebar.classList.remove('is-open');
    overlay.classList.remove('is-open');
    document.body.style.overflow = '';
    hamburger.setAttribute('aria-expanded', 'false');
  }

  if (hamburger) hamburger.addEventListener('click', function() {
    sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
  });
  if (overlay) overlay.addEventListener('click', closeSidebar);

  // Auto-dismiss flash messages
  var flashes = document.querySelectorAll('.adm-flash[data-auto-dismiss]');
  flashes.forEach(function(el) {
    setTimeout(function() {
      el.style.transition = 'opacity 0.5s';
      el.style.opacity = '0';
      setTimeout(function() { el.style.display = 'none'; }, 500);
    }, 4500);
  });

  // Delete modal
  var deleteModal = document.getElementById('deleteModal');
  var deleteForm  = document.getElementById('deleteForm');
  var cancelDelete= document.getElementById('cancelDelete');

  document.querySelectorAll('[data-delete-url]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      if (!deleteModal || !deleteForm) return;
      deleteForm.action = this.getAttribute('data-delete-url');
      deleteModal.classList.add('is-open');
    });
  });
  if (cancelDelete) cancelDelete.addEventListener('click', function() {
    deleteModal.classList.remove('is-open');
  });
  if (deleteModal) deleteModal.addEventListener('click', function(e) {
    if (e.target === deleteModal) deleteModal.classList.remove('is-open');
  });

  // Image preview
  var imageInput = document.getElementById('imageInput');
  var previewWrap= document.getElementById('previewWrap');
  var previewImg = document.getElementById('previewImg');
  var previewName= document.getElementById('previewName');
  var previewSize= document.getElementById('previewSize');
  var previewErr = document.getElementById('previewError');
  var currentUrl = null;

  if (imageInput) {
    imageInput.addEventListener('change', function(e) {
      if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
      if (previewWrap) previewWrap.classList.remove('is-visible');
      if (previewErr)  previewErr.style.display = 'none';

      var file = e.target.files[0];
      if (!file) return;

      if (file.size > 15 * 1024 * 1024) {
        if (previewErr) {
          previewErr.textContent = 'Ukuran file melebihi 15 MB (' + (file.size / 1048576).toFixed(1) + ' MB). Pilih file yang lebih kecil.';
          previewErr.style.display = 'block';
        }
        imageInput.value = '';
        return;
      }

      currentUrl = URL.createObjectURL(file);
      if (previewImg)  previewImg.src  = currentUrl;
      if (previewName) previewName.textContent = file.name;
      if (previewSize) previewSize.textContent = (file.size / 1024).toFixed(1) + ' KB — Dioptimasi otomatis saat disimpan.';
      if (previewWrap) previewWrap.classList.add('is-visible');
    });
  }

  var uploadZone = document.querySelector('.adm-upload-zone');
  if (uploadZone) {
    uploadZone.addEventListener('dragover', function(e) { e.preventDefault(); uploadZone.classList.add('drag-over'); });
    uploadZone.addEventListener('dragleave', function() { uploadZone.classList.remove('drag-over'); });
    uploadZone.addEventListener('drop', function(e) { e.preventDefault(); uploadZone.classList.remove('drag-over'); });
  }
})();
</script>
