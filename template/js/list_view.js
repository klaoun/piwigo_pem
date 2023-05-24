// Get url params
const queryString = window.location.search
const urlParams = new URLSearchParams(queryString);

const cId = urlParams.get('cId')
const page = urlParams.get('page')

jQuery(document).ready(function () {

  var getData= getExtensionList(cId, page);

  var extensions = getData['extensionList'];
  var nb_pages = getData['nb_pages'];

 
  // Foreach extension clone the empty extension div and fill it with the extension information
  jQuery(extensions).each(function()
  {
    var extension_id = this.extension_id;
    var clone = jQuery("#jango_fett").clone().removeAttr("id").attr("id","extension_"+extension_id)
    clone.appendTo(".extensions_container");
    jQuery("#jango-fett").clone().removeAttr("id").attr("id","request"+this.id).appendTo(".table-body")

    jQuery('#extension_'+extension_id)
      .data("id", extension_id)
      .data("extension_name", this.extension_name)
      .data("authors", this.authors)
      .data("rating_score", this.rating_score)
      .data("downloads", this.downloads)
      .data("about", this.about)
    ;

    //Fill extension card line with info
    jQuery('#extension_'+extension_id+' .extension_name').text(this.extension_name);

    //add authors, ther can be multiple, that is the reason for the foreach
    $.each(this.authors, function(key, value) 
    {
      jQuery('#extension_'+extension_id+' .extension_authors').html("<p>"+value+"</p>");
    });

    //If extensions has rating score then display it
    if(this.rating_score != null)
    {
      jQuery('#extension_'+extension_id+' .extension_score').html(this.rating_score_stars + this.rating_score); 
    }
    jQuery('#extension_'+extension_id+' .extension_number_downloads').text(this.downloads);

    // If description is to long, shorten it 
    var len = this.about.length;

    if(len>200)
    {
      var description = this.about.substr(0,200)+'...'
      jQuery('#extension_'+extension_id+' .extension_description').html(description);
    }
    else{
      jQuery('#extension_'+extension_id+' .extension_description').html(this.about);
    }
    var _href = jQuery('#extension_'+extension_id+' .more_info_link').attr('href')
    jQuery('#extension_'+extension_id+' .more_info_link').attr('href', _href + extension_id)
    
    // If extension has image then display it
    if(this.screenshot_url != null)
    {
      jQuery('#extension_'+extension_id+' .extension_description_container').removeClass('col-9').addClass('col-5')
      jQuery('#extension_'+extension_id+' .card-body').prepend('\
        <div class="col-4 text-center">\
          <img class="img-fluid extension_image" src="'+this.screenshot_url +'">\
        </div>'
      )
    }

    // Change state of tag filter

    var checkLabel = document.querySelector("input[type=checkbox]");
    checkLabel.addEventListener("click", function() {
      if (checkLabel.checked) {
        console.log("turnOn");
      } else {
        console.log("turnOff");
      }
    })

  });

  //Define pagination depending on amount of plugins and which page we are on
  var pagination_href = jQuery('.pagination #previous_page').attr('href')

  if(page > 1 && page != nb_pages)
  {
    // If page is different from first or last
    jQuery('#previous_page').attr('href', pagination_href + 'cId=' + cId + '&page=' + (parseInt(page)-1))
    jQuery('#next_page').attr('href', pagination_href + 'cId=' + cId + '&page=' + (parseInt(page)+1))
  }
  else if(page == 1)
  {
    // If page is first
    jQuery('#next_page').attr('href', pagination_href + 'cId=' + cId + '&page=' + (parseInt(page)+1))
    // Disable previous arrow
    jQuery('#previous_page').replaceWith(jQuery('<span id="#previous_page" class="disabled"><i class="icon-chevron-left"></i></spn>'))
  }
  else if(page == nb_pages)
  {
    // If page is last 
    jQuery('#previous_page').attr('href', pagination_href + 'cId=' + cId + '&page=' + (parseInt(page)-1))
    // disable next arrow
    jQuery('#next_page').replaceWith(jQuery('<span id="#next_page" class="disabled"><i class="icon-chevron-right"></i></span>'))
  }
  
  //First page number
  jQuery(".page_buttons").append('<a class="page_number" id="first_page_number" href="' + pagination_href + 'cId=' + cId + '&page=1">1</a>')

  //Current page number with +1 and -1
  var previousPage = parseInt(page) -1;
  var nextPage = parseInt(page) + 1;

   // Add ... when there is more than one number between current page and first
  if(previousPage - 1 > 1)
  {
    jQuery(".page_buttons").append('<span>...</span>')
  }

  // If used to display pages number depeding on which page we are on
  if(1 == page)
  {
    // If page is first
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + nextPage +'">' + nextPage + '</a>')  
  }
  else if(page == nb_pages)
  {
    // If page is last
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + previousPage +'">' + previousPage+ '</a>')
  }
  else if(page == 2 && nb_pages == 3)
  {
    // If page is second and total pages = 3, avoid displaying the last page number twice
    jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cId=' + cId + '&page=' + page +'">' + page + '</a>')
  }
  else if(page == 2)
  {
    // If page is second
    jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cId=' + cId + '&page=' + page +'">' + page + '</a>')
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + nextPage +'">' + nextPage + '</a>')
  }
  else if(nb_pages - page == 1)
  {
    // If page before last
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + previousPage +'">' + previousPage+ '</a>')
    jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cId=' + cId + '&page=' + page +'">' + page + '</a>')
  }
  else
  {
    // All other pages
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + previousPage +'">' + previousPage+ '</a>')
    jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cId=' + cId + '&page=' + page +'">' + page + '</a>')
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + nextPage +'">' + nextPage + '</a>')
  }
  
  // Add ... when there is more than one number between current page and last
  if(nb_pages - nextPage > 1)
  {
    jQuery(".page_buttons").append('<span>...</span>')
  }
 
  jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + nb_pages +'">' + nb_pages + '</a>')

});

// Ajax request to get all extension information from specifique category
function getExtensionList(cId,page) {
  var extensionInfos ;
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,

    url: 'ws.php?format=json&method=pem.categories.getExtensions&category_id='+cId+'&page='+page,
    data: { ajaxload: 'true' },
    success: function (data) {
        if (data.stat == 'ok') {
          extensionInfos = {
            'extensionList' : data.result.revisions,
            'nb_pages' : data.result.nb_pages,
          }
        }
        else {
          console.log(data)
        }
    },
    error: function (e) {
        console.log(e);
    }
  });
  return extensionInfos;
}

// Toggle for filter section
function toggleFilter(){
  jQuery('.extension_filters').toggle();
  jQuery('.filter_tab ').toggleClass('toggled');
}