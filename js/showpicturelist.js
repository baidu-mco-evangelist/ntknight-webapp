
var activity_name;
var activity_id;
var pictureUrlList;
var access_token ;
var user_id;
var user_name;
var share_list;

if(window.localStorage)
{
	activity_name = window.localStorage.getItem("current_activity_name") ;
	activity_id = window.localStorage.getItem("current_activity_id");;
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
			newTd1.innerHTML = '<img src="'+pictureUrlList[i]+'" onClick="loadPictureInfo(\''+ pictureUrlList[i] +'\',\''+ pictureNameList[i] +'\')" />';
			
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
	
	if(window.localStorage){
		window.localStorage.setItem("current_picture_url",picture_url);
		window.localStorage.setItem("current_picture_name",picture_name);
	}
	self.location.href="showpictureinfo.html";	
}