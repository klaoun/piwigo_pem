{* Modal asking for confirmation to delete *}
<section id="deleteRevision-popin">
  <div>
    <div class="modal fade" id="deleteRevisionModal" tabindex="-1" aria-labelledby="deleteRevisionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteRevisionModalLabel"><i class="icon-trash"></i> {'Delete revision'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <p>{'Are you sure you want to delete this revision ?'|translate}</p>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{'Cancel'|translate}</button>
            <button type="button" class="btn btn-danger" id="deleteRevision">{'Yes, delete'|translate}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>