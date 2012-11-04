
document.getElementById('member_user_name').innerHTML = window.localStorage.getItem("NT_user_name");

var sex = window.localStorage.getItem("NT_user_sex");

if(sex == "female"){
	document.getElementById('person_icon').src="images/more/fillin_avatar_woman126-126.png";
}

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

//moreinfo button
function myNtIncoPress(){
	document.getElementById('myNtIcon').src = "images/main/file_tital_meicon_press.png";
}

function myNtIncoMouseOut(){
	document.getElementById('myNtIcon').src = "images/main/file_tital_meicon.png";
}
function loadMyNtPage()
{

	self.location.href="myntpage.html";
}


function loadMoreInfoPage()
{

	self.location.href="moreinfopage.html";

}


function logout(){
	if(window.localStorage){
		
		var member_id = window.localStorage.getItem("NT_user_id");
		window.localStorage.removeItem("NT_user_name");
		window.localStorage.removeItem("NT_user_id");
		
		var postData = {
			'method': 'logout',
			'member_id' : member_id
		};
		$.ajax({
			url : '/server/server.php', 
			data : postData,
			type : 'POST', 
			dataType : 'json',
			cache : false
			}).done(function(data) {

				alert("已退出当前账户");
				self.location.href="mainpage.html";
			})
			.fail(function(data, txt) {
			  //					alert("Internal Server Error" + data);
			})
			.always(function(data) {
			});			
	}
}

function loadFeedBackPage(){
	self.location.href="suggestpage.html";
}


function loadMemberInfoPage(){
	self.location.href="showmemberinfopage.html";
}