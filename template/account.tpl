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
      <span class="link-secondary link" data-bs-toggle="modal" data-bs-target="#userInfoModal">
        <i class="icon-gears me-1"></i>{'User settings'|translate}
      </span>
    </div>
  </section>
{/if}

  <section class="section-fluid my-5">
    <div class="row">
      <div class="col-6 border-right position-relative">
        <h3 class="hide-text-overflow-300">{$USER.username}</h3>
       {if isset($USER.group)}<h5>{$USER.group}</h5>{/if }
      </div>
      <div class="col-6 d-flex flex-column align-items-start justify-content-evenly">
        <div class="p-1">
          <p class="d-inline">{'Member since %s'|translate:$USER.registration_date_formatted}<span class="badge blue-badge d-inline">{$USER.member_since}</span></p>
        </div>
{if isset($USER.last_activity_formatted)}
        <div class="p-1 mt-2">
          <p class="d-inline">{'Last activity %s'|translate:$USER.last_activity_formatted}<span class="badge blue-badge d-inline">{$USER.last_activity_since}</span></p>
        </div>
{/if}
      </div>
    </div>
  </section>
{if isset($u_translator) && $u_translator == true}
    <section class="section-fluid my-5">
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
          <div>
          <h4 class="d-inline-block m-0">{'Languages'|translate}</h4>
          <span class="badge blue-badge d-inline">{$count_langs}</span>
        </div>
      </div>
      <div class="mt-3">
        <p>You can translate these languages :</p>
  {foreach $all_languages as $lang}
    {if in_array($lang.id_language, $translator_lang_ids) }
        <p class="d-inline-block my-2 ms-0 me-3">{$lang.name}</p>
    {/if}
  {/foreach}
          
      </div>

    </div>
    </section>
{/if}

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
              <th onclick="sortExtensions('name')" class="name">
               {'Name'|translate}
                {* <i class="icon-sort"></i> *}
              </th>
              <th onclick="sortExtensions('publish_date_not_formatted')" class="publish_date_not_formatted">
               {'Age '|translate}
                {* <i class="icon-sort"></i> *}
              </th>
              <th onclick="sortExtensions('last_updated')" class="last_updated">
                {'Last update'|translate}
                {* <i class="icon-sort"></i> *}
              </th>
              <th onclick="sortExtensions('compatibilty_last')" class="compatibilty_last">
               {'Compatibility'|translate}
                {* <i class="icon-sort"></i> *}
              </th>
              <th class="text-center" onclick="sortExtensions('nb_downloads')" class="nb_downloads">
                <i class="icon-download"></i> 
                {* <i class="icon-sort"></i> *}
              </th>
              <th onclick="sortExtensions('rating_score_not_formatted')" class="rating_score_not_formatted">
                {'Rating'|translate}
                {* <i class="icon-sort"></i> *}
              </th>
            </tr>
          <thead>
          <tbody data-extensions='{$extensions_json}' >

            <tr id="jango_fett">
              <td class="text-center grey-font category"></td>
              <td class="name"></td>
              <td class="age"></td>
              <td class="last_updated"></td>
              <td class="compatibilty"></td>
              <td class="text-end nb_downloads"></td>
              <td class="rating"></td>
            </tr>


  {* {foreach from=$extensions item=extension}
            <tr>
              
              <td class="name"><a class="link orange-link" href="{$PEM_ROOT_URL}index.php?eid={$extension.id}">{$extension.name}</a></td>
              <td>
    {if !empty($extension.age)}
                <span class="ms-0 badge blue-badge pem-tooltip">
                <i class="icon-clock"></i>
                  {$extension.age}
                  <span class="pem-tooltiptext">{$extension.publish_date}</span>
                </span>
    {/if}
              </td>
              <td>{$extension.last_updated}</td>
              <td>
    {if !empty($extension.compatibility_first)}
                <span class="compatibility compatibility-first">
                  <i class="icon-code-branch"></i>{$extension.compatibility_first}
                </span>
                <i class="icon-arrow-right"></i>
                <span class="compatibility compatibility-last">
                  <i class="icon-code-branch"></i>{$extension.compatibility_last}
                </span>
    {/if}
              </td>
              <td class="text-end">
                {if $extension.nb_downloads > 0}{$extension.nb_downloads}{/if}
              </td>
              <td>
                {if isset($extension.rating_score)}{$extension.rating_score} ({$extension.total_rates}){/if}
              </td>
            </tr>
  {/foreach} *}
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

{combine_script id='jquery.confirm' load='footer' require='jquery' path='vendor/js/jquery-confirm.min.js'}

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
  const PEM_ROOT_URL = `{$PEM_ROOT_URL}`
</script>
<script src="{$PEM_ROOT_URL_PLUGINS}template/js/account.js" require="jquery"></script>


