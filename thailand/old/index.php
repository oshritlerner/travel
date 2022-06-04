<?php 


if(!session_id())
	session_start();
$action	= isset($_SESSION['action']) ? $_SESSION['action']	: 'main';
$login	= isset($_SESSION['login'])  ? $_SESSION['login']	: NULL;
$month	= isset($_SESSION['month'])  ? $_SESSION['month']	: '00';
$day	= isset($_SESSION['day'])	 ? $_SESSION['day']		: '00';
?>
<!DOCTYPE HTML>
<html>
	<head>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<script src="./lib/jquery.uploadfile.min.js"></script>
		<link href="./lib/uploadfile.css" rel="stylesheet">
		<script  src="./lib/tools.js">	</script>
	</head>
	<body dir =rtl>
		<div class='main' id="main">
			<div id="menu" class='main' style="width: 10%; direction: rtl; border: black solid medium; float: right">
			</div>
		<div class='main' id="loading-indicator" style=" position: fixed; background: #151515 ;  opacity: .7; height: 1000px; width: 100%; border: #000000;">
			<img src="./lib/images/ajax-loader.gif"
				style="
				position: fixed;
				top: 50%;
				left: 50%;
				margin-top: -50px;
				margin-left: -100px;	"/>
			</div>
			<div class='main' id="view" style="width: 85%; min-height: 600px; direction: rtl; border: black solid medium; float: left;">
				<?php 
				?>
			</div>
		</div>
		<script>
		$( document ).ready(function() {
			$('div').hide(); 
			$(".main").show(); 
			$.post('view.rpc.php', { action: '<?php echo $action;?>', day: '<?php echo $day;?>', month: '<?php echo $month;?>'}, function(data) {
					$("#view").html(data);
					menu();
					$("#loading-indicator").hide();
			  }, "json");
		});
		</script>
	</body>	
</html>
