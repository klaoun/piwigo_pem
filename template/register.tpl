<div class="" id="register">

  <section class="align-self-start p-4">
    <a href="{$PEM_ROOT_URL}index.php" class="orange-link"><i class="icon-chevron-left"></i>Back</a>
  </section>


  <section class=" mx-auto col-xs-12 col-md-4 col-lg-4 col-xl-3 py-5">
    <div class="w-100"> 

      <h1 class="text-center">
        <i class="icon-arrow-right-from-bracket"></i>Create an account
      </h1>

      <form class=" properties" method="post" action="{$F_ACTION}" name="register_form" id="register_form">

{include file='infos_errors.tpl'}

        <div class="form-group mx-auto">
          <label for="mail_address">* {'Email address'|@translate}</label>
          <input type="email" class="form-control w-100" name="mail_address" id="mail_address" placeholder="Email" value="{$F_EMAIL}">
        </div>

        <div class="form-group mx-auto pt-3">
          <label for="login">* {'Username'|@translate}</label>
          <input type="text" class="form-control w-100"name="login" id="login" value="{$F_LOGIN}"  placeholder="Username">
        </div>

        <div class="form-group mx-auto pt-3">
          <label for="password">* {'Password'|@translate}</label>
          <input type="password" class="form-control w-100" name="password" id="password" >
        </div>

        <div class="form-group mx-auto pt-3">
          <label for="password_conf">* {'Confirm Password'|@translate}</label>
          <input type="password" class="form-control w-100" name="password_conf" id="password_conf" >
        </div>

        <div class="form-group mx-auto pt-3">
          <label for="send_password_by_mail">{'Send my connection settings by email'|@translate}</label>
          <input type="checkbox" name="send_password_by_mail" id="send_password_by_mail" value="1" checked="checked">
        </div>

        <div class="text-center">
          <input type="hidden" name="key" value="{$F_KEY}" >
          <input type="submit" name="submit" value="{'Register'|@translate}" class="btn btn-primary mt-4 w-100">
        </div>

      </form>

      <div class="w-100 text-center border-top mt-4">
        <p class="mt-4">or</p>
        <a href="{$PEM_ROOT_URL}identification.php"><button class="btn btn-secondary mt-4 w-100">Already have an account ?</button></a>
      </div>

    </div>
  </section>

</div>

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
</script>
