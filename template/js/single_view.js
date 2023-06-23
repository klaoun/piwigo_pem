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


// Selectize modal inputs
jQuery('.extension_author_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_tag_select').selectize({
  plugins: ["remove_button"],
})

jQuery("#extension_category").selectize();
