{*Start of svn_git_config tpl*}
<section id="editSvnGit-popin">
  <div>
    <div class="modal fade" id="editSvnGitModal" tabindex="-1" aria-labelledby="editSvnGitModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editSvnGitModalLabel"><i class="icon-git-alt"></i> SVN and Git configuration</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form method="POST" enctype="multipart/form-data" autocomplete="off">
            
            <div class="modal-body">

              <h4>Edit configuration</h4>

              <div class="mb-3 form-check d-inline-block">
                <input class="form-check-input" type="radio" name="type" id="type_svn" value="svn" {if $TYPE == 'svn'} checked="checked"{/if}>
                <label class="form-check-label" for="type">
                  SVN
                </label>
              </div>

              <div class="mb-3 form-check d-inline-block">
                <input class="form-check-input" type="radio" name="type" id="type_git" value="git" {if $TYPE == 'git'} checked="checked"{/if}>
                <label class="form-check-label" for="type">
                  Git
                </label>
              </div>

              <div class="mb-3 form-group">
                <label for="repository_url" class="col-12">{'Repository URL'|@translate}</label>
                <input type="text" name="url" id="repository_url" size="30" class="w-100 form-control" {if isset($URL)}value="{$URL}"{/if} required>
              </div>

{if $TYPE == 'svn'}
  {if isset($ROOT_DIR)}
              <div class="mb-3 form-group">
                <label for="root_dir" class="col-12">{'Archive root directory'|@translate}</label>
                <input type="text" name="root_dir" id="root_dir" size="30" class="w-100 form-control" {if isset($ROOT_DIR)}value="{$ROOT_DIR}"{/if}>
              </div>
  {/if}

  {if isset($ARCHIVE_NAME)}
              <div class="mb-3 form-group">
                <label for="archive_name" class="col-12">{'Archive name'|@translate}</label>
                <input type="text" name="archive_name" id="archive_name" size="30" class="w-100 form-control" {if isset(ARCHIVE_NAME)}value="{$ARCHIVE_NAME}"{/if}>
                <p class="form-text">{'% will be replaced by version number'|@translate}</p>
              </div>
  {/if}
{/if}

{if isset($URL)}
              <div class="mb-3 form-group">
                <button class="link-primary" onclick="deleteSVNGitConfig({$extension_id})">{'Delete SVN/Git data'|translate}</button>
              </div>
{/if}                

              
{if isset($SVN_INFOS) && !empty($SVN_INFOS)}
                <h4>Information</h4>
  {foreach from=$SVN_INFOS item=info}
                <p>{$info}</p>
  {/foreach}
{/if}

            </div>

            <input type="hidden" name="pem_action" value="edit_svn_git">

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