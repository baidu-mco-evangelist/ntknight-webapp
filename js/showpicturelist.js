
document.getElementById('menu_tool').style.display = "none";

var url =  window.location.search;
var activity_name;
var activity_id;
var pictureUrlList;
var access_token ;
var user_id;
var user_name;
var share_list;

if(url.indexOf("?")!=-1)
{
	var str = url.substr(1);
	strs = str.split("&");
	activity_name = unescape(strs[0].split("=")[1]);
	activity_id = strs[1].split("=")[1];
}

var postData = {
	'method': 'getPictureList',
	'activity_id':activity_id,
};
$.ajax({
	url : '/server/server.php', 
	data : postData,
	type : 'GET', 
	dataType : 'json',
	cache : false
	}).done(function(data) {
		
		pictureUrlList = data.pictureUrlList;
		pictureNameList = data.pictureNameList;
		
		var newTr = document.getElementById('picture_list').insertRow();
		for(i=0; i<pictureUrlList.length; i++){
			
			var newTd1 = newTr.insertCell();
			newTd1.innerHTML = '<img src="'+pictureUrlList[i]+'" width="154" height="149" onClick="loadPictureInfo(\''+ pictureUrlList[i] +'\',\''+ pictureNameList[i] +'\')" />';
			
			if(i%3==0){
				var newTr = document.getElementById('picture_list').insertRow();
			}
			
		}
		
	}).fail(function(data, txt) {
	  //					alert("Internal Server Error" + data);
	})
	.always(function(data) {
		
	});
	
	
function loadPictureInfo(picture_url,picture_name){
	self.location.href="showpictureinfo.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id+"&url="+escape(picture_url)+"&picture_name="+escape(picture_name);
	
}

function backToActivityInfo(){
	self.location.href="activityinfo.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
}

function weiboIncoPress(){
	document.getElementById('weibo').src = "images/pictureinfo/editpicture_share_press44-44.png";
}

function weiboIconMouseOut(){s
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
			'default_image' : pictureUrlList[0]
		});
	});
}

function saveToPCS(){
	
}

function editPicture(){	
	document.getElementById('menu_tool').style.display = "block";
	
}


