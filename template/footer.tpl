  <footer>
    <div class="container-fluide footer">
      <div class="container">
        <div class="row">
          <div class="col-md-2">
            <div class="text-center icon-social-media">
              <a href="https://www.facebook.com/Piwigo"><img src="{$PEM_ROOT_URL_PLUGINS}images/footer/facebook.svg" class="icon-facebook" alt="Facebook icon"></a>
              <a href="https://twitter.com/piwigo"><img src="{$PEM_ROOT_URL_PLUGINS}images/footer/twitter.svg" class="icon-twitter" alt="Twitter icon"></a>
              <a href="https://github.com/Piwigo"><img src="{$PEM_ROOT_URL_PLUGINS}images/footer/github.svg" class="icon-github" alt="Github icon"></a>
            </div>
            <div class="row">
              <div class="col-md-12 col-xs-6">
                <div class="footer-donate-content">
                  <a type="button" class="btn" href="https://piwigo.org/get-involved#donate"><img src="{$PEM_ROOT_URL_PLUGINS}images/footer/donate.svg" alt="Donate to Piwigo"/>{'Donate'|translate}</a>
                </div>
              </div>
              <div class="col-md-12 col-xs-6">
                <div class=" footer-newspaper-content">
                  <a type="button" class="btn" href="https://piwigo.org/newsletters"><img src="{$PEM_ROOT_URL_PLUGINS}images/footer/newsletters.svg" alt="Newsletter from Piwigo"/>{'Newsletters'|translate}</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 equal footer-list">
            <div class="col-md-15 col-xs-6">
              <h5>{'Discover'|translate}</h5>
                <ul>
                  <li><a href="https://piwigo.org/demo}">{'Demo'|translate}</a></li>
                  <li><a href="https://piwigo.org/what_is_piwigo">{'What is Piwigo?'|translate}</a> </li>
                  <li><a href="https://piwigo.org/features">{'Features'|translate}</a> </li>
                  <li><a href="https://piwigo.org/showcases">{'Showcases'|translate}</a></li>
                  <li><a href="https://piwigo.org/changelogs">{'Changelogs'|translate}</a></li>
                  <li><a href="https://piwigo.org/testimonials">{'Testimonials'|translate}</a></li>
                </ul>
            </div>
            <div class="col-md-15 col-xs-6">
              <h5>{'Extensions'|translate}</h5>
                <ul>
                  <li><a href="//{$PORG_DOMAIN_PREFIX}piwigo.org/ext/">{'Themes'|translate}</a></li>
                  <li><a href="//{$PORG_DOMAIN_PREFIX}piwigo.org/ext/">{'Plugins'|translate}</a> </li>
                  <li><a href="//{$PORG_DOMAIN_PREFIX}piwigo.org/ext/">{'Languages'|translate}</a></li>
                </ul>
            </div>
            <div class="col-md-15 col-xs-6">
              <h5>{'Support'|translate}</h5>
                <ul>
                  <li><a href="//{$PORG_DOMAIN_PREFIX}piwigo.org/forum/">{'Forum'|translate}</a></li>
                  <li><a href="//{$PORG_DOMAIN_PREFIX}piwigo.org/doc/">{'Documentation'|translate}</a> </li>
                  <li><a href="https://piwigo.org/guides">{'porg_guides'|translate}</a></li>
                  <li><a href="https://piwigo.org/get_started">{'Get Started'|translate}</a></li>
                </ul>
            </div>
            <div class="col-md-15 col-xs-6">
              <h5>{'About'|translate}</h5>
                <ul>
                  <li><a href="https://piwigo.org/about_us">{'About us'|translate}</a></li>
{*
                  <li><a href="https://piwigo.org/press">{'Press'|translate}</a></li>
*}
                  <li><a href="https://piwigo.org/get_involved">{'Get Involved'|translate}</a></li>
                </ul>
            </div>
            <div class="col-md-15 col-xs-6">
              <h5>{'News'|translate}</h5>
                <ul>
                  <li><a href="https://piwigo.org/news">{'What\'s new?'|translate}</a></li>
                  <li><a href="https://piwigo.org/newsletters">{'Newsletters'|translate}</a> </li>
                </ul>
            </div>
          </div>
          <div class="col-md-2 text-center">
            <div class="row">
              <div class="col-xs-12 footer-piwigo-logo">
                <a href="https://piwigo.org/"><img src="{$PEM_ROOT_URL_PLUGINS}images/porg.svg" alt="Piwigo.org" title="go to Piwigo.org home page"></a>
              </div>
              <div class="col-xs-12 footer-language">
                <li class="dropdown active">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-globe"></span>Languages</a>
                  <ul class="dropdown-menu">
{foreach from=$switch_languages item=switch_language}
                    <li><a href="{$switch_language.url}">{$switch_language.label}</a></li>
{/foreach}
                  </ul>
                </li>
              </div>
              <div class="footer-contact col-md-12 col-xs-6">
                <a href="https://piwigo.org/contact"><span class="glyphicon glyphicon-envelope"></span>{'Contact'|translate}</a>
              </div>
              <div class="footer-copyright col-md-12 col-xs-6">
                <p>Piwigo.org <span title="{$time}">©</span><br>2002 - {$CURRENT_YEAR}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
  </body>
</html>
