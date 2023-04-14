<div class="container" id="register">

  <section class="w-50 m-auto">
    <h1 class="text-center pt-5">
      <i class="icon-arrow-right-from-bracket"></i>Create an account
    </h1>

    <form method="post" action="{$F_ACTION}" class="" name="register_form">

      <div class="form-group mx-auto">
        <label for="mail_address">* {'Email address'|@translate}</label>
        <input type="email" class="form-control w-100" id="mail_address" placeholder="Email" value="{$F_EMAIL}">
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

      <div>
        <label for="send_password_by_mail">{'Send my connection settings by email'|@translate}</label>
        <input type="checkbox" name="send_password_by_mail" id="send_password_by_mail" value="1" checked="checked">
      </div>

      <div class="text-center pt-3 bottomButtons">
        <button type="submit" name="submit" class="btn btn-primary mt-4 w-100" value="{'Register'|@translate}">Sign up</button>
        <button type="submit" name="reset" class="btn btn-primary mt-4 w-100" value="{'Reset'|@translate}">Sign up</button>
      </div>

    </form>

    <div class="text-center">
      <a href="{$PEM_ROOT_URL}identification.php"><button class="btn btn-secondary mt-4 w-100">Already have an account ?</button></a>
    </div>

  </section>

</div>/