<?php
include("ZipFolder.class.php");
include("tools.class.php");
if(!session_id())
	session_start();
$_SESSION['edit_main']	= isset($_SESSION['edit_main']) ? $_SESSION['edit_main'] : NULL;
$_SESSION['month']		= isset($_SESSION['month']) ? $_SESSION['month'] : '00';
$_SESSION['day']		= isset($_SESSION['day']) ? $_SESSION['day'] : '00';

	$users		= config::$users;
	$action		= isset($_POST['action'])	 ? $_POST['action']		: 'main';
	$edit_main	= isset($_POST['edit_main']) ? $_POST['edit_main']	: $_SESSION['edit_main'];
	$degree		= isset($_POST['degree'])	 ? $_POST['degree']		: NULL;
	$user		= isset($_POST['user'])		 ? $_POST['user']		: NULL;
	$pass		= isset($_POST['pass'])		 ? $_POST['pass']		: NULL;
	$month		= isset($_POST['month'])	&& $_POST['month']	 ? $_POST['month']		: $_SESSION['month'];
	$day		= isset($_POST['day'])		&& $_POST['day']	 ? $_POST['day']		: $_SESSION['day'];
	$text		= isset($_POST['text'])		 ? $_POST['text']		: NULL;
	$file_name	= isset($_POST['file_name']) ? $_POST['file_name']	: NULL;
	$login		= isset($_SESSION['login'])  ? $_SESSION['login']	: NULL;
	if($day == 'x' && $action != 'backup')
		$day = '01';
	$edit		= array_key_exists(strtolower($login), $users);
	if(!$edit &&  in_array($action, array('delete', 'save_text')))
		die(json_encode());
	switch($action)
	{
		case 'view':
			$_SESSION['action'] = 'view';
			break;
		case 'edit':
		case 'save_text':
		case 'save_main':
			$_SESSION['action'] = 'edit';
			break;
		case 'main':
			$_SESSION['action'] = 'main';
			break;
	}
//	die($_SESSION['action']);
	$_SESSION['edit_main'] = $edit_main;
	if($month)
		$_SESSION['month']	= $month;
	if($day)
		$_SESSION['day']	= $day;
	if($day == '00' && $month == '00')
	{
		if($action == 'edit' || $action == 'view')
			$action = 'main';
	}
	switch ($action)
	{
		case 'main':
		{
			if($edit && $edit_main)
			{
				echo json_encode(tools::edit_main());
				break;
			}
			echo json_encode(tools::main());
			break;
		}
		case 'login':
			if(array_key_exists(strtolower($user), $users) && $users[strtolower($user)] === $pass)
			{
				$_SESSION['login'] = strtolower($user);
				echo json_encode("$user login!!");
				break;
			}
			echo json_encode(":(");
			break;
		case 'logout':
			unset($_SESSION['login']);
			echo json_encode("$login logout!!");
			break;
		case 'save_text':
			if($edit)
				echo json_encode(tools::save_text($month, $day, $text));
			break;
		case 'save_main':
			if($edit)
				echo json_encode(tools::save_main($text));
			break;
		case 'backup':
			if($edit)
				echo json_encode(tools::backup($month, $day));
			break;
		case 'edit':
			if($edit)
			{
				echo json_encode(tools::edit($month, $day));
				break;
			}
		case 'view':
				echo json_encode(tools::view($month, $day));
			break;
		case 'menu':
			echo json_encode(tools::menu($edit, $month));
			break;
		
		case 'delete':
			if($file_name && $edit)
				echo json_encode(tools::delete($month, $day, $file_name));
			break;
		case 'rotate':
			if($file_name && $edit && $degree)
				echo json_encode(tools::rotate($month, $day, $file_name, $degree));
			break;
		default :
			echo json_encode("error");
	}

return;