<?php
include_once('include/site.php');

function print_sidebar()
{
?>
<script type="text/javascript" src="js/folder_tree.js"></script>
<script type="text/javascript">
	window.onload = initTree;
</script>
<style type="text/css">
#vmenu2
{
	background:	#b9defd;
	border: 	1px solid #adadae;
	padding:	0px;
	opacity:	.7;
	width:		18%;
	height:		300px;
	float:		left;
}

#vmenu2 p
{
	display:	none;
}

#vmenu2 ul
{
	list-style:	none;
	padding:	0px;
	margin:		0px;
}

#vmenu2 ul li
{
/*	border-bottom:	1px dotted #025391;*/
	margin:		0px;
}

#vmenu2 ul li a
{
/*	display:	 block;*/
	text-decoration: none;
	font-size:	 82%;
	font-weight:	 bold;
	color:		 #025391;
	width:		 100%;
/*	padding:	 5px 0px 5px 5px;*/
}
#tree{
	margin:0px;
	padding:0px;
}
#tree ul{	/* Sub menu groups */
	margin-left:20px;	/* Left spacing */
	padding-left:0px;
	display:none;	/* Initially hide sub nodes */
}
#tree li{	/* Nodes */
	list-style-type:none;
	vertical-align:middle;
}
#tree li a{	/* Node links */
	color: #025391;
	text-decoration:none;
	font-family:arial;
	font-size:0.8em;
	padding-left:2px;
}
</style>

<div id = "vmenu2">
	<p>Навигация</p>
	<ul id="tree" class="tree">	
		<li><a href="<?php echo gallary_url . "/gallery.php" ?>">Всякая всячина</a></li>
		<li><a href="#">stud</a>
		  <ul>
			<?php
				echo "<li><a href=\"#\">pm05</a>
					  <ul>
					  	<li><a href=\"#\">pm05bsv</a></li>
						<li><a href=\"#\">pm05km</a></li>
					  </ul>
				      </li>
				";

				for($i = 6; $i < 10; $i++){
					echo "<li><a href=\"#\">pm0$i</a></li>";
				}
			?>
	         </ul>
		</li>
	</ul>
</div>
<?php
}
?>
