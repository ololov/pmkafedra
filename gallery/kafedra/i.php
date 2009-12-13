<div id = 'main'>
<script language="JavaScript">
	var cnt = 32;
 	var scr = 8;
 	var w   = 200;
 	var h   = 150;

 	document.writeln ('<form>');
 		var p = Math.floor(cnt/scr); if (cnt%scr>0) p++;
 		for (var i=1;i<=p;i++)
  			document.write ('<input type=radio onClick="javascript:load('+((i-1)*scr+1)+')">'+i+' ');
 	document.writeln ('</form>');
 	
	for (var i=1; i<=scr; i++)
  		document.writeln ('<img src="void.jpg" name=p'+i+' hspace=4 vspace=4'+(w>0?' width='+w:'')+(h>0?' height='+h:'')+'>');
 
 	function load (st) {
  		for (var i=1; i<=scr; i++) {
   			document.images['p'+i].src= (st>cnt?'void':'gallery/kafedra/'+st)+'.jpg';
   			st++;
  		}
 	}
 	load (1);
</script>
</div>
