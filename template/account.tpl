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
        <label class="form-check-label" for="edit_mode"> {'Edit mode'|translate}</label>
      </div>                                            
    </div>
  </section>
{/if}

  <section class="section-fluid my-5">
    <div class="row">
      <div class="col-6 border-right position-relative">
{if $can_modify == true}
        <span class="circle-icon edit_mode position-absolute top-0 end-0 translate-middle" data-bs-toggle="modal" data-bs-target="#userInfoModal">
          <i class="icon-pencil"></i>
        </span>
 {/if}
        <h3 class="hide-text-overflow-300">{$USER.username}</h3>
       {if isset($USER.group)}<h5>{$USER.group}</h5>{/if }
      </div>
      <div class="col-6 d-flex flex-column align-items-start justify-content-evenly">
        <div class="p-1">
          <p class="d-inline">{'Member since %s'|translate:$USER.registration_date_formatted}<span class="badge blue-badge d-inline">{$USER.member_since}</span></p>
        </div>
        {if isset($USER.last_activity_formatted)}
        <div class="p-1">
          <p class="d-inline">{'Last activity %s'|translate:$USER.last_activity_formatted}<span class="badge blue-badge d-inline">{$USER.last_activity_since}</span></p>
        </div>
        {/if}
      </div>
    </div>
  </section>

  <section class="section-fluid my-5">
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <div>
          <h4 class="d-inline-block m-0">{'Extensions'|translate}</h4>
          <span class="badge blue-badge d-inline">{$USER.nb_extensions}</span>
        </div>
{if $can_modify == true}
        <button data-bs-toggle="modal" data-bs-target="#addExtModal" class="btn btn-primary align-middle">{'Add an extension'|translate}</button>
{/if}
      </div>
{if isset($extensions) || isset($other_extensions)}
      <div class="col-12 my-5">
        <table class="table table-striped">
          <thead>
            <tr>
              <th></th>
              <th>{'Name'|translate}</th>
              <th>{'Age'|translate}</th>
              <th>{'Last update'|translate}</th>
              <th>{'Compatibility'|translate}</th>
              <th class="text-center"><i class="icon-download"></i></th>
              <th>{'Rating'|translate}</th>
            </tr>
          <thead>
          <tbody>
  {foreach from=$extensions item=extension}
            <tr>
              <td class="text-center grey-font">
                <i class="
                {if $extension.category == "Plugin"}
                  icon-puzzle-piece
                {else if $extension.category == "Theme"}
                  icon-palette
                {else if $extension.category == "Tool"}
                  icon-screwdriver-wrench
                {else if $extension.category == "Language"}
                  icon-language
                {/if}
                "></i>
              </td>
              <td><a class="link orange-link" href="{$PEM_ROOT_URL}index.php?eid={$extension.id}">{$extension.name}</a></td>
              <td>
              {if !empty($extension.age)}
                <span class="ms-0 badge blue-badge pem-tooltip">
                  {$extension.age}
                  <span class="pem-tooltiptext">{$extension.publish_date}</span>
                </span>
              {/if}
              </td>
              <td>{$extension.last_updated}</td>
              <td>
                <span class="compatibility compatibility-first">
                  <i class="icon-code-branch"></i>{$extension.compatibility_first}
                </span>
                <i class="icon-arrow-right"></i>
                <span class="compatibility compatibility-last">
                  <i class="icon-code-branch"></i>{$extension.compatibility_last}
                </span>
              </td>
              <td class="space-mono-regular text-end">
                {if $extension.nb_downloads > 0}{$extension.nb_downloads}{/if}
              </td>
              <td>{if $extension.rating_score}{$extension.rating_score}({$extension.total_rates}){/if}</td>
            </tr>
  {/foreach}
          </tbody>
        </table>
      </div>
{else}
  <div class="row">
      <div class="col-12">
        <p>{'This user has not yet contributed any plugins'|translate}</p>
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

