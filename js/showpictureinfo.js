var url =  window.location.search;
var activity_name;
var activity_id;
var picture_url;
var share_list = new Array();
var access_token;
var user_name;
var user_id;
var picture_name;
var picture_name_list = new Array();

if(url.indexOf("?")!=-1)
{
	var str = url.substr(1);
	strs = str.split("&");
	activity_name = unescape(strs[0].split("=")[1]);
	activity_id = strs[1].split("=")[1];
	picture_url = unescape(strs[2].split("=")[1]);
	picture_name = unescape(strs[3].split("=")[1]);
}

share_list.push(picture_url);
picture_name_list.push(picture_name);

document.getElementById('picture').src = picture_url;

document.getElementById('content').style.minHeight = window.screen.availHeight+'px' ;

function backToPictureList(){
	self.location.href="showpicturelist.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
}


function weiboIncoPress(){
	document.getElementById('weibo').src = "images/pictureinfo/editpicture_share_press44-44.png";
}

function weiboIconMouseOut(){
	document.getElementById('weibo').src = "images/pictureinfo/editpicture_share44-44.png";
}


function cloudIncoPress(){
	document.getElementById('pcs').src = "images/pictureinfo/editpicture_save_press50-44.png";
}

function cloudIconMouseOut(){
	document.getElementById('pcs').src = "images/pictureinfo/editpicture_save_50-44.png";
}

function downloadIncoPress(){
	document.getElementById('download_picture').src = "images/pictureinfo/editpicture_down_press44-44.png";
}

function downloadIconMouseOut(){
	document.getElementById('download_picture').src = "images/pictureinfo/editpicture_down_44-44.png";
}


function shareToWeiBo(){
	WB2.anyWhere(function(W){
		W.widget.publish({
			'id' : 'weibo',
			'default_text' : '我在西二旗夜话上发现一张有趣的照片@西二旗夜话',
			'default_image' : picture_url
		});
	});
}

function saveToPCS(){
	
	if(window.localStorage)
	{	
		if(window.localStorage.getItem('BD_user_name') && window.localStorage.getItem('BD_user_id') && window.localStorage.getItem('access_token')){
			access_token = window.localStorage.getItem('access_token');
			linkServer();
			
		}else{
			baidu.require('connect', function(connect){
				connect.init( 'Wd6f4dwnfxVgL1tVQoVUwbLL',{
					status:true,		
			   	});
			   	
				//login and get access_token
				connect.login(function(info){
					access_token = info.session.access_token;//get access_token			
					//get user_id and user_name 				
					connect.api({
						url: 'passport/users/getLoggedInUser',
						onsuccess: function(info){
							user_name = info.uname;
							user_id = info.uid;
							if(window.localStorage){
								window.localStorage.setItem('BD_user_name',user_name);
								window.localStorage.setItem('BD_user_id',user_id);
								window.localStorage.setItem('access_token',access_token);
								linkServer();			
							}						
						},
						onnotlogin: function(){
							
						},
						params:{
						  "access_token": access_token	
						}
					});
					
				},{
					scope: "netdisk"
				  });
			  		   
			});
			
		}
	}else{
		alert('your browser is old!');
	}
}

function linkServer(){
	var postData = {
			'method': 'savePhotoToPCS',
			'access_token': access_token,
			'source_file': share_list,
			'activity_name' : activity_name,
			'picture_name_list' : picture_name_list
		};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'GET', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			alert("转存成功！");
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {

		});
}