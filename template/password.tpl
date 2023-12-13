<div class="" id="password">

  <section class="align-self-start p-4">
    <a href="{$PEM_ROOT_URL}index.php" class="orange-link"><i class="icon-chevron-left"></i>Back</a>
  </section>


  <section class=" mx-4 position-absolute vertical-horizontal-align">
    <div class="w-100"> 
      <h1 class="text-center">
        <i class="icon-lock"></i>Forgotten password ?
      </h1>

{if isset($errors) }
      <div class="errors mt-5">
        <i class="icon-cross h-100 d-inline-block"></i>
        <div class="errors-content d-inline-block">
  {foreach from=$errors item=error}
          <p class="m-0">{$error}</p>
  {/foreach}
        </div>
      </div>
{/if}
  
{if not empty($infos)}
      <div class="infos mt-5">
        <i class="icon-check h-100 d-inline-block"></i>
        <div class="infos-content d-inline-block">
  {foreach from=$infos item=info}
          <p class="m-0">{$info}</p>
  {/foreach}
        </div>
      </div>
{/if}

{if $action != 'none'}
      <form class="col-xs-12 col-md-4 col-lg-3 col-xl-2 w-100 pt-5 properties" id="lostPassword" name="lostPassword" action="{$form_action}?action={$action}{if isset($key)}&amp;key={$key}{/if}" method="post">

        <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  {if $action == 'lost'}
        <div class="messages">
          <p class="m-0">{'Please enter your username or email address.'|@translate} {'You will receive a link to create a new password via email.'|@translate}</p>
        </div>

        <div class="w-100 form-group mx-auto pt-3">
          <label for="username_or_email">* {'Username or email'|@translate}</label>
          <input type="text" class="form-control w-100" name="username_or_email" id="username_or_email" {if isset($username_or_email)} value="{$username_or_email}"{/if}>
        </div>

        <div class="text-center">
          <input type="submit" name="submit" value="Send me the link" class="btn btn-primary mt-4 w-100"> 
        </div>

  {elseif $action == 'reset'}

        <div class="message">
          <p>{'Hello'|@translate} <em>{$username}</em>. Please enter your new password below.</p>
        </div>

        <div class="w-100 form-group mx-auto pt-3">
          <label for="use_new_pwd">* {'New password'|@translate}</label>
          <input type="password" class="form-control w-100" name="use_new_pwd" id="use_new_pwd" value="">
        </div>

        <div class="w-100 form-group mx-auto pt-3">
          <label for="passwordConf">* {'Confirm Password'|@translate}</label>
          <input type="password" class="form-control w-100" name="passwordConf" id="passwordConf" value="">
        </div>

        <div class="text-center">
          <input type="submit" name="submit" value="{'Change my password'|@translate}"class="btn btn-primary mt-4 w-100"> 
        </div>

  {/if}

      </form>
{/if}

    </div>
  </section>

</div>

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
</script>
