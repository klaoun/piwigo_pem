<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/account.css">

<div class="container" id="profile">

  <section class="section-fluid my-5">
    <div class="row">
      <div class="col-4 border-right">
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
{if isset($extensions) || isset($other_extensions)}
  <section class="section-fluid my-5">
    <div class="row">
      <div class="col-12">
        <h4 class="d-inline">Extensions by "{$USER.username}"</h4><span class="badge blue-badge d-inline">{$USER.nb_extensions}</span>
      </div>
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
            <td><a href="{$PEM_ROOT_URL}index.php?eid={$extension.id}">{$extension.name}</a></td>
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
    </div>
  </section>
{/if}


</div>