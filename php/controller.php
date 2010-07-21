<?php

  if(isset($_POST['documentContent']) && $_POST['documentContent'] != '') {

    /* set up your apikey from GOOGLE API */  
    $mapkey = 'ABQIAAAAijZqBZcz-rowoXZC1tt9iRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQQBCaF1R_k1GBJV5uDLhAKaTePyQ';

    /* grab your content sent as POST in paramenter 'content' and hold in 'text'*/
    $text = $_POST['documentContent'];

    /* endpoint for YQL service*/
    $url = 'http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20FROM%20geo.placemaker%20WHERE%20documentContent%20%3D%20%22'.urlencode($text).'%22%20AND%20documentType%3D%22text%2Fplain%22%20AND%20appid%20%3D%20%22%22&format=json&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

    /* get all data */
    $output = get($url);

    /* decode json data */
    $data = json_decode($output);

    /* get results */
    $results = $data->query->results->matches->match;

               $markers = '';

               $locs = '<table><thead><th>ID</th><th>Name</th><th>Type</th><th>woeid</th><th>latitude</th><th>longitude</th></thead><tbody>';
 
             //if we have more results then fetch each component */
             if(is_array($results) && $results) {

                 //for each result do it
                 for($i=0;$i<count($results);$i++) {

                         $found = $results[$i]->reference->text;
 
                         $name = $results[$i]->place->name;

                         $type = $results[$i]->place->type;

                         $woeid = $results[$i]->place->woeId;

                         $latitude = $results[$i]->place->centroid->latitude;
 
                         $longitude = $results[$i]->place->centroid->longitude;

                         $markers .= '&markers=color:blue|label:'.($i+1).'|'.$latitude.','.$longitude;

                         $locs .= '<tr><td>'.($i+1).'</td><td>'.$name.'</td><td>'.$type.'</td><td>'.$woeid.'</td><td>'.$latitude.'</td><td>'.$longitude.'</td></tr>';

                         $text = str_replace($found,'<a href="http://maps.google.com/maps?q='.$name.'">'.$found.'</a>',$text);
 
                 }

             //otherwise we have one result then assemble UL LI and execute
             } else if($data->query->results->matches) {

                         $found = $results->reference->text;
 
                         $name = $results->place->name;

                         $type = $results->place->type;

                         $woeid = $results->place->woeId;

                         $latitude = $results->place->centroid->latitude;
 
                         $longitude = $results->place->centroid->longitude;

                         $markers .= '&markers=color:blue|label:'.($i+1).'|'.$latitude.','.$longitude;
  
                         $locs .= '<tr><td>'.(1).'</td><td>'.$name.'</td><td>'.$type.'</td><td>'.$woeid.'</td><td>'.$latitude.'</td><td>'.$longitude.'</td></tr>';

                         $text = str_replace($found,'<a href="http://maps.google.com/maps?q='.$name.'">'.$found.'</a>',$text);
             }


                 /* assemble IMG with src and alt */
                 $url = 'http://maps.google.com/maps/api/staticmap?sensor=false'.

                           '&size=640x200&maptype=roadmap'.

                           '&key='.$mapkey . $markers;

                 $locs .= '</tbody></table>';

                 $badge = '<div class="locations">'. $locs . 

                             '<img src="'.$url.'" alt="Map">'.

                             '<p class="branding">Powered By: Google Maps, Yahoo Placemakers and YQL</p></div>';

    /* sent results */
    $out = '<div id="locations"><p>'.stripslashes($text).'</p>'.$badge.'</div>';

   } 

   //use cURL
   function get($url) {

          $ch = curl_init();

          curl_setopt($ch,CURLOPT_URL,$url);

          curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

          curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);

          $data = curl_exec($ch);

          curl_close($ch);  

          if(empty($data)) {

            return 'Error retrieve data, please try again.';

          } else {return $data;}   

    }//endfunction

?>