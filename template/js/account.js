jQuery("#edit_mode").change(function() {
  jQuery('.edit_mode').toggle();
});

// Selectize modal inputs
jQuery('.extension_author_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_tag_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_lang_desc_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_category_select').selectize();

jQuery(document).ready(function () {

  const data_extensions = jQuery('#profile tbody').attr("data-extensions")
  var extensions = jQuery.parseJSON(data_extensions)
  console.log(extensions)
  fillTable(extensions);
});

function sortExtensions(sortBy)
{
  jQuery("tbody > *:not('#jango_fett')").remove();

  jQuery('table.table th i.icon-chevron-up').removeClass('icon-chevron-up').addClass('icon-chevron-down')
  
  // jQuery('table.table th.'+sortBy+ ' i').toggleClass('icon-chevron-down')
  // jQuery('table.table th.'+sortBy+ ' i').toggleClass('icon-chevron-up')

  const data_extensions = jQuery('#profile tbody').attr("data-extensions")
  var sortedExtensions = jQuery.parseJSON(data_extensions)
  // jQuery(sortedExtensions).each(function()
  // {
  //   this.compatibility_last = parseFloat(this.compatibility_last);
  //   this.publish_date_not_formatted = new Date(this.publish_date_not_formatted).getTime();
  // });

  // if(jQuery('icon-chevron-down' == 'table.table th.'+sortBy+ ' i').attr("class"))
  // {
  //   sortedExtensions.sort(function(obj1,obj2) {
  //     if("date" == sortBy)
  //     {  
  //       return (new Date(obj1[sortBy]) - new Date(obj2[sortBy]));   
  //     }
  //     else
  //     {
  //       return (obj1[sortBy] - obj2[sortBy]);   
  //     }
  //   })
  // }
  // else if ('icon-chevron-up' == jQuery('table.table th.'+sortBy+ ' i').attr("class"))
  // {
  //   sortedExtensions.sort(function(obj1,obj2) {
  //     if("date" == sortBy)
  //     {  
  //       return (new Date(obj2[sortBy]) - new Date(obj1[sortBy]));
  //     }
  //     else
  //     {
  //       return (obj2[sortBy] - obj1[sortBy]);
  //     }
  //   })
  // }
  
  fillTable(sortedExtensions)
}


function fillTable(extensions)
{
  // Foreach extension clone the empty extension div and fill it with the extension information
  jQuery(extensions).each(function()
  {
    var extension_id = this.id;
    var clone = jQuery("#jango_fett").clone().removeAttr("id").attr("id","extension_"+extension_id)
    clone.appendTo("#profile tbody");

    // for extension category
    switch(this.category) {
      case "Plugin":
        jQuery('#extension_' + extension_id + ' td.category').append('<span class="pem-tooltip"><i class="icon-puzzle-piece"></i><span class="pem-tooltiptext">'+this.category+'</span></span>')
        break;
      case "Theme":
        jQuery('#extension_' + extension_id + ' td.category').append('<span class="pem-tooltip"><i class="icon-palette"></i><span class="pem-tooltiptext">'+this.category+'</span></span>')
        break;
      case "Tool":
        jQuery('#extension_' + extension_id + ' td.category').append('<span class="pem-tooltip"><i class="icon-screwdriver-wrench"></i><span class="pem-tooltiptext">'+this.category+'</span></span>')
      break;
      case "Language":
        jQuery('#extension_' + extension_id + ' td.category').append('<span class="pem-tooltip"><i class="icon-language"></i><span class="pem-tooltiptext">'+this.category+'</span></span>')
        break;
    }

    // for extension name
    jQuery('#extension_' + extension_id + ' td.name').append('\
      <a class="link orange-link" href="'+PEM_ROOT_URL+'index.php?eid='+extension_id+'">'+this.name+'</a>'
    );
    
    // for extension age
    if (this.publish_date)
    {
      jQuery('#extension_' + extension_id + ' td.age').append('\
      <span class="ms-0 badge blue-badge pem-tooltip">\
        <i class="icon-clock me-1"></i>'+this.age+'\
        <span class="pem-tooltiptext">'+this.publish_date+'</span>\
      </span>');
    }

    // Last update date
    if (this.last_updated)
    {
      jQuery('#extension_' + extension_id + ' td.last_updated').append(this.last_updated);
    }

    // for compatibility badges
    if (this.compatibility_first && this.compatibility_last)
    {
      jQuery('#extension_' + extension_id + ' td.compatibilty').append(' \
      <span class="compatibility compatibility-first">\
        <i class="icon-code-branch"></i>'+this.compatibility_first+'\
      </span>\
      <i class="icon-arrow-right"></i>\
      <span class="compatibility compatibility-last">\
        <i class="icon-code-branch"></i>'+this.compatibility_last+'\
      </span>\
      ');
    }

    // Last update date
    if (this.nb_downloads > 0)
    {
      jQuery('#extension_' + extension_id + ' td.nb_downloads').append(this.nb_downloads);
    }

    // For rating
    if (this.rating_score)
    {
      jQuery('#extension_' + extension_id + ' td.rating').append(this.rating_score);
    }
  });
}
