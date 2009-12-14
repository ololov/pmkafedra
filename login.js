$(document).ready(function(){
			$('.buttonSubmit').click($('#enter').submit(function(e){ 
										register(); 
										e.preventDefault(); 
								     }))
			    }
	         );

function register(){
	$.ajax({
		type: "POST",
		url: "auth/auth.php",
		data: $('#enter').serialize(),
		dataType: "json",
		success: function(msg){
				if(parseInt(msg.status)==1){
					window.location=msg.txt;
				}else if(parseInt(msg.status)==0){
					error(1,msg.txt);
				}
			}
	});
}
