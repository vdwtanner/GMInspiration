
function sendMsg(sender, subject, body, recipient){
	$.ajax({
		url: "scripts/sendRobotMsg.php",
		type: "POST",
		data: ({
			msgrecipient: recipient,
			msgsubject: subject,
			msgbody: body,
			msgsender: sender,
		}),
		success: function(html){
			console.log(html);
		}

	});
}

