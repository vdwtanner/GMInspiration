function editImgSrc(img){
	var div = document.createElement("div");
	$(div).html('<label for="src">URL: </label><input type="text" id="src" placeholder="'+img.src+'" />');
	$(div).dialog({
		height: 250,
		width: 450,
		buttons: ({
			"Accept": function(){
				img.src=$("#src").val();
				$(this).dialog("close");
			},
			"Cancel": function(){$(this).dialog("close")}
		})
	});
}