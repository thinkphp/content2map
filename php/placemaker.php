<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title>Analyse a text and add map with its locations in pure JavaScript</title>
   <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">
   <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/base/base.css" type="text/css">
  <style type="text/css">
  html,body{font-family: helvetica,arial,verdana,sans-serif}
  #doc{width: 650px}
  #bd p{font-weight: bold}
  h1{font-size: 28px;text-shadow:1px 3px 3px #ccc;margin:0 0 .5em 0;color:#393}
  a{color: #393}
  #map span strong{display:block;color:#0f0;}
  #ismap,table,textarea{width:640px;margin:1em auto;display:block;}
  table{display:table;}
  input[type='submit']{margin:1em auto;display:block;}
  #ft p{text-align:right;margin-top:3em;font-size:80%;}
  </style>
</head>
<body>
<div id="doc" class="yui-t7">
   <div id="hd" role="banner"><h1>Analyse a text and add map with its locations in pure PHP</h1></div>

   <div id="bd" role="main">
	<div class="yui-g">
           <p>Simply enter a text in the following textfield and hit the "get locations" button to analyse it.</p>
           <div id="content"><!-- start input -->
           <form action="<?php echo$_SERVER['PHP_SELF'];?>" id="f" name="f" method="post">
                 <textarea id="documentContent" name="documentContent" rows="5" cols="99">Currently I am on a working trip in Sunnyvale. I came via San Francisco and I need to go to San Jose  later on to return the broken rental car. I'll probably check visit the Facebook guys in Palo Alto  afterwards.</textarea>
                 <input type="submit" name="s" id="s" value="post locations"/>
           </form>

	</div><!-- end input -->
       <!-- start output -->
      <div id="results">
<?php
      if(isset($_POST['s'])) {

          /* set up your apikey from GOOGLE API */  
          $mapkey = 'ABQIAAAAijZqBZcz-rowoXZC1tt9iRT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQQBCaF1R_k1GBJV5uDLhAKaTePyQ';

          $text = filter_input(INPUT_POST,'documentContent',FILTER_SANITIZE_ENCODED);
             
          $key = 'q0pcWFLIkY77xr0DLfxcK04QfkBMGvEe'; 

          define('POSTURL','http://wherein.yahooapis.com/v1/document');

          define('POSTVARS','appid='.$key.'&documentContent='.$text.'&documentType=text/html&outputType=xml');

          $xml = get(POSTURL,POSTVARS); 

          $places = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

          $text = rawurldecode($text);

          echo"<h2>Output:</h2>"; 

          if($places->document->placeDetails) {

               echo '<table><thead><th>ID</th><th>Name</th><th>Type</th><th>woeid</th><th>latitude</th><th>longitude</th></thead><tbody>';

               $pl = $places->document->placeDetails;
 
               $all = sizeof($pl);

               $map = 'http://maps.google.com/maps/api/staticmap?key='.$mapkey;

               for($i=0;$i<$all;$i++) {

                   $p = $pl[$i];

                   $lat = $p->place->centroid->latitude;

                   $lon = $p->place->centroid->longitude;

                   echo'<tr>';

                   echo'<td>'.($i+1).'</td>';

                   echo'<td>'.($p->place->name).'</td>';

                   echo'<td>'.($p->place->type).'</td>';

                   echo'<td>'.($p->place->woeId).'</td>';

                   echo'<td>'.($lat).'</td>';

                   echo'<td>'.($lon).'</td>';

                   echo'</tr>';

                   $map .= '&markers=label:'.($i+1).'|'.$lat.','.$lon.'&visible='.$lat.','.$lon;
               }

               echo'</tbody></table>';

               $map .= '&sensor=false&size=700x200&maptype=roadmap';

               echo'<img id="ismap" src="'.$map.'" alt="map">';
                               
            } else {

               echo'<h2>Cannot find any locations for </h2><p>'.$text.'</p>';
            }
 
      }//endif
?>
      </div>
      <!-- end output -->
	</div>
   <div id="ft" role="contentinfo"><p>Written by Adrian Statescu | <a href="placemaker.phps">source</a></p></div>

</div>
</body>
</html>

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
?>