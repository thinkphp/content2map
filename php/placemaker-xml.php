<?php

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


  if(isset($_POST['documentContent']) && $_POST['documentContent'] != '') {

    header('content-type: text/xml');

    /* grab your content sent as POST in paramenter 'content' and hold in 'text'*/
    $text = $_POST['documentContent'];

    /* endpoint for YQL service*/
    $url = 'http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20FROM%20geo.placemaker%20WHERE%20documentContent%20%3D%20%22'.urlencode($text).'%22%20AND%20documentType%3D%22text%2Fplain%22%20AND%20appid%20%3D%20%22%22&format=xml&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

    /* get all data */
    $output = get($url);

    echo$output;

   } else {

?>


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
  .locations {margin-top:10px}
  .locations{position:relative;background: #fff;border: 1px solid #999;border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;-moz-box-shadow:-2px 2px 2px rgba(66,66,66,.3);overflow: hidden;width:640px}
  .locations{height: 400px;}
  .locations img {float: left;}
  .locations table{width: 650px}
  #results p{font-weight: bold;font-size: 15px;}
  #results p.branding {position: absolute;bottom: 5px;right: 5px;font-size: 10px;}
  #content{border:1px solid #999;border-left:none;border-right:none;padding:1em 0; margin:1em 0;}
  #ft p{text-align:right;margin-top:3em;font-size:80%;}
  </style>
</head>
<body>
<div id="doc" class="yui-t7">
   <div id="hd" role="banner"><h1>Placemaker</h1></div>
   <div id="bd" role="main">
	<div class="yui-g">
           <p>Simply enter a text in the following textfield and hit the "get locations" button to analyse it.</p><p>The result will be an XML file.</p>
           <div id="content"><!-- start input -->
           <form action="<?php echo$_SERVER['PHP_SELF'];?>" id="f" name="f" method="post">
                 <textarea id="documentContent" name="documentContent" rows="5" cols="99"><?php echo isset($_POST['documentContent']) ? $_POST['documentContent'] : "Currently I am on a working trip in Sunnyvale. I came via San Francisco and I need to go to San Jose  later on to return the broken rental car. I'll probably check visit the Facebook guys in Palo Alto  afterwards.";?></textarea>
                 <input type="submit" name="s" id="s" value="post locations"/>
           </form>
	</div><!-- end input -->
       <!-- start output -->
      <div id="results"><?php echo$out; ?></div>
      <!-- end output -->
	</div>
   <div id="ft" role="contentinfo"><p>Written by Adrian Statescu | <a href="placemaker-xml.phps">souce</a></p></div>
</div>
</body>
</html>
<?php
}
?>