// Get url params
const queryString = window.location.search
const urlParams = new URLSearchParams(queryString);

const cId = urlParams.get('cId')
const page = urlParams.get('page')

jQuery(document).ready(function () {

  var getData= getExtensionList(cId);

  var extensions = getData['extensionList'];
  var nb_total_extensions = getData['nb_total_extensions'];
  var nb_total_displayed = getData['nb_total_displayed'];
 
  jQuery(extensions).each(function(){

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
    $.each(this.authors, function(key, value) {
      jQuery('#extension_'+extension_id+' .extension_authors').html("<p>"+value+"</p>");
    });

    //If extensions has rating score then display it
    if(this.rating_score != null){
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
      
  });

  //Define pagination depending on amount of plugins and which page we are on

  var nb_pages = nb_total_extensions % nb_total_displayed;
  var pagination_href = jQuery('.pagination #previous_page').attr('href')
  
  // jQuery(".page_buttons a").first().
  // jQuery('.page_buttons')

  if(page > 1 && page != nb_pages){
    jQuery('#previous_page').attr('href', pagination_href + 'cId=' + cId + '&page=' + (parseInt(page)-1))
    // aria-disabled="true" 
  }
  else if(page == 1){
    jQuery('#next_page').attr('href', pagination_href + 'cId=' + cId + '&page=' + (parseInt(page)+1))
    jQuery('#previous_page').replaceWith(jQuery('<span id="#previous_page"><i class="icon-chevron-left"></i></spn>'))
    // jQuery('#previous_page').attr("aria-disabled", true)
  }
  else if(page == nb_pages){
    jQuery('#next_page').replaceWith(jQuery('<span id="#next_page"><i class="icon-chevron-right"></i></span>'))
  }
  
  //First page number
  jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=1">1</a>')


  //Current page number with +1 and -1
  var previousPage = parseInt(page) -1;
  var nextPage = parseInt(page) + 1;

  if(previousPage - 1 > 1)
  {
    jQuery(".page_buttons").append('<span>...</span>')
  }

  if(parseInt(page) == 1)
  {
    console.log()
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + nextPage +'">' + nextPage + '</a>')
  }
  else if(parseInt(page) == nb_pages)
  {
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + previousPage +'">' + previousPage+ '</a>')
  }
  else
  {
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + previousPage +'">' + previousPage+ '</a>')
    jQuery(".page_buttons").append('<a class="page_number active" href="' + pagination_href + 'cId=' + cId + '&page=' + page +'">' + page + '</a>')
    jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + nextPage +'">' + nextPage + '</a>')
  }

  if(nb_pages - nextPage > 1)
  {
    jQuery(".page_buttons").append('<span>...</span>')
  }
  //Last page number
  jQuery(".page_buttons").append('<a class="page_number" href="' + pagination_href + 'cId=' + cId + '&page=' + nb_pages +'">' + nb_pages + '</a>')

});

function getExtensionList(cId) {
  var extensionInfos ;
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,

    url: 'ws.php?format=json&method=pem.categories.getExtensions&category_id='+cId,
    data: { ajaxload: 'true' },
    success: function (data) {
        if (data.stat == 'ok') {
          extensionInfos = {
            'extensionList' : data.result.revisions,
            'nb_total_displayed' : data.result.nb_total_displayed,
            'nb_total_extensions' : data.result.nb_total_extensions 
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

function toggleFilter(){
  jQuery('.extension_filters').toggle();
}