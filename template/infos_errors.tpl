{if isset($errors) }
<div class="alert alert-danger" role="alert">
  {foreach from=$errors item=error}
    <span>{$error}</span><br>
  {/foreach}
</div>

{/if}

{if not empty($infos)}
<div class="alert alert-success" role="alert">
  {foreach from=$infos item=info}
  <span>{$info}</span><br>
  {/foreach}
</div>
{/if}