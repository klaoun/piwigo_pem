<div class="container" id="identification">

  <section class="w-50 m-auto">
    <h1 class="text-center pt-5">
      <i class="icon-arrow-right-from-bracket"></i>Login
    </h1>

    <form class="w-100 pt-5 properties" action="{$F_LOGIN_ACTION}" method="post" name="login_form">
      <div class="form-group mx-auto">
        <label for="username">{'Username'|@translate}</label>
        <input type="email" class="form-control w-100" type="text" name="username" id="username" size="25 placeholder="Enter username">
      </div>

      <div class="form-group mx-auto pt-3">
        <label for="password">{'Password'|@translate}</label>
        <input type="password" class="form-control w-100" type="password" name="password" id="password" size="25" placeholder="Password">
      </div>

      <div class="form-group mx-auto pt-3">
        <label for="password">{'Auto login'|@translate}</label>
        <input type="checkbox" name="remember_me" id="remember_me" value="1">
      </div>

      <div class="text-center pt-3">
        <button type="submit" class="btn btn-primary mt-4 w-100">Login</button>
      </div>

      <p>
        <input type="hidden" name="redirect" value="{$U_REDIRECT|@urlencode}">
        <input tabindex="4" type="submit" name="login" value="{'Submit'|@translate}">
      </p>

    </form>

    <div class="text-center">
      <a href="{$U_LOST_PASSWORD}" title="{'Forgot your password?'|@translate}">
        <button class="btn btn-secondary mt-4 w-100">{'Forgot your password?'|@translate}</button>
      </a>
    </div>

    <div class="text-center">
      <a href="{$U_REGISTER}" title="{'Register'|@translate}">
        <button class="btn btn-secondary mt-4 w-100">{'Register'|@translate}</button>
      </a>
    </div>
  
  </section>

</div>