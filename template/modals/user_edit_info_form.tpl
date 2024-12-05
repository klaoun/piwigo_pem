<section id="userInfo-popin">
  <div>
    <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="userInfoModalLabel"><i class="icon-gears me-2"></i>{'User settings'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" enctype="multipart/form-data" autocomplete="off" id="userInfoForm">

            <div class="modal-body">

              <input type="hidden" name="user_id" value="{$USER.id}">

              {* User name *}
              <div class="mb-3 form-group">
                <label for="user_name" class="form-label w-100 ">{'Username'|translate}</label>
                <input type="text" name="user_name" size="35" maxlength="255" {if isset($USER.username)}value="{$USER.username}"{/if} class="w-100 form-control"/>
              </div>

              {* User EMAIL *}
              <div class="mb-3 form-group">
               <label for="user_email" class="form-label w-100 ">{'Email'|translate}</label>
               <input type="email" name="user_email" {if isset($USER.username)}value="{$USER.email}"{/if} class="w-100 form-control"/>
              </div>

            </div>

            <input type="hidden" name="pem_action" value="edit_user_info">

            <div class="modal-footer">
              <button type="button" class="btn btn-tertiary small-btn" data-bs-dismiss="modal">{'Close'|translate}</button>
              <div class="spinner-border d-none" role="status">
                <span class="visually-hidden">{'Loading...'|translate}</span>
              </div>
              <input type="submit" class="btn btn-primary small-btn" value="{'Save Changes'|translate}" name="submit" onclick="onSubmitDisplaySpinner('userInfoForm');"/>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>