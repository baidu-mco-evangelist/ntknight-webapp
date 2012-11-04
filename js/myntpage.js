
function mainPageIncoPress(){
	document.getElementById('mainPageIcon').src = "images/main/file_tital_meicon_press220-60.png";
}


function mainPageIconMouseOut(){
	document.getElementById('mainPageIcon').src = "images/main/file_tital_meicon_220-60.png";
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
function moreInfoIconPress(){
	document.getElementById('moreInfoIcon').src = "images/main/file_tital_moreicon_press.png";
}

function moreInfoIconMouseOut(){
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
			flag_list = data.flag_list;
			mynt_banner_list = data.mynt_banner_list;
			
			flag = 1;
			
			if(activity_list.length == 0){
				document.getElementById("myactivity").style.display = "none";
				document.getElementById('content').innerHTML = ' <div  id="info" align="center"><img src="images/main/mypage_empty116-192.png"  id="picture"><div id="comment"><p >您还没有参加过西二旗夜话活动！</p></div><div id="activityinfo"><a href="mainpage.html">点击此处查看活动信息</a></div></div>';
			}else{
				for(i=0;i<activity_list.length;i++){
					if(flag_list[i] == 0){
						flag = 0;
						document.getElementById('title').innerHTML = '您被邀请参加"'+activity_list[i]+'"';
						document.getElementById('banner').src = mynt_banner_list[i];
					}
					var newTr = document.getElementById('old_activity').insertRow();
					var newTd1 = newTr.insertCell();
					newTd1.innerHTML = '<img src="images/mynt/mypage_arrow25-25.png" id="action_icon" onClick="loadActivityinfo(\''+activity_id_list[i]+'\',\''+activity_list[i]+'\')">';
					var newTd2 = newTr.insertCell();
					newTd2.innerHTML = '<p id="activity_name">'+ activity_list[i] +'</p>';
				}
				
				if(flag == 1){
					document.getElementById('new_activity').style.display = "none";
					
				}
			}
			
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {
			
		});

}

function loadActivityinfo(activity_id,activity_name){
	
	if(window.localStorage){
		window.localStorage.setItem("current_activity_name",activity_name);
		window.localStorage.setItem("current_activity_id",activity_id);
	}
	self.location.href = "activityinfo.html";
}