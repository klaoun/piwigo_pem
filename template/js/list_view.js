// Get url params
const queryString = window.location.search
const urlParams = new URLSearchParams(queryString);

const cid = urlParams.get('cid')
const page = urlParams.get('page')

jQuery(document).ready(function () {
  
  console.log(jQuery(".extension_version_select").val())

  
  if(sessionStorage.getItem("filter_version"))
  {
    var filter_version = sessionStorage.getItem("filter_version")
    // console.log(filter_version)
    // jQuery(".extension_version_select").val(filter_version).change()
  }

  // Call getExtensionList to populate list of extensions
  getExtensionList(cid);
});

// Watch for sort or filter changes
jQuery("#sort_order").on('change', function () {
  updatePageParam()
  getExtensionList(cid);
});

jQuery(".extension_version_select").on('change', function () {
  updatePageParam()
  getExtensionList(cid);
});

jQuery(".extension_author_select").on('change', function () {
  updatePageParam()
  getExtensionList(cid);
});

jQuery(".extension_tag_select").on('change', function () {
  updatePageParam()
  getExtensionList(cid);
});


jQuery("#cid-search").keydown(function (e) {
  updatePageParam()
  if (jQuery("#cid-search").val().length > 2)
  {
    emptyContent()
    $('.extensions_container').append('\
    <div class="d-flex justify-content-center">\
      <div class="spinner-border" role="status"></div>\
      <span class="sr-only ms-3 align-middle">Loading...</span>\
    </div>');

    getExtensionList(cid);
  }

  if(e.keyCode == 13){
    e.preventDefault()
  }
});

function emptyContent()
{
  // Empty container div to avoid adding extensions twice when filters are changed
  jQuery(".extensions_container > *:not('#jango_fett')").remove();
  jQuery(".extensions_container .spinner").remove();
  jQuery(".page_buttons > *").remove();
  jQuery('#previous_page').addClass('d-none').removeClass('d-inline-block')
  jQuery('#next_page').addClass('d-none').removeClass('d-inline-block')
}

function updatePageParam()
{
  
  const urlParams = new URLSearchParams(window.location.search);
  if(urlParams.get('page') != 1)
  {
    urlParams.set('page', '1');
    window.location.search = urlParams;
  }
  // console.log(urlParams)
}

function getFilterValues()
{

  // Filter with version, author and tags
  var filters = '';
  var version = jQuery(".extension_version_select").val();
  if (version != "all")
  {
    filters += '&filter_version=' + version
    
    sessionStorage.setItem("filter_version",version);
  }

  var author_ids = [];
  jQuery('.extension_author_select').children( "option" ).each(function(){
    var value = $(this).val()
    author_ids.push(value)
  })

  if (author_ids.length !== 0)
  {
    filters += '&filter_authors=' + author_ids

    sessionStorage.setItem("filter_authors",author_ids);
  }

  var tag_ids = [];
  jQuery('.extension_tag_select').children( "option" ).each(function(){
    var value = $(this).val()
    tag_ids.push(value)
  })

  if (tag_ids.length !== 0)
  {
    filters += '&filter_tags=' + tag_ids

    sessionStorage.setItem("filter_tags",tag_ids);
  }

  var search = jQuery("#cid-search").val();
  
  if (search != null && search.length > 2)
  {
    console.log(search)
    filters += '&filter_search=' + search

    sessionStorage.setItem("filter_search",search);
  }

  return filters;
}

// Ajax request to get all extension information from specific category
function getExtensionList(cid) {
  emptyContent()

  var sort_by = jQuery("#sort_order").find(":selected").val();
  var params = '&sort_by=' + sort_by
  var filters = getFilterValues();

  if ( filters != null)
  {
    // if(page != 1){
    //   page = 1
    // }
    params = params + filters
  }

  // console.log(params)

  $('.extensions_container').append('\
  <div class="d-flex justify-content-center">\
    <div class="spinner-border" role="status"></div>\
    <span class="sr-only ms-3 align-middle">Loading...</span>\
  </div>');

  var extensionInfos;

  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.getList&category_id=' + cid + '&page=' + page + params,
    data: { ajaxload: 'true' },
    success: function (data) {
      if (data.stat == 'ok') {
        var extensions = data.result.revisions;
      
        // Empty container div to avoid adding extensions twice when filters are changed
        jQuery(".extensions_container > *:not('#jango_fett')").remove();
        jQuery(".page_buttons > *").remove();

        // If there are no extensions
        if (!extensions)
        {
          jQuery(".extensions_container .spinner").remove();
          jQuery('.pagination').addClass('d-none').removeClass('d-inline-block');

          $('.extensions_container').append(PEM_NO_EXTENSIONS)
        }
        // If there is any extensions  
        else
        {
          jQuery(".extensions_container .spinner").remove();

          // Foreach extension clone the empty extension div and fill it with the extension information
          jQuery(extensions).each(function()
          {
            var extension_id = this.extension_id;
            var clone = jQuery("#jango_fett").clone().removeAttr("id").attr("id","extension_"+extension_id)
            clone.appendTo(".extensions_container");
            // jQuery("#jango-fett").clone().removeAttr("id").attr("id","request"+this.id).appendTo(".table-body")

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

            //add authors, there can be multiple, that is the reason for the foreach
            $.each(this.authors, function(key, value) 
            {
              jQuery('#extension_'+extension_id+' .extension_authors').append( "<a class='link' href='"+PEM_ROOT_URL+"index.php?uid="+key+"'>"+value+"</a>");
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
              jQuery('#extension_'+extension_id+' .extension_image_div').append('\
                <img class="img-fluid extension_image" src="'+this.screenshot_url +'">'
              )
            }
            else
            {
              jQuery('#extension_'+extension_id+' .extension_image_div').append('\
                <img class="img-fluid extension_image placeholder_image" src="'+ PEM_ROOT_URL_PLUGINS +'images/image-solid.svg">'
              )
            }
          });

          var extensions_per_page = data.result.extensions_per_page;
          // console.log("extensions_per_page = " +extensions_per_page)

          var nb_extensions_filtered = data.result.nb_extensions_filtered;
          // console.log("nb_extensions_filtered = " +nb_extensions_filtered)

          var nb_total_extensions = parseInt(data.result.nb_total_extensions);
          // console.log("nb_total_extensions = " +nb_total_extensions)

          var nb_total_pages = Math.ceil(nb_extensions_filtered/extensions_per_page)
          // console.log("nb_total_pages = "+ nb_total_pages)

          if(nb_total_pages < 4)
          {

          }
          else
          {

          }

          //Define pagination depending on amount of plugins and which page we are on
          var pagination_href = PEM_ROOT_URL + 'index.php?'



          // jQuery('#previous_page').addClass('d-inline-block').removeClass('d-none')
          // jQuery('#next_page').addClass('d-inline-block').removeClass('d-none')

          // if(page > 1 && page != nb_pages)
          // {
          //   // If page is different from first or last
          //   jQuery('#previous_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)-1))
          //   jQuery('#next_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)+1))
          // }
          // else if(page == 1)
          // {
          //   // If page is first
          //   jQuery('#next_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)+1))
          //   // Disable previous arrow
          //   jQuery('#previous_page').replaceWith(jQuery('<span id="#previous_page" class="disabled"><i class="icon-chevron-left"></i></spn>'))
          // }
          // else if(page == nb_pages)
          // {
          //   // If page is last 
          //   jQuery('#previous_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)-1))
          //   // disable next arrow
          //   jQuery('#next_page').replaceWith(jQuery('<span id="#next_page" class="disabled"><i class="icon-chevron-right"></i></span>'))
          // }
          
          // //First page number
          // jQuery(".page_buttons").append('<a class="page_number" id="first_page_number" href="' + pagination_href + 'cid=' + cid + '&page=1">1</a>')

          // //Current page number with +1 and -1
          // var previousPage = parseInt(page) -1;
          // var nextPage = parseInt(page) + 1;

          // // Add ... when there is more than one number between current page and first
          // if(previousPage - 1 > 1)
          // {
          //   jQuery(".page_buttons").append('<span>...</span>')
          // }

          // // If used to display pages number depeding on which page we are on
          // if(1 == page)
          // {
          //   // If page is first
          //   jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nextPage +'">' + nextPage + '</a>')  
          //   jQuery('#first_page_number').addClass('active')
          // }
          // else if(page == nb_pages)
          // {
          //   // If page is last
          //   jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + previousPage +'">' + previousPage+ '</a>')
          // }
          // else if(page == 2 && nb_pages == 3)
          // {
          //   // If page is second and total pages = 3, avoid displaying the last page number twice
          //   jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
          // }
          // else if(page == 2)
          // {
          //   // If page is second
          //   jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
          //   jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nextPage +'">' + nextPage + '</a>')
          // }
          // else if(nb_pages - page == 1)
          // {
          //   // If page before last
          //   jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + previousPage +'">' + previousPage+ '</a>')
          //   jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
          // }
          // else
          // {
          //   // All other pages
          //   jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + previousPage +'">' + previousPage+ '</a>')
          //   jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
          //   jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nextPage +'">' + nextPage + '</a>')
          // }
          
          // // Add ... when there is more than one number between current page and last
          // if(nb_pages - nextPage > 1)
          // {
          //   jQuery(".page_buttons").append('<span>...</span>')
          // }

          // // last page number
          // jQuery(".page_buttons").append('<a class="page_number" id="last_page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nb_pages +'">' + nb_pages + '</a>')
          // if(page == nb_pages)
          // {
          //   // If page is last
          //   jQuery('#last_page_number').addClass('active')
          // }
        }

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

// Selectize filters
jQuery('.extension_tag_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_author_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_version_select').selectize()
