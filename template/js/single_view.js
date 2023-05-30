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

// function editToggleDisplay()
// {


//   const edit_switch = jQuery('#edit_mode');
//   console.log(edit_switch.checked)
//   if ($('#edit_switch').is(':checked')) {
//   // if(edit_switch.checked != false)
//     jQuery('.edit_mode').toggle();
//     console.log('true')
//   }
//   else if(edit_switch.checked == false)
//   {
//     console.log('false')
//   }
// }

jQuery("#edit_mode").change(function() {
  jQuery('.edit_mode').toggle();
  jQuery('.related_links').toggle();
});