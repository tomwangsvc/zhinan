
//create XMLHttpRequestObject depending on kinds of detected internet explore 
function getXmlHttpObject(){
	
    var xmlHttpRequestObject=false;
	  
	  if(window.ActiveXObject){
		  xmlHttpRequestObject =new ActiveXObject("Microsoft.XMLHTTP"); //for ie	
		     
	  }else{
		  xmlHttpRequestObject =new XMLHttpRequest(); //for other browsers
	  }
	 return xmlHttpRequestObject;
}  

function getTextData(data,url){
	var RequestObject = getXmlHttpObject();  
	var response="";
    if(RequestObject){	
		 RequestObject.open('post',url,true);
		 RequestObject.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		 RequestObject.onreadystatechange=function(){  				 
		 	if(RequestObject.readyState==4 && RequestObject.status==200){		 		
		 		response = RequestObject.responseText;	
		 		//alert(response["username"]);
		 		 //alert(response);
		 		
		 		
		        }       					   
		};		 
	 	RequestObject.send(data);	
	}
    
    return response;
}