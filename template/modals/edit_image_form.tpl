<section id="Image-popin">
    <div>
      <div class="modal fade" id="ImageModal" tabindex="-1" aria-labelledby="ImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="ImageModalLabel"><i class="icon-image"></i>Edit images</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            

            <form method="post" action="{$f_action}" enctype="multipart/form-data">
              <div class="modal-body">
                <legend>{'Add or replace screenshot'|@translate}</legend>
            
                <strong>{'File'|@translate} *</strong>
                <input type="file" name="picture" maxlength="50" {if isset($f_picture_name)}value="{$f_picture_name}"{/if}/>

              </div>

              <input type="hidden" name="pem_action" value="edit_screenshot">

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="{'Save Changes'|@translate}" name="submit" />
              </div>

            </form>
          
          </div>
        </div>
      </div>
    </div>
  </section>