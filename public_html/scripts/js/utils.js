function editImgSrc(img){
	var div = document.createElement("div");
	$(div).html('<label for="src">URL: </label><input type="text" id="src" placeholder="'+img.src+'" />');
	$(div).dialog({
		height: 250,
		width: 450,
		position: {my: "center top", at: "center top", of: window},
		buttons: ({
			"Accept": function(){
				img.src=$("#src").val();
				$(this).dialog("close");
			},
			"Cancel": function(){$(this).dialog("close")}
		})
	});	
}

//replace a div with an editor and then update the contents of said element
var editor;
var replaced_div;
function replaceWithEditor(div){//constructor
	replaced_div=div;
	editor=CKEDITOR.replace( div );
	editor.on('change', function(event){//listener
		console.log('Total bytes: '+event.editor.getData().length);
		$(replaced_div).html(event.editor.getData());
	});
}
function destroyEditor(){
	if(editor){
		editor.destroy();
	}
}
function resetEditor(){
	editor.setData("");
}
