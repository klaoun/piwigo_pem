<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/account.css">

{* Load selectize *}
<script src="{$PEM_ROOT_URL_PLUGINS}vendor/js/selectize.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}vendor/css/selectize.clear.css">

<div class="container" id="profile">

{if isset($MESSAGE)}
<div class="alert {if $MESSAGE_TYPE == "success"}alert-success{/if} mt-3" role="alert">
  <span>{$MESSAGE}</span><br>
</div>
{/if}

{if $can_modify == true}
  <section  class="mt-4 section-fluid">
    <div class="d-flex justify-content-end">
      <div class="form-check form-switch ">
        <input class="form-check-input" type="checkbox" role="switch" id="edit_mode" >
        <label class="form-check-label" for="edit_mode">Edit mode</label>
      </div>                                            
    </div>
  </section>
{/if}

  <section class="section-fluid my-5">
    <div class="row">
      <div class="col-4 border-right position-relative">
{if $can_modify == true}
        <span class="circle-icon edit_mode position-absolute top-0 end-0 translate-middle" data-bs-toggle="modal" data-bs-target="#userInfoModal">
          <i class="icon-pencil"></i>
        </span>
 {/if}
        <h3>{$USER.username}</h3>
       {if isset($USER.group)}<h5>{$USER.group}</h5>{/if }
      </div>
{if isset($USER.links)}
      <div class="col-4 border-right">
      </div>
{/if}
      <div class="col-4">
        <p class="sub-text">Member since:</p>
        <p class="d-inline">{$USER.registration_date_formatted}</p><span class="badge blue-badge d-inline">{$USER.member_since}<span>
      </div>
    </div>
  </section>

  <section class="section-fluid my-5">
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <div>
          <h4 class="d-inline">Extensions by "{$USER.username}"</h4><span class="badge blue-badge d-inline">{$USER.nb_extensions}</span>
        </div>
{if $can_modify == true}
        <button data-bs-toggle="modal" data-bs-target="#addExtModal" class="btn btn-primary align-middle">Add an extension</button>
{/if}
      </div>
{if isset($extensions) || isset($other_extensions)}
      <div class="col-12 my-5">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Publish date</th>
              <th>Last update</th>
              <th>Downloads</th>
              <th>Rating</th>
            </tr>
          <thead>
          <tbody>
  {foreach from=$extensions item=extension}
            <tr>
              <td><a class="link" href="{$PEM_ROOT_URL}index.php?eid={$extension.id}">{$extension.name}</a></td>
              <td>{$extension.category}</td>
              <td>{$extension.publish_date}</td>
              <td>{$extension.last_updated}</td>
              <td>{$extension.nb_downloads}</td>
              <td>{if $extension.rating_score}{$extension.rating_score}({$extension.total_rates}){/if}</td>
            </tr>
  {/foreach}
          </tbody>
        </table>
      </div>
{else}
  <div class="row">
      <div class="col-12">
        <p>This user has not yet contributed any plugins</p>
      </div>
    </div>
{/if}
    </div>
  </section>
  
</div>

{$PEM_USER_EDIT_INFO_FORM}
{$PEM_ADD_EXT_FORM}

<script src="{$PEM_ROOT_URL_PLUGINS}template/js/account.js" require="jquery"></script>
{combine_script id='jquery.confirm' load='footer' require='jquery' path='vendor/js/jquery-confirm.min.js'}

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
</script>

