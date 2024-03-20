{* Modal asking for confirmation to delete *}
<section id="deleteLink-popin">
  <div>
    <div class="modal fade" id="deleteLinkModal" tabindex="-1" aria-labelledby="deleteLinkModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteLinkModalLabel"><i class="icon-trash"></i> {'Delete link'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <p>{'Are you sure you want to delete this link ?'|translate}</p>
            <button type="button" class="btn btn-outline-secondary me-3" data-bs-dismiss="modal">{'Cancel'|translate}</button>
            <button type="button" class="btn btn-danger" id="deleteLink">{'Yes, delete'|translate}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>