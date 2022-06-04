<?php

// *** Include the class
include("resize-class.php");

function make_str($image_path, $fileName, $output_dir)
{
	$type = strtolower(pathinfo("$image_path", PATHINFO_EXTENSION));
	$data = file_get_contents("$image_path");
	$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
	$class = str_replace('.', '_', $fileName);
	$details = explode("/", $output_dir);
	$edit_month = $details[0];
	$edit_day = $details[1];
	return  "<div class='main' style=\"text-align: center; padding:10px;\">
			<img id=\"$class\" class=\"$class\" src=\"$base64\" >
		<br><div class=\"$class main btn-group\" data-toggle=\"buttons-radio\" dir =\"ltr\">
		<button onclick =\"return rotate_image('$edit_month', '$edit_day', '$fileName','+');\" class=\"btn btn-danger\" name = \"delete\" value = \"$fileName\"> <i style =\"-moz-transform: rotateY(-180deg); -webkit-transform: rotateY(-180deg); transform: rotateY(-180deg);\" class=\" mirror-icon icon-repeat  icon-white\"></i> rotate</button>
		<button onclick =\"return delete_images('$edit_month', '$edit_day', '$fileName');\" class=\"btn btn-danger\" name = \"delete\" value = \"$fileName\">delete</button>
		<button onclick =\"return rotate_image('$edit_month', '$edit_day', '$fileName','-');\" class=\"btn btn-danger\" name = \"delete\" value = \"$fileName\">rotate<i class=\"icon-repeat icon-white\"></i> </button>
		</div>
	</div>";
}

$users = array('ronen'=>'0508383423', 'oshrit'=>'0506411478');
if(!session_id())
	session_start();
$login		= isset($_SESSION['login'])  ? $_SESSION['login']	: NULL;
$edit		= array_key_exists($login, $users);

$output_dirs = array_keys($_POST);
if(!$output_dirs || !$edit)
{

	header('Location: ./');
	exit;
}

$output_dir = $output_dirs[0];
if (isset($_FILES["myfile"]))
{
	$ret = array();
	$error = $_FILES["myfile"]["error"];
	if(!$error)
	{
		if(strpos('image',$_FILES["myfile"]['type'] === FALSE))
			die(json_encode($ret));
		$str = NULL;
		if (!is_array($_FILES["myfile"]['name']))
		{ //single file
			$fileName = $_FILES["myfile"]["name"];
			move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $_FILES["myfile"]["name"]);
			$image_path = $output_dir . $fileName;
			resize_image($image_path);
			$ret[$fileName] = $image_path;
			$str .= make_str($image_path, $fileName, $output_dir);
		}else
		{
			$fileCount = count($_FILES["myfile"]['name']);
			for ($i = 0; $i < $fileCount; $i++) 
			{
				$fileName = $_FILES["myfile"]["name"][$i];

				$ret[$fileName] = $image_path;
				move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $image_path);
				$image_path = $output_dir . $fileName;
				resize_image($image_path);
				$str .= make_str($image_path, $fileName, $output_dir);
			}
		}
	}
	echo $str;
}


function resize_image($image_path)
{
	$resizeObj = new resize($image_path);
	$resizeObj -> resizeImage(600, 800, 'auto');
	$resizeObj -> saveImage($image_path, 100);

}