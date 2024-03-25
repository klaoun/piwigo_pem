// Get url params
const queryString = window.location.search
const urlParams = new URLSearchParams(queryString);

const cid = urlParams.get('cid')
const page = urlParams.get('page')

jQuery(document).ready(function () {
  // If change extension category reset filters in storage
  var storedcid = sessionStorage.getItem("cid");
  var filter_version = sessionStorage.getItem("filter_version") ? sessionStorage.getItem("filter_version") : null;
  var filter_authors = sessionStorage.getItem("filter_authors") ? sessionStorage.getItem("filter_authors") : null;
  var filter_tags = sessionStorage.getItem("filter_tags") ? sessionStorage.getItem("filter_tags") : null;
  
  if(sessionStorage.getItem("filter_search") != null && sessionStorage.getItem("filter_search").length > 2)
  {
    var filter_search = sessionStorage.getItem("filter_search") ? sessionStorage.getItem("filter_search") : null;
  }

  // if we change category page reset filters
  if(storedcid != cid){
    sessionStorage.clear()
  }

  // Selectize filters
  jQuery('.extension_tag_select').selectize({
    plugins: ["remove_button"],
    items:[filter_tags],
  })

  jQuery('.extension_author_select').selectize({
    plugins: ["remove_button"],
    items:[filter_authors]
  })
  
  // change filter values if there is any stored
  if(filter_version != null )
  {
    jQuery(".extension_version_select").val(filter_version).change()
    jQuery('.extension_filters').css("display", "block");
    jQuery('.filter_tab').addClass("toggled");
  }

  if(filter_authors != null)
  {
    jQuery(".extension_author_select").val(filter_authors).change()
    jQuery('.extension_filters').css("display", "block");
    jQuery('.filter_tab').addClass("toggled");
  }

  if(filter_tags != null)
  {
    jQuery(".extension_tag_select").val(filter_tags).change()
    jQuery('.extension_filters').css("display", "block");
    jQuery('.filter_tab').addClass("toggled");
  }

  if(filter_search != null && filter_search.length > 2)
  {
    jQuery("#cid-search").val(filter_search).change()
  }

  // Watch for sort order or filter changes
  jQuery("#sort_order").on('change', function () {
    updatePageParam()
    getExtensionList(cid);
  });

  jQuery(".extension_version_select").on('change', function () {
    var version = jQuery(".extension_version_select").val();
    sessionStorage.setItem("filter_version",version);
    updatePageParam()
    getExtensionList(cid);
  });

  jQuery(".extension_author_select").on('change', function () {
    var author_ids = [];
    jQuery('.extension_author_select').children( "option" ).each(function(){
      var value = $(this).val()
      author_ids.push(value)
    })
    sessionStorage.setItem("filter_authors",author_ids);
    updatePageParam()
    getExtensionList(cid);
  });

  jQuery(".extension_tag_select").on('change', function () {
    var tag_ids = [];
    jQuery('.extension_tag_select').children( "option" ).each(function(){
      var value = $(this).val()
      tag_ids.push(value)
    })
    sessionStorage.setItem("filter_tags",tag_ids);
    updatePageParam()
    getExtensionList(cid);
  });

  //For word search 
  jQuery("#cid-search").on('keyup', function(e) {
    emptyContent()
    updatePageParam()
    // Ajax request to get all extension information from specific category
    if (jQuery("#cid-search").val().length > 2)
    {
      $('.extensions_container').append('\
      <div class="d-flex justify-content-center">\
        <div class="spinner-border" role="status"></div>\
        <span class="sr-only ms-3 align-middle">Loading...</span>\
      </div>');

      var timeout = null;

      if (timeout) {  
        clearTimeout(timeout);
      }
      timeout = setTimeout(function() {
        getExtensionList(cid);
      }, 1000);

    }
    // if value of input is removed reset page to all extensions
    if (jQuery("#cid-search").val().length == 0)
    {
      getExtensionList(cid);
    }

    if (e.keyCode == 13) {  
      e.preventDefault(); 
    }  
    var search = jQuery("#cid-search").val();
    sessionStorage.setItem("filter_search",search);

  });
  
  // Call getExtensionList to populate list of extensions
  getExtensionList(cid);
  sessionStorage.setItem("cid",cid);
});

// Remove all displayed extensions to avoid having them twice displayed due to page reload
function emptyContent()
{
  // Empty container div to avoid adding extensions twice when filters are changed
  jQuery(".extensions_container > *:not('#jango_fett')").remove();
  jQuery(".extensions_container .spinner").remove();
  jQuery(".page_buttons > *").remove();
  jQuery('#previous_page').addClass('d-none').removeClass('d-inline-block');
  jQuery('#next_page').addClass('d-none').removeClass('d-inline-block');
  jQuery('.filter_tab h5 span').replaceWith('');
  jQuery('#filtered_extensions_number').text('');
}

// Called when filter is changed to set page to 1
function updatePageParam()
{
  const urlParams = new URLSearchParams(window.location.search);
  if(urlParams.get('page') != 1)
  {
    urlParams.set('page', '1');
    window.location.search = urlParams;
  }
}

// Used to create the string for the filters applied in the ajax request
function createFilterString()
{
  // Filter with version, author and tags
  var filters = '';

  var filter_version = sessionStorage.getItem("filter_version") ? sessionStorage.getItem("filter_version") : null;
  var filter_authors = sessionStorage.getItem("filter_authors") ? sessionStorage.getItem("filter_authors") : null;
  var filter_tags = sessionStorage.getItem("filter_tags") ? sessionStorage.getItem("filter_tags") : null;
  var filter_search = sessionStorage.getItem("filter_search") ? sessionStorage.getItem("filter_search") : null;

  // Get version value
  if(filter_version !== null && "all" !== filter_version){
    filters += '&filter_version=' + filter_version
  }

  // Get author(s) value
  if(filter_authors !== null){
  filters += '&filter_authors=' + filter_authors
  }

  // Get tag(s) value
  if(filter_tags !== null){
  filters += '&filter_tags=' + filter_tags
  }

  // Get user input value
  if(filter_search !== null){
  filters += '&filter_search=' + filter_search
  }

  return filters;
}

function getExtensionList(cid) {
  emptyContent()

  var sort_by = jQuery("#sort_order").find(":selected").val();
  var params = '&sort_by=' + sort_by
  var filters = createFilterString();

  if ( filters != null)
  {
    params = params + filters
  }

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
              .data("last_revision_name", this.revision_name)
              .data("last_revision_date", this.revision_date)
            ;

            //Fill extension card line with info
            jQuery('#extension_'+extension_id+' .extension_name').prepend(this.extension_name);

            if (this.certification == 3)
            {
              jQuery('#extension_'+extension_id+' .certification-blue').removeClass('d-none');
            }
            else if (this.certification == 2)
            {
              jQuery('#extension_'+extension_id+' .certification-orange').removeClass('d-none');
            }
            else if (this.certification == 1)
            {
              jQuery('#extension_'+extension_id+' .certification-grey').removeClass('d-none');
            }
            else if (this.certification == 0)
            {
              jQuery('#extension_'+extension_id+' .certification-ghost').removeClass('d-none');
            }

            //If extension has a revision
            if(this.revision_name != null)
            {
              jQuery('#extension_'+extension_id+' .revision_name').html('<i class="icon-code-branch"></i>'+ this.revision_name); 
            }


            //add authors, there can be multiple, that is the reason for the foreach
            $.each(this.authors, function(key, value) 
            {
              jQuery('#extension_'+extension_id+' .extension_authors').append( "<a class='badge green-badge my-1 d-inline-block hide-text-overflow-150 ms-0 me-2 ' href='"+PEM_ROOT_URL+"index.php?uid="+key+"'><i class='icon-user'></i>"+value+"</a>");
            });

            //If extensions has rating score then display it
            if(this.rating_score != null)
            {
              jQuery('#extension_'+extension_id+' .extension_score').html(this.rating_score_stars + "<span class='ms-2 align-middle'>"+this.rating_score + '</span>'); 
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
            jQuery('#extension_'+extension_id+' .extension_name_link').attr('href', _href + extension_id)
            
            // If extension has image then display it
            if(this.screenshot_url != null)
            {
              jQuery('#extension_'+extension_id+' .extension_image_div .image-background').append('\
                <img class="extension_image position-absolute vertical-horizontal-align" src="'+this.screenshot_url +'">'
              )
            }
            else
            {
              jQuery('#extension_'+extension_id+' .extension_image_div .image-background').append('\
                <i class="icon-image position-absolute vertical-horizontal-align"></i>'
              )
            }

            //Display if extension compatible with latest version of Piwigo
            if (this.compatible_latest_pwg_version == true)
            {
              jQuery('#extension_'+extension_id+' .piwigo-compatibility').append('<i class="icon-check green-font"></i><p class="card-text">Compatible with the latest version of Piwigo</p>')
            }
            else
            {
              jQuery('#extension_'+extension_id+' .piwigo-compatibility').append('<i class="icon-cross red-font"></i><p class="card-text">Not compatible with the latest version of Piwigo</p>')
            }
          });

          if ( data.result.nb_extensions_filtered != data.result.nb_total_extensions)
          {
            jQuery('#filtered_extensions_number').append('<b>'+ data.result.nb_extensions_filtered +'</b> '+ FILTERED_EXTENSIONS );
          }

          var extensions_per_page = data.result.extensions_per_page;
          var nb_extensions_filtered = data.result.nb_extensions_filtered;
          var nb_total_pages = Math.ceil(nb_extensions_filtered/extensions_per_page)

          //Define pagination depending on amount of extension and which page we are on
          var pagination_href = PEM_ROOT_URL + 'index.php?'

          jQuery('#previous_page').addClass('d-inline-block').removeClass('d-none');
          jQuery('#next_page').addClass('d-inline-block').removeClass('d-none');
          jQuery('.pagination').removeClass('d-none');

        
          // This is is to disable or not the pagination arrows
          if(1 == nb_total_pages)
          {
            // If nb pages = 1
            jQuery('#previous_page').replaceWith(jQuery('<span id="previous_page" class="disabled"><i class="icon-chevron-left"></i></span>'))
            jQuery('#next_page').replaceWith(jQuery('<span id="next_page" class="disabled"><i class="icon-chevron-right"></i></span>'))
          }
          if(page > 1 && page != nb_total_pages)
          {
            // If page is different from first or last
            jQuery('#previous_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)-1))
            jQuery('#next_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)+1))
          }
          else if(page == 1)
          {
            // If page is first
            jQuery('#next_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)+1))
            // Disable previous arrow
            jQuery('#previous_page').replaceWith(jQuery('<span id="previous_page" class="disabled"><i class="icon-chevron-left"></i></span>'))
          }
          else if(page == nb_total_pages)
          {
            // If page is last 
            jQuery('#previous_page').attr('href', pagination_href + 'cid=' + cid + '&page=' + (parseInt(page)-1))
            // disable next arrow
            jQuery('#next_page').replaceWith(jQuery('<span id="next_page" class="disabled"><i class="icon-chevron-right"></i></span>'))
          }

          // These display the different page numbers

          //First page number
          jQuery(".page_buttons").append('<a class="page_number" id="first_page_number" href="' + pagination_href + 'cid=' + cid + '&page=1">1</a>')

          //Current page number with +1 and -1
          var previousPage = parseInt(page) -1;
          var nextPage = parseInt(page) + 1;

          // Add ... when there is more than one number between current page and first
          if(previousPage - 1 > 1 && nb_total_pages > 4)
          {
            jQuery(".page_buttons").append('<span>...</span>')
          }

          if(1 !== nb_total_pages)
          {
            // If used to display pages number depeding on which page we are on
            if(1 == page )
            {
              // If page is first page
              jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nextPage +'">' + nextPage + '</a>')  

              if(nb_total_pages > 3){
                jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + (nextPage+1) +'">' + (nextPage+1) + '</a>')  
              }

              // display current page style
              jQuery('#first_page_number').addClass('active')
            }
            else if(page == nb_total_pages)
            {
              // If page is last
              if(nb_total_pages > 3){
                jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + (previousPage-1) +'">' + (previousPage - 1)+ '</a>')
              }
              jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + previousPage +'">' + previousPage+ '</a>')
            }
            else if(page == 2 && nb_total_pages == 3)
            {
              // If page is second and total pages = 3, avoid displaying the last page number twice
              jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
            }
            else if(page == 2)
            {
              // If page is second
              jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
              // jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nextPage +'">' + nextPage + '</a>')
            }
            else if(nb_total_pages - page == 1)
            {
              // If page before last (n-1)
              jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + previousPage +'">' + previousPage+ '</a>')
              jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
            }
            else
            {
              // All other pages
              jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + previousPage +'">' + previousPage+ '</a>')
              jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cid=' + cid + '&page=' + page +'">' + page + '</a>')
              jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nextPage +'">' + nextPage + '</a>')
            }
          }
          
          // Add ... when there is more than one number between current page and last
          if(nb_total_pages - nextPage > 1  && nb_total_pages > 4)
          {
            jQuery(".page_buttons").append('<span>...</span>')
          }
          
          // If number of pages bigger than 2
          if(nb_total_pages > 2)
          {
            jQuery(".page_buttons").append('<a class="page_number" id="last_page_number" href="' + pagination_href + 'cid=' + cid + '&page=' + nb_total_pages +'">' + nb_total_pages + '</a>')
          }

          if(page == nb_total_pages)
          {
            // If page is last (n) display current page style
            jQuery('#last_page_number').addClass('active')
          }
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
