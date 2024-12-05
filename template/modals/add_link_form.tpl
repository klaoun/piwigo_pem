<section id="addLink-popin">
  <div>
    <div class="modal fade" id="addLinkModal" tabindex="-1" aria-labelledby="addLinkModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addLinkModalLabel"><i class="icon-circle-plus"></i> {'Create a new link'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" enctype="multipart/form-data" autocomplete="off" id="addLinkForm">

            <div class="modal-body"> 
                
                <div class="mb-3 form-group">
                  <label for="link_name" class="col-12">{'Name'|translate}</label>
                  <input type="text" name="link_name" maxlength="50" class="w-100 form-control" required autofocus>
                  <p><small>{'Here are some link suggestions: "Github page", "Forum topic", "Issues", "Buy me a coffee" ...'|translate}</small></p>
                </div>

                <div class="mb-3 form-group">
                  <label for="link_url" class="col-12">{'URL'|translate}</label>
                  <input type="url" name="link_url" size="50" maxlength="255" class="w-100 form-control" required>
                </div>
{if !empty($languages)}
                    <label for="link_language" class="col-12">{'Language'|translate}</label>           
                    <select name="link_language" class="form-select w-100">
                      <option value="0">{'All languages'|translate}</option>
  {foreach from=$languages item=language}
                      <option value="{$language.id}">{$language.name}</option>
  {/foreach}
                    </select>
{/if}
          

            </div>

            <input type="hidden" name="pem_action" value="add_link">

            <div class="modal-footer">
              <button type="button" class="btn btn-tertiary small-btn" data-bs-dismiss="modal">{'Close'|translate}</button>
              <div class="spinner-border d-none" role="status">
                <span class="visually-hidden">{'Loading...'|translate}</span>
              </div>
              <input type="submit" class="btn btn-primary small-btn" value="Submit" name="submit" onclick="onSubmitDisplaySpinner('addLinkForm');"/>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>