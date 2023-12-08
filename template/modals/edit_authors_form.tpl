<section id="authors-popin">
  <div>
    <div class="modal fade" id="authorsModal" tabindex="-1" aria-labelledby="authorsModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="authorsModalLabel"><i class="icon-user"></i>My profil</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" action="{$f_action}" enctype="multipart/form-data">

            <div class="modal-body">
              <h4>Add authors</h4>
              {* Extension Authors *}
              <div class="mb-3 form-group">
                <label for="extension_author_select" class="col-12">Authors</label>
                <select class="extension_author_select col-12" data-selectize="author"
                  placeholder="{'Type in a search term'|translate}"
                  data-create="true" name="author">
                  <option selected>Type in a search term</option>
{foreach from=$ALL_AUTHORS item=author}
                  <option value="{$author.uid}" >{$author.username}</option>
{/foreach}
                </select>
              </div>

              <h4>Manage authors</h4>
              <div>
{foreach from=$authors item=author}
                <div class="d-block">
                  <strong>{$author}</strong>
  {if isset($u_owner)}
                  <a href="{$u_delete}">{'Delete'|translate}</a>
    {if isset($u_owner)}| <a href="{$u_owner}">{'Set as owner'|translate}</a>{/if}
  {else}
                  <p>({'Owner'|translate})</p>
  {/if}
{/foreach}
                </div>
              </div>

            </div>

            <input type="hidden" name="pem_action" value="edit_authors">

            <div class="modal-footer mt-3">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" value="{'Save Changes'|@translate}" name="submit" />
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
