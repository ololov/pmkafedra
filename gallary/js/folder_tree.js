var imageFolder = 'images/';
var folderImage = 'folder.gif';
var plusImage = 'plus.gif';
var minusImage = 'minus.gif';

function showHideNode(e,inputId){
	if(inputId){
		if(!document.getElementById('treeNode' + inputId)) return;
		thisNode = document.getElementById('treeNode' + inputId).getElementsByTagName('IMG')[0]; 
	}else {
		thisNode = this;
	}
	if(thisNode.style.visibility == 'hidden') return;
	var parentNode = thisNode.parentNode;
	inputId = parentNode.id.replace(/[^0-9]/g,'');
	if(thisNode.src.indexOf(plusImage) >= 0){
		thisNode.src = thisNode.src.replace(plusImage,minusImage);
		parentNode.getElementsByTagName('UL')[0].style.display = 'block';
		if(!initExpandedNodes)initExpandedNodes = ',';
		if(initExpandedNodes.indexOf(',' + inputId + ',') < 0) initExpandedNodes = initExpandedNodes + inputId + ',';
		
	}else{
		thisNode.src = thisNode.src.replace(minusImage,plusImage);
		parentNode.getElementsByTagName('UL')[0].style.display = 'none';
		initExpandedNodes = initExpandedNodes.replace(',' + inputId,'');
	}	
}

function initTree(){
	var tree = document.getElementById('tree');
	if(!tree) return;
	
	var menuItems = tree.getElementsByTagName('LI');
	for(var i = 0; i < menuItems.length; i++){
		var subItems = menuItems[i].getElementsByTagName('UL');
		var img = document.createElement('IMG');
		img.src = imageFolder + plusImage;
		img.onclick = showHideNode;

		if(subItems.length == 0) img.style.visibility = 'hidden';
		var a_tag = menuItems[i].getElementsByTagName('A')[0];
		menuItems[i].insertBefore(img, a_tag);
		menuItems[i].id = 'treeNode' + (i + 1);

		var folderImg = document.createElement('IMG');
		if(menuItems[i].className){
			folderImg.src = imageFolder + menuItems[i].className;
		}else{
			folderImg.src = imageFolder + folderImage;
		}
		menuItems[i].insertBefore(folderImg,a_tag);

	}
}

