<?php
include("resize-class.php");
include("config.php");

class tools
{
	public static function mail_attachment($to, $subject, $message, $from, $file)
	{
		// $file should include path and filename
		$filename = basename($file);
		$file_size = filesize($file);
		$content = chunk_split(base64_encode(file_get_contents($file))); 
		$uid = md5(uniqid(time()));
		$from = str_replace(array("\r", "\n"), '', $from); // to prevent email injection
		$header = "From: ".$from."\r\n"
			."MIME-Version: 1.0\r\n"
			."Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n"
			."This is a multi-part message in MIME format.\r\n" 
			."--".$uid."\r\n"
			."Content-type:text/plain; charset=iso-8859-1\r\n"
			."Content-Transfer-Encoding: 7bit\r\n\r\n"
			.$message."\r\n\r\n"
			."--".$uid."\r\n"
			."Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"
			."Content-Transfer-Encoding: base64\r\n"
			."Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n"
			.$content."\r\n\r\n"
			."--".$uid."--"; 
		return mail($to, $subject, "", $header);
	}
	
	public static function save_text($month, $day, $text)
	{
		if(!$text)
			return ("Empty text - please enter text");
		$text_file_name = "$month/$day/index.html";
		file_put_contents($text_file_name, str_replace('script', 'my_script', $text));
		return ("Text saved for $day-$month-".config::year);
	}
	
	public static function save_main($text)
	{
		if(!$text)
			return ("Empty text - please enter text");
		$main_path = "main.txt";
		file_put_contents($main_path, str_replace('script', 'my_script', $text));
		return ("Text saved for main");
	}
	
	public static function delete($month, $day, $file_name)
	{
		$path = "./$month/$day/";
		if(file_exists($path.$file_name))
		{
			unlink ($path.$file_name);
			return "$file_name was deleted";
		}
	}
	
	
		public static function saveImage($img, $path,  $imageQuality="100")
	{
		// *** Get extension
		$extension = strrchr($path, '.');
		$extension = strtolower($extension);

		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				if (imagetypes() & IMG_JPG) {
					imagejpeg($img, $path, $imageQuality);
				}
				break;

			case '.gif':
				if (imagetypes() & IMG_GIF) {
					imagegif($this->imageResized, $path);
				}
				break;

			case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);

				// *** Invert quality setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;

				if (imagetypes() & IMG_PNG) {
					 imagepng($this->imageResized, $path, $invertScaleQuality);
				}
				break;

			// ... etc

			default:
				// *** No extension - No save.
				break;
		}
	}

	function rotateImage($img, $rotation) {
	$width = imagesx($img);
	$height = imagesy($img);
	switch($rotation) {
	  case 90: $newimg= @imagecreatetruecolor($height , $width );break;
	  case 180: $newimg= @imagecreatetruecolor($width , $height );break;
	  case 270: $newimg= @imagecreatetruecolor($height , $width );break;
	  case 0: return $img;break;
	  case 360: return $img;break;
	}
	if($newimg) {
	  for($i = 0;$i < $width ; $i++) {
		for($j = 0;$j < $height ; $j++) {
		  $reference = imagecolorat($img,$i,$j);
		  switch($rotation) {
			case 90: if(!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference )){return false;}break;
			case 180: if(!@imagesetpixel($newimg, $i, ($height - 1) - $j, $reference )){return false;}break;
			case 270: if(!@imagesetpixel($newimg, $j, $width - $i, $reference )){return false;}break;
		  }
		}
	  } return $newimg;
	}
	return false;
  }
	
	public static function rotate($month, $day, $file_name, $degree)
	{
		$path = "./$month/$day/";
		if(file_exists($path.$file_name))
		{
			$source = imagecreatefromjpeg($path.$file_name);

			// Rotate
			$rotate = tools::rotateImage($source, $degree, 0);

			//and save it on your server...

			tools:: saveImage($rotate, $path.$file_name);
			$data = file_get_contents($path.$file_name);
			$type = strtolower(pathinfo($path.$file_name, PATHINFO_EXTENSION));

			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$id = str_replace('.', '_', $file_name);
			return $path.$file_name;
		}
	}
	
	public static function backup($month, $day)
	{
		$dir		= $day != 'x' ? "$month/$day/" : "$month/";
		$tmp_zip	= $day != 'x' ? config::year."-$month-$day.zip" : config::year."-$month.zip";
		$zip		= new ZipFolder($tmp_zip, $dir);
		tools::mail_attachment('ronen.no1@gmail.com', "backup $tmp_zip", 'backup', 'backup', $tmp_zip);
		unlink($tmp_zip);
		return "$tmp_zip was sent to ronen.no1@gmail.com";
	}
	

	public static function menu($edit, $month_to_show)
	{
		$date		= date(config::start_date);
		$last_date  = date(config::last_date);
		if(strtotime(date('Y-m-d'))<=strtotime(config::last_date))
			$last_date = date('Y-m-d');
		
		$days_over = ceil((strtotime($last_date)- strtotime(config::start_date))/24/3600);
		$ratio = ($days_over/180)*100;
		$days_txt = "<b>עברו $days_over ימים מתחילת הטיול <br>(מתוך כ 180)</b>";

		$loading ="";// "<div style=\" border:2px solid; width:99%; background: linear-gradient(to left, #99FFCC $ratio%, #99CCFF $ratio%);\">$days_txt</div>"; 
 		
		$cur_month	= date ('m',strtotime($date));
		ob_start();
		 
		echo '<div class="main"><br><a href="javascript:void(0)" onclick="main(0);  return false;">Main</a>';
		
		if($edit)
				echo "<button class = \"btn btn-primary btn-small\" onclick=\"main(1); return false;\"\">Edit </button>";
		echo '<div>'; 
		?>
		<div class='main'><a href="javascript:void(0)" onclick="$('#<?php echo $cur_month;?>').toggle();"><?php echo date("F", mktime(0, 0, 0, $cur_month, 10))." ".config::year?></a></div>
		<div style="display:none;" id = "<?php echo $cur_month;?>">
		<?php 
		$i = 0;
		while(strtotime($date)<=strtotime(config::last_date))
		{
			$day	= date ('d',strtotime($date));
			$month	= date ('m',strtotime($date));
			echo "<br><br><a href=\"javascript:void(0)\" onclick=\"view('$day','$month');\">$day-$month-".config::year."</a>";
			if($edit)
				echo "<button class = \"btn btn-primary btn-small\" onclick=\"edit('$day','$month'); return false;\"\">Edit </button>";
			$date = date('Y-m-d', strtotime($date . " + 1 day"));
			if(date ('m',strtotime($date)) != $cur_month)
			{
				$cur_month = date ('m',strtotime($date));?>
			</div>
			<div class='main'><a href="javascript:void(0)" onclick="$('#<?php echo $cur_month;?>').toggle();"><?php echo date("F", mktime(0, 0, 0, $cur_month, 10))." ".config::year?></a></div>
			<div style="display:none;" id = "<?php echo $cur_month;?>">
				<?php
			}
		}
		echo 	"</div> 
		<br>$loading <div class=\"main\" style=\"margin:30px;\">";

			
		
		if(!$edit)
		{
			?>
				<form onsubmit="return false;">
				<input class="input-small" type="text" id="user" placeholder="user name">
				<input class="input-small" type ="password" id="pass" placeholder="password">
				<button class = "btn btn-primary btn-small" onclick="login()">Login</button>
				</form>
			<?php
		}
		else
			echo
				'<button class = "btn btn-primary btn-small" onclick="logout()">logout</button>';
		?></div>
			<script>$('#<?php echo $month_to_show;?>').show();</script><?php
		return ob_get_clean();
	}
	
	public static function edit($month, $day)
	{
		ob_start();
		echo "<h3> $day-$month-".config::year."</h3>";
		$dir_path = "$month/$day";
		if(!file_exists($month))
			mkdir($month);
		if(!file_exists("$month/$day"))
			mkdir("$month/$day");
		$text_file_name = "$dir_path/index.html";
		$text			= file_exists($text_file_name) ? stripcslashes(file_get_contents($text_file_name)) : NULL;
		?>
		<br>
		<div class='main' style="text-align: center;">
			<textarea id="text" name="text" placeholder="Please enter text"  rows="10" style="resize: none; width: 500px;" ><?php echo $text;?></textarea>
			<br>
		<button class="btn btn-primary" onclick="save_text('<?php echo $month;?>', '<?php echo $day;?>'); return false;">Save text</button>
		</div>
		<!--<input name="upload[]" type="file" multiple="multiple" accept="image/*"/>-->

			<?php echo tools::preview($month, $day, TRUE);?>
		
		<div class='main' style="text-align: center">
		<div id="append"></div>
		
		<div class='main' id="mulitplefileuploader">Upload</div>
		<div class='main' id="status"></div>
		<div class='main' style="padding: '10px';">

		<button class="btn btn-info" onclick="edit('<?php echo $day;?>', '<?php echo $month;?>'); return false;">Reload page</button>
		<button class="btn btn-info" onclick="backup('<?php echo $month;?>', '<?php echo $day;?>'); return false;">backup day</button>
		<button class="btn btn-info" onclick="backup('<?php echo $month;?>', 'x'); return false;">backup month</button>
		</div>
		</div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<script src="./lib/jquery.uploadfile.min.js"></script>
		<link href="./lib/uploadfile.css" rel="stylesheet">

		<script>
			var settings = {
				url: "upload.php",
				method: "POST",
				allowedTypes:"jpg",
				fileName: "myfile",
				formData: "<?php echo $month;?>/<?php echo $day;?>/",
				fileName: "myfile",
				multiple: true,
				onSuccess:function(files,data,xhr)
				{
//					$("#status").html("<font color='green'>Upload is success</font>");
					$("#append").append(data);

				},
				onError: function(files,status,errMsg)
				{		
					$("#status").html("<font color='red'>Upload is Failed</font>");
				}
			}
			$("#mulitplefileuploader").uploadFile(settings);

		</script>
		<?php
		return ob_get_clean();
	}

	public static function view($month, $day)
	{
		ob_start();
		echo "<h3> $day-$month-".config::year."</h3>";
		if(file_exists("./$month/") && file_exists("./$month/$day/"))
		{
			$rel_path = "$month/$day";
			$text_file_name = "$rel_path/index.html";
			$text			= file_exists($text_file_name) ? file_get_contents($text_file_name) : NULL;
			echo "<div class='main' style=\"text-align: center;\">
			".str_replace("\n", "<br>", stripslashes($text))."
				</div>";
			echo tools::preview($month, $day);
		}
		return ob_get_clean();
	}
	
	public static function main()
	{
		ob_start();

		$main_path= "main.txt";
		if(file_exists($main_path))
		{
			$text			= file_exists($main_path) ? file_get_contents($main_path) : NULL;
			echo "<div class='main' style=\" border: 1px solid #000000; color: #ffffff; text-shadow: 1px 1px #000000; margin:auto; padding-top:30px;  height:690px; width:610px; text-align: center;\">
			".str_replace("\n", "<br>", stripslashes($text))."
				</div>";
		}
		return ob_get_clean();
	}
	public static function edit_main()
	{
		ob_start();
		echo "<h3>Main</h3>";
		$main_path= "main.txt";
		$text			= file_exists($main_path) ? stripcslashes(file_get_contents($main_path)) : NULL;
		?>
		<br>
		<div class='main' style="text-align: center;">
			<textarea id="text" name="text" placeholder="Please enter text"  rows="10" style="resize: none; width: 500px;" ><?php echo $text;?></textarea>
			<br>
		<button class="btn btn-primary" onclick="save_main(); return false;">Save text</button>
		</div>
		<?php
		return ob_get_clean();
	}

public static function preview($edit_month, $edit_day, $edit = FALSE)
{
	$path = "./$edit_month/$edit_day/";
	$images_to_show = scandir($path);
	
	foreach ($images_to_show as $image_to_show)
	{
		if(in_array($image_to_show, array('.', '..')))
			continue;
		$type = strtolower(pathinfo("$path/$image_to_show", PATHINFO_EXTENSION));
		if(!in_array($type, array('jpg','png','gif')))
			continue;
		$data = file_get_contents("$path/$image_to_show");
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		$class = str_replace('.', '_', $image_to_show);
		echo "<div class='main' style=\"text-align: center; padding:10px;\">
				<img id =\"$class\"  class=\"$class\" src=\"$path/$image_to_show\" >";
		if($edit)
		{
			echo '<br><div class="'.$class.' main btn-group" data-toggle="buttons-radio" dir ="ltr">';
			echo "<button onclick =\"return rotate_image('$edit_month', '$edit_day', '$image_to_show','+');\" class=\"btn btn-danger\" name = \"delete\" value = \"$image_to_show\"> <i style =\"-moz-transform: rotateY(-180deg); -webkit-transform: rotateY(-180deg); transform: rotateY(-180deg);\" class=\" mirror-icon icon-repeat  icon-white\"></i> rotate</button>";
			echo "<button onclick =\"return delete_images('$edit_month', '$edit_day', '$image_to_show');\" class=\"btn btn-danger\" name = \"delete\" value = \"$image_to_show\">delete</button>";
			echo "<button onclick =\"return rotate_image('$edit_month', '$edit_day', '$image_to_show','-');\" class=\"btn btn-danger\" name = \"delete\" value = \"$image_to_show\">rotate<i class=\"icon-repeat icon-white\"></i> </button>";
			echo "</div>";
		}
		echo "</div>";
	}
	$next_date = strtotime(date(config::year."-$edit_month-$edit_day") . " + 1 day");
	$prev_date = strtotime(date(config::year."-$edit_month-$edit_day") . " - 1 day");
		echo "<div class='main' style=\"text-align: center; padding:10px;\">";
	$action= $edit ? 'edit' : 'view';
	echo '<div class="main btn-group" dir ="ltr">';
		if(strtotime(config::last_date)>=$next_date)
			echo "<button onclick =\"$action('".date('d',$next_date)."','".date('m',$next_date)."');\" class=\"btn btn-info\"  icon-white\"></i> next</button>";
		if(strtotime(config::start_date)<=$prev_date)
			echo "<button onclick =\"$action('".date('d',$prev_date)."','".date('m',$prev_date)."');\" class=\"btn btn-info\"  icon-white\"></i> back</button>";
			
	echo "</div>";
	echo "</div>";
}
	
}


