<?php
     //use cURL
     function get($posturl,$postvars) {

              $ch = curl_init($posturl);

              curl_setopt($ch,CURLOPT_POST, 1);

              curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);

              curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 

              $buffer = curl_exec($ch);

              return $buffer;
        
     }//end function


  if(isset($_POST['documentContent']) && $_POST['documentContent'] != '') {

    /* set up your apikey from GOOGLE API */  
    $mapkey = 'ABQIAAAAijZqBZcz-rowoXZC1tt9iRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQQBCaF1R_k1GBJV5uDLhAKaTePyQ';

    $text = filter_input(INPUT_POST,'documentContent',FILTER_SANITIZE_ENCODED);
             
    $key = 'q0pcWFLIkY77xr0DLfxcK04QfkBMGvEe'; 

    define('POSTURL','http://wherein.yahooapis.com/v1/document');

    define('POSTVARS','appid='.$key.'&documentContent='.$text.'&documentType=text/html&outputType=xml');

    $xml = get(POSTURL,POSTVARS); 

    $places = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

    $text = rawurldecode($text);

    $foundplaces = array();

                 if($places->document->placeDetails) {

                           foreach($places->document->placeDetails as $p) {

                                   $woeid = 'woeid'.$p->place->woeId;

                                   $foundplaces[$woeid] = array('name'=>$p->place->name.'',

                                                               'type'=>$p->place->type.'',

                                                               'woeid'=>$p->place->woeId.'',

                                                               'lat'=>$p->place->centroid->latitude.'',

                                                               'lon'=>$p->place->centroid->longitude.'');
                           }//end foreach

                 }//end if

                 /* loop over the reference */
                 if($places->document->referenceList->reference) {

                           $history = array(); $i = 0;

                           $locs = '<table><thead><th>ID</th><th>Name</th><th>Type</th><th>woeid</th><th>latitude</th><th>longitude</th></thead><tbody>';

                           foreach($places->document->referenceList->reference as $r) {
 
                                   $woeid = $r->woeIds;

                                   foreach($woeid as $wi) {

                                           if(!in_array($wi,$history)) {

                                                 $history[] = $wi.'';

                                                 $currentLocation = $foundplaces['woeid'.$wi];

                                                 /* check if all data exists in vector */
                                                 if($currentLocation['name'] != '' && $currentLocation['lat'] != '' && $currentLocation['lon'] != '') {

                                                        $name = $currentLocation['name'];

                                                        $type = $currentLocation['type'];

                                                        $woeid = $currentLocation['woeid'];

                                                        $lat = $currentLocation['lat'];

                                                        $lon = $currentLocation['lon'];

                                                        $locs .= '<tr><td>'.($i+1).'</td><td>'.$name.'</td><td>'.$type.'</td><td>'.$woeid.'</td><td>'.$lat.'</td><td>'.$lon.'</td></tr>';

                                                        $markers .= '&markers=color:blue|label:'.($i+1).'|'.$lat.','.$lon;

                                                        $i++;

                                                        $found = $r->text;

                                                        $text = str_replace($found,'<a href="http://maps.google.com/maps?q='.$name.'">'.$found.'</a>',$text);

                                                 }//endif

                                           }//endif

                                   }//enf foreach

                           }//endforech


                           /* assemble IMG with src and alt */
                           $url = 'http://maps.google.com/maps/api/staticmap?sensor=false'.

                           '&size=640x200&maptype=roadmap'.

                           '&key='.$mapkey . $markers;

                            $locs .= '</tbody></table>';

                            $badge = '<div class="locations">'. $locs . '<img src="'.$url.'" alt="Map">'.

                             '<p class="branding">Powered By: Google Maps, Yahoo Placemakers and YQL</p></div>';

                            /* sent results */
                            $out = '<div id="locations"><p>'.stripslashes($text).'</p>'.$badge.'</div>';

                            $output = $out;
                 }
              

  }

?>