
function mainPageIncoPress(){
	document.getElementById('mainPageIcon').src = "images/main/file_tital_meicon_press.png";
}


function mainPageIconMouseOut(){
	document.getElementById('mainPageIcon').src = "images/main/file_tital_meicon.png";
}

addEventListener('load',function(){
	setTimeout(function(){window.scrollTo(0,1); },100);
});


function loadMainPage()
{
	self.location.href="mainpage.html";
}


function loadMyNtPage()
{

	self.location.href="myntpage.html";
}


//moreinfo button
function moreInfoIncoPress(){
	document.getElementById('moreInfoIcon').src = "images/main/file_tital_moreicon_press.png";
}

function moreInfoIncoMouseOut(){
	document.getElementById('moreInfoIcon').src = "images/main/file_tital_moreicon.png";
}

function loadMoreInfoPage()
{

	self.location.href="mainpage.html";

}


if(window.localStorage){
	
	member_id = window.localStorage.getItem("NT_user_id");
	
	var postData = {
		'method' : 'getMemberJoinActivityStatus',
		'member_id': member_id
	};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'GET', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			
			activity_list = data.activity_list;
			activity_id_list = data.activity_id_list;
			
			if(activity_list.length == 0){
				document.getElementById('content').innerHTML = "您还没有参加过西二旗夜话的活动！";
			}else{
				for(i=0;i<activity_list.length;i++){
					
				}
			}
			
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {
			
		});

}