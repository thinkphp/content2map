var geolocatedtext = function(){

    var config = {
      
        mapkey: null,
        colormarker: 'blue',
        width: 700,
        height: 200 
    };

    var elem,text,data;

    function init(id){

        if(!document.getElementById) {return;}

        elem = document.getElementById(id);

        if(!elem) {return;}

        if(elem) {

           text = elem.innerHTML;

           var url = 'http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20FROM%20geo.placemaker%20WHERE%20documentContent%20%3D%20%22'+encodeURIComponent(text)+'%22%20AND%20documentType%3D%22text%2Fplain%22%20AND%20appid%20%3D%20%22%22&format=json&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=geolocatedtext.seed';

           var s = document.createElement('script');

               s.setAttribute('type','text/javascript')

               s.setAttribute('src',url);

               document.getElementsByTagName('head')[0].appendChild(s);
        }
    };

    function paint(o) {

         var results = o.query.results.matches.match;

         geolocatedtext.data = results;

         var markers = '', locs = '<table><thead><th>ID</th><th>Name</th><th>Type</th><th>woeid</th><th>latitude</th><th>longitude</th></thead><tbody>';

         if(results.length > 1) {

                for(var i=0;i<results.length;i++) {

                    var found = results[i].reference.text;
 
                    var name = results[i].place.name;

                    var type = results[i].place.type;

                    var woeid = results[i].place.woeId;

                    var latitude = results[i].place.centroid.latitude;

                    var longitude = results[i].place.centroid.longitude;

                        markers += '&markers=color:'+config.colormarker+'|label:'+(i+1)+'|'+latitude+','+longitude;

                        locs += '<tr><td>'+(i+1)+'</td><td>'+name+'</td><td>'+type+'</td><td>'+woeid+'</td><td>'+latitude+'</td><td>'+longitude+'</td></tr>';

                   text = text.replace(found,'<a href="http://maps.google.com/maps?q='+name+'">'+found+'</a>');

                }//end for

         } else if(results) {

                    var found = results.reference.text;
 
                    var name = results.place.name;

                    var type = results.place.type;

                    var woeid = results.place.woeId;

                    var latitude = results.place.centroid.latitude;

                    var longitude = results.place.centroid.longitude;

                        markers += '&markers=color:'+config.colormarker+'|label:'+(1)+'|'+latitude+','+longitude;

                        locs += '<tr><td>'+(1)+'</td><td>'+name+'</td><td>'+type+'</td><td>'+woeid+'</td><td>'+latitude+'</td><td>'+longitude+'</td></tr>';

                   text = text.replace(found,'<a href="http://maps.google.com/maps?q='+name+'">'+found+'</a>');
         }


                var src = 'http://maps.google.com/maps/api/staticmap?sensor=false'+

                          '&size='+config.width+'x'+config.height+'&maptype=roadmap'+

                          '&key='+config.mapkey + markers;
                    

                locs +='</tbody></table>';

                var badge = '<div class="locations">'+locs+'<img src="'+src+'" alt="map">'+

                             '<p class="branding">Powered By: Google Maps, Yahoo Placemakers and YQL</p></div>';

                elem.innerHTML = text;

                elem.innerHTML += badge;

    }

    return {analyse: init,seed:paint,config: config,data:data}; 
}();