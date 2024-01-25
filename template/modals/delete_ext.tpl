{* Modal asking for confirmation to delete *}
<section id="deleteExtension-popin">
  <div>
    <div class="modal fade" id="deleteExtensionModal" tabindex="-1" aria-labelledby="deleteExtensionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteExtensionModalLabel"><i class="icon-trash"></i> {'Delete extension'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <p>{'Are you sure you want to delete this extension ?'|translate}</p>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{'Cancel'|translate}</button>
            <button type="button" class="btn btn-danger" onclick="deleteExtension({$extension_id},'{$PEM_ROOT_URL}/index.php?uid={$owner_id}' )">{'Yes delete'|translate}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>