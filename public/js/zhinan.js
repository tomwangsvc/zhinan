function validateEmail(sEmail) {
	var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,5}$/;
	if (filter.test(sEmail)) {
	  return true;
	}
	else {
	  return false;
	}
}
function validateUsername(name) {
	
	var filter =/^[a-zA-Z\u4e00-\u9fa5][a-zA-Z0-9\u4e00-\u9fa5]+$/;
	//var filter = /^[a-zA-Z][a-zA-Z0-9_]+$/;
	if (filter.test(name)) {
	  return true;
	}
	else {
	  return false;
	}
}
function validateName(name) {
	
	var filter =/^[a-zA-Z\u4e00-\u9fa5][a-zA-Z\u4e00-\u9fa5]+$/;
	//var filter = /^[a-zA-Z][a-zA-Z0-9_]+$/;
	if (filter.test(name)) {
	  return true;
	}
	else {
	  return false;
	}
}

function validateNumber(number) {
	var filter = /^[0-9]+$/;
	if (filter.test(number)) {
	  return true;
	}
	else {
	  return false;
	}
}
function validatePhoneNumber(number) {
	var filter = /^[0-9\-]+$/;
	if (filter.test(number)) {
	  return true;
	}
	else {
	  return false;
	}
}

var count; //global varible for timer
/**
 * create a new div element for showing a notice
 * @param txt
 * @param count
 */
function shownotice(txt,second,url){
	
	var txt1 = txt;
	count =second;
    var newdiv = $("<div style='top:0;left:0; position:absolute; width:100%;height:100%;'><div id='zn-notice'></div></div>");
    //var exitbtn = $('<img id="notice-closebtn" src="/images/exit.png">');
    var p1 = $('<p id="notice-p1"></p>');
    var p2 = $('<p id="notice-p2"><span id="timer"></span></p>');  
    var txt2 = '秒后自动跳转';    
    $('body').append(newdiv);
    $("#zn-notice").append(p1,p2); 
    $("#notice-p1").append(txt1);
    $("#notice-p2").append(txt2);
    $("#timer").append(count);
    showtimer(count,url);
   
}
function showtimer(count,url) {
	var currentTime = new Date().getTime()
    // Handler for .ready() called.
    window.setTimeout(function () {
        location.href = url;
    }, 1000*count)
	var counter=setInterval(timer, 1000); //1000 will  run it every 1 second
    
}
function timer()
{
  count=count-1;
  if (count < 0)
  {
     clearInterval(counter);
     //counter ended, do something here
     return;
  }
  document.getElementById("timer").innerHTML=count;
}


/* for dynamic login form*/

function ShowLoginForm(){
	
	if ($('#dynamic-loginform').length > 0) {
		$("#dynamic-loginform").remove();
	}	
	var newdiv = $("<div id='dynamic-loginform'></div>");
	var exitbtn = $('<p><img id="dlf-closebtn" src="/images/exit.png"></p>');
	
	var elements = $("<h3>您还没登入，请先登入！</h3><p><input placeholder='您的帐号或邮箱' id='dusername' name='dusername' type='text'></p><p>" +
			"<input name='dpassword' id='dpassword' placeholder='您的密码' type='password'></p>" +
			"<input id='dy-submitbtn' type='button' value='登     入'><br>"+
			"<p class='dy-plink'><a class='dyn-getpassword' target='_blank' href='http://www.zhinan.local/account/getpassword'>忘记密码</a><span>|</span><a class='dyn-register' target='_blank' href='http://www.zhinan.local/account/register'>立即注册 </a></p>");
			
	$('body').append(newdiv);
	$("#dynamic-loginform").append(exitbtn,elements);
	
}


function CheckIfLogin(){
	var islogin = false;
	$.ajax({		   
        url:"/account/checkiflogin",   
        data:{   
        },          
        type:"POST",           
        dataType:"text", 
        async:false,
        success:function(data)
        {
          if(data>0){
        	  islogin = true;
              return;		            	      
          }
        }
     });
    return islogin;
}

$(document).ready(function() {
	
	$("body").delegate("#dlf-closebtn", "click", function(e){
		$("#dynamic-loginform").remove();		
	});

	$("body").delegate("#dpassword", "click", function(e){
		$("#dpassword").css("border", "1px solid white");		
	});
	$("body").delegate("#dusername", "click", function(e){
		$("#dusername").css("border", "1px solid white");		
	});

	$("body").delegate("#dy-submitbtn", "click", function(e){
		var username=$('#dusername').val().trim();
		var password=$('#dpassword').val().trim();
		if( username!="" && password!=""){	
			$.ajax({		   
	            url:"/account/login",   
	            data:{   
	            	username: username,             
	                password:password, 
	            },          
	            type:"POST",           
	            dataType:"text", 
	            success:function(data)
	            {
	              if(data>0){			            	           
		              if(data==2){
		            	  $("#dynamic-loginform").remove(); 		            	  
			          }
	              }else{
	            	  $('#dusername').css("border", "1px solid red");
	            	  $('#dpassword').css("border", "1px solid red");
		           }
	            }
	         });
		}else{
		    if(username==""){
		    	$('#dusername').css("border", "1px solid red");
		    }
		    if(password==""){
		    	$('#dpassword').css("border", "1px solid red");
		    }
		}	
	});		
});



/**
 * call to mypost deletepost action to set post's status to be 0
 * @param classid
 * @param itemid
 */
function deleteAction(classid, itemid,signal){

	 $.ajax({		   
        url:"/mypost/deletepost",   
        data:{   
       	 classid:classid,             
       	 itemid:itemid 
        },          
        type:"POST",           
        dataType:"text", 
        success:function(data)
        {
         if(data){
        	 if(signal=='1'){
        		 window.location.href='/mypost/display'; 
        	 }
        	 if(signal=='2'){
        		 window.location.href='/mypost/verifyfail'; 
        	 }       	
        	}
        }
    });
}
/**
 * generate a checking form before sent a delete action
 * @param classid
 * @param itemid
 */
function deleteCheckForm(classid, itemid,signal){
	$(".deleteckform").remove();	
	var newdiv = $("<div class='deleteckform'></div>");
	var content=$("<p>确定删除？</p> <p><input onclick='deleteAction("+classid+
		        ","+itemid+","+signal+");' type='button' value='是'>"+
		        "<input type='button' onclick='closeDelectCheckForm();' value='否'></p>");
	
	$('body').append(newdiv);
	$(".deleteckform").append(content);

}
function closeDelectCheckForm(){
	
	$(".deleteckform").remove();
}

/*showing a div with a processing image*/
function showProcessImage(){	
	var html=$('<div style="z-index:999;background:; opacity:1; position:absolute; top:0; left:0; margin:0; padding:0;width:100%;height:100%;" id="processcontainer">'+
				'<div style="width:100px; height:100px;top:50%;left:50%;position:absolute;margin-left:-50px; margin-top:-100px;" ><img style="width:80px; opacity:1;" src="/images/process.gif"></div></div>');
   $('body').append(html);
}
/*remove a processing image div*/
function clearProcessImage(){
	$("#processcontainer").remove();
}

/*
 * for top login form
 */
$(document).ready(function() {	
	//triggle top login form
	$('#top-exitbtn').mouseover(function(){
	  var newSrc = $(this).attr("src").replace("exit.png", "exit2.png");
	  $(this).attr("src", newSrc); 
	});
	$('#top-exitbtn').mouseout(function(){
	  var newSrc = $(this).attr("src").replace("exit2.png", "exit.png");
	  $(this).attr("src", newSrc); 
	});


	$("#top-loginbtn").click(function(){
		  $(".loginform").css("visibility","visible"); 
		});
	$('#top-exitbtn').click(function(){
		$(".loginform").css("visibility","hidden"); 
	});
	
	//submit buttion to login
	$( "#top-login-form" ).submit(function( event ) {
		  $('.notice').empty();
		  var username=$('#username').val().trim();
		  var password=$('#password').val().trim();
		  var autologin = $('.rememberpsw-check:checked').val();
		  
		  if(username=="" && password==""){
	        txt = "用户名及密码不能为空";
			 $('.notice').append(txt);
			 event.preventDefault();
			 return;
			  }
		  if(password==""){
		         txt = "密码不能为空";
				 $('.notice').append(txt);
				 event.preventDefault();
				 return;
		  }
		  if(username==""){
		
		        txt = "用户名不能为空";
				 $('.notice').append(txt);
				 event.preventDefault();
				 return;
		    }
		  $.post(this.action, $(this).serialize(), function (notice) {
		    	if(notice>0){		            	           
				     if(notice==2){	
				    	  $(".loginform").remove(); 
					      window.location.href=$(location).attr('href');
					  }
					  if(notice==1){
				          txt = "帐号没有被激活，请登入注册邮箱查看激活链接";
				          $('.notice').append(txt);
				      }
			     }else{
					  txt = "用户及密码不匹配，请重新输入";
					  $('.notice').append(txt);
				      }
			        	   
			 });
			return false;	        
	});      
});