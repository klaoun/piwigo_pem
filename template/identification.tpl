<div class="" id="identification">

  <section class="align-self-start p-4">
    <a href="{$PEM_ROOT_URL}index.php" class="orange-link link"><i class="icon-chevron-left"></i>Back</a>
  </section>

  <section class="w-100 h-100">

    <div class="col-xs-12 col-md-4 col-lg-3 col-xl-3 position-absolute vertical-horizontal-align">
    
      <h1 class="text-center">
        <i class="icon-arrow-right-from-bracket"></i>{'Login'|translate}
      </h1>
      
      <form class=" pt-5 properties" action="{$F_LOGIN_ACTION}" method="post" name="login_form">

{include file='infos_errors.tpl'}

        <div class="w-100 form-group mx-auto">
          <label for="username">{'Username'|translate}</label>
          <input type="text" class="form-control w-100" type="text" name="username" id="username" size="25" placeholder="Enter username">
        </div>

        <div class="w-100 form-group mx-auto pt-3">
          <label for="password">{'Password'|translate}</label>
          <input type="password" class="form-control w-100" type="password" name="password" id="password" size="25" placeholder="Password">
        </div>

{if $authorize_remembering }
          <div class="w-100 form-group mx-auto pt-3">
            <input tabindex="3" type="checkbox" name="remember_me" id="remember_me" value="1">
            <label for="remember_me">{'Auto login'|translate}</label>
          </div>
{/if}
        
        <div class="text-center">
          <input tabindex="4" type="submit" name="login" value="{'Login'|translate}" class="btn btn-primary mt-4 w-100">
        </div>

      </form>

      <div class="w-100 text-center mt-4">
        <a href="{$U_LOST_PASSWORD}" title="{'Forgot your password?'|translate}" class="link-primary orange-link link">
          {'Forgot your password?'|translate}
        </a>
      </div>

      {if isset($U_REGISTER)}
      <div class="w-100 text-center border-top mt-4">
        <p class="mt-4">or</p>
        <a href="{$U_REGISTER}" title="{'Register'|translate}">
          <button class="btn btn-secondary mt-4 w-100">{'Register'|translate}</button>
        </a>
      </div>
      {/if}
    
    </div>
  </section>

</div>

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
</script>
