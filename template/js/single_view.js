function display_changelog( revision_id )
{
  var element = document.getElementById( 'changelog_' + revision_id );
  
  if( element.style.display == 'none' )
  {
    element.style.display = 'block';
  }
  else
  {
    element.style.display = 'none';
  }
}

function revToggleDisplay(headerId, contentId)
{
  var revHeader = document.getElementById(headerId);
  var revContent = document.getElementById(contentId);

  if (revContent.style.display == 'none')
  {
    revContent.style.display = 'block';
    revHeader.className = 'changelogRevisionHeaderExpanded pb-3';

    var revArrow = jQuery('#'+headerId+' i.icon-chevron-right');
    revArrow.removeClass('icon-chevron-right')
    revArrow.addClass('icon-chevron-down')
  }
  else
  {
    revContent.style.display = 'none';
    revHeader.className = 'changelogRevisionHeaderCollapsed pb-0';

    var revArrow = jQuery('#'+headerId+' i.icon-chevron-down');
    revArrow.addClass('icon-chevron-right')
    revArrow.removeClass('icon-chevron-down')
  }
}

jQuery("#edit_mode").change(function() {
  jQuery('.edit_mode').toggle();
  jQuery('.related_links').toggle();
});

jQuery(document).ready(function () {
// Selectize modal inputs
  jQuery('.extension_author_select').selectize()

  jQuery('.extension_tag_select').selectize({
    plugins: ["remove_button"],
  })
  
  jQuery('.extension_lang_desc_select').selectize({
    plugins: ["remove_button"],
  })

  jQuery('.revision_compatible_versions').selectize({
    plugins: ["remove_button"],
  })

  jQuery('#extensions_languages').selectize({
    plugins: ["remove_button"],
  })
  
});


function showOnlyThisChild(parentId, childIdtoShow)
{
  var parent = document.getElementById(parentId);
  var children = parent.childNodes;
  var n = children.length;

  for (i=0; i<n; i++)
  {
    var child = children[i];
    if (child.id != undefined)
    {
      if (child.id == childIdtoShow)
      {
        child.style.display = 'block';
      }
      else
      {
        child.style.display = 'none';
      }
    }
  }
}

function deleteAuthor(userId, extensionId)
{
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.deleteAuthor&extension_id=' + extensionId + '&user_id=' + userId ,
    data: { ajaxload: 'true' },
    success: function (data) {
      if (data.stat == 'ok') {
        localStorage.setItem("message",data.message)
        window.location.reload(); 
      }
    }
  });

}

function setOwner(userId, extensionId)
{
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=php&method=pem.extensions.setOwner&extension_id=' + extensionId + '&user_id=' + userId ,
    data: { ajaxload: 'true' },
    success: function (data) {
      if (data.stat == 'ok') {
        localStorage.setItem("message",data.message)
        window.location.reload(); 
      }
    }
  });

//Ajax requet to delete SVN/Git config
function deleteSVNGitConfig(extensionId){
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.deleteSvnGitConfig&extension_id=' + extensionId ,
    data: { ajaxload: 'true' },
function deleteExtension(extensionId, link)
{
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.deleteExtension&extension_id=' + extensionId ,
    data: { ajaxload: 'true' },
    success: function (data) {
      if (data.stat == 'ok') {
        window.location.replace(link)
      }
    }
  });
}

function deleteExtension(extensionId)
{
  console.log("delete extension")
}

const editLinkModal = document.getElementById('editLinkModal');

editLinkModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget
  // Extract info from data-bs-* attributes
  const linkId = button.getAttribute('data-bs-link-id')
  const linkName = button.getAttribute('data-bs-link-name')
  const linkURL = button.getAttribute('data-bs-link-url')
  const linkLang = button.getAttribute('data-bs-link-lang')
  // Update the modal's content.
  const modalLinkID= editLinkModal.querySelector('#link_id')
  const modalLinkName = editLinkModal.querySelector('#link_name')
  const modalLinkUrl= editLinkModal.querySelector('#link_url')
  const modalLinkLang= editLinkModal.querySelector('#link_lang')

  modalLinkID.value = linkId
  modalLinkName.value = linkName
  modalLinkUrl.value = linkURL
  modalLinkLang.value = linkLang

});