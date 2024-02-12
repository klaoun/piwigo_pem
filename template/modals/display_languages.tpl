{* Modal to display list of languages for extension*}
<section id="displayLanguages-popin">
  <div>
    <div class="modal fade" id="displayLanguagesModal" tabindex="-1" aria-labelledby="displayLanguagesModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="displayLanguagesModalLabel"><i class="icon-language"></i> {'Available languages'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <ul class="list-group list-group-flush">
{if !empty($ext_languages)}
  {foreach from=$ext_languages item=lang}
              <li class="list-group-item col-4 text-start">{$lang.name}</li>
  {/foreach}
{/if}
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>