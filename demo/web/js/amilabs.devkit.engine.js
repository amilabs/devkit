var DKApp = {
    request: function(method, params){
        $.ajax({
            url: 'service.php', 
            data: JSON.stringify({jsonrpc:'2.0',method:method, params:params, id:"jsonrpc"}),
            type:"POST",
            dataType:"json",
            success:  function(data){ alert("The result is : " + data.result); },
            error: function(err){ alert("Error"); }
         });
    }
};