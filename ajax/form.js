(function(){

  var f = document.getElementById('f');

  var s = document.getElementById('s');

  f.onsubmit = function(){

        s.setAttribute('value','loading...');

        var url = 'getlocations.php';

        var postData = 'documentContent='+document.f.documentContent.value;
                     
        asyncRequest.REQUEST('POST',url,handleResponse,postData);    

        function handleResponse(r) {

                 document.getElementById('results').innerHTML = r;

                 s.setAttribute('value','get locations');
        } 

     return false;
  }

})();

