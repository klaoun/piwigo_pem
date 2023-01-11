jQuery(document).ready(function () {

  let extensonsTypes = ['plugin','theme','tool','language']
  let spotlightIds = [303,831,899,716]


  extensonsTypes.forEach(function(element, index) {
    getHighestRated(element);
    getMostDownloaded(element);
    getMostRecent(element);
    getSpotlighted(spotlightIds[index], element)
  });

});


function getHighestRated(extensionType) {
  var highestRated;
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.getHighestRated&extension_type='+extensionType,
    data: { ajaxload: 'true' },
    success: function (data) {
        if (data.stat == 'ok') {
          highestRated = data.result;
          $('#'+extensionType+' .highest-rated div.info-column').append(
            "<h5 class='extension-name'>"+highestRated.name+"</h5>\
            <p class='description'  >"+highestRated.description+"</p>"
          )

          if(extensionType == "plugin")
          {
            var ratingScore = parseInt((highestRated.rating_score/100), 10)
          }
          else{
            var ratingScore = highestRated.rating_score
          }

          $('#'+extensionType+' .highest-rated div.info-column').append("<div class='rating_score'>")
          for(var i = 0; i < ratingScore; i++) {
            $('#'+extensionType+' .highest-rated div.info-column').append("<i class='icon-star-solid'></i>")
          }
          $('#'+extensionType+' .highest-rated div.info-column').append("</div>")
        }
        else {
          console.log(data)
        }
    },
    error: function (e) {
        console.log(e);
    }
  });
}

function getMostDownloaded(extensionType) {
  var mostDownloaded;
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.getMostDownloaded&extension_type='+extensionType,
    data: { ajaxload: 'true' },
    success: function (data) {
        if (data.stat == 'ok') {
          mostDownloaded = data.result;
          $('#'+extensionType+' .most-downloaded div.info-column').append(
            "<h5 class='extension-name'>"+mostDownloaded.name+"</h5>\
            <p class='description'>"+mostDownloaded.description+"</p>"
          )

          $('#'+extensionType+' .most-downloaded div.info-column').append(
            "<div class='number_of_downlaods'>\
            <i class='icon-download-solid'> "+mostDownloaded.download_count+"</i>\
            </div>"
          )
        }
        else {
          console.log(data)
        }
    },
    error: function (e) {
        console.log(e);
    }
  });
}

function getMostRecent(extensionType) {
  var mostRecent;
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.getMostRecent&extension_type='+extensionType,
    data: { ajaxload: 'true' },
    success: function (data) {
        if (data.stat == 'ok') {
          mostRecent = data.result;
          $('#'+extensionType+' .most-recent div.info-column').append(
            "<h5 class='extension-name'>"+mostRecent.name+"</h5>\
            <p class='description'>"+mostRecent.description+"</p>"
          )

          $('#'+extensionType+' .most-recent div.info-column').append(
            "<div class='lat_updated'>\
             "+mostRecent.formatted_date+" <span class='badge blue-badge'>"+mostRecent.time_since+"</span>\
            </div>"
          )

        }
        else {
          console.log(data)
        }
    },
    error: function (e) {
        console.log(e);
    }
  });
}


function getSpotlighted(id, extensionType) {
  var spotlighted;
  jQuery.ajax({
    type: 'GET',
    dataType: 'json',
    async: false,
    url: 'ws.php?format=json&method=pem.extensions.getInfo&extension_id='+id,
    data: { ajaxload: 'true' },
    success: function (data) {
        if (data.stat == 'ok') {
          spotlighted = data.result[id];
          $('#'+extensionType+' .spotlight div.info-column').append(
            "<h5 class='extension-name'>"+spotlighted.name+"</h5>\
            <p class='description'>"+spotlighted.description+"</p>"
          )

        }
        else {
          console.log(data)
        }
    },
    error: function (e) {
        console.log(e);
    }
  });
}

