<?php

// full path used to demonstrate it's root-path stripping ability
class ZipFolder {
    protected $zip;
    protected $root;
    protected $ignored_names;
    
    function __construct($file, $folder, $ignored=null) {
        $this->zip = new ZipArchive();
        $this->ignored_names = is_array($ignored) ? $ignored : $ignored ? array($ignored) : array();
        if ($this->zip->open($file, ZIPARCHIVE::CREATE)!==TRUE) {
            throw new Exception("cannot open <$file>\n");
        }
        $folder = substr($folder, -1) == '/' ? substr($folder, 0, strlen($folder)-1) : $folder;
        if(strstr($folder, '/')) {
            $this->root = substr($folder, 0, strrpos($folder, '/')+1);
            $folder = substr($folder, strrpos($folder, '/')+1);
        }
        $this->zip($folder);
        $this->zip->close();
    }
    
    function zip($folder, $parent=null) {
        $full_path = $this->root.$parent.$folder;
        $zip_path = $parent.$folder;
        $this->zip->addEmptyDir($zip_path);
        $dir = new DirectoryIterator($full_path);
        foreach($dir as $file) {
            if(!$file->isDot()) {
                $filename = $file->getFilename();
                if(!in_array($filename, $this->ignored_names)) {
                    if($file->isDir()) {
                        $this->zip($filename, $zip_path.'/');
                    }
                    else {
                        $this->zip->addFile($full_path.'/'.$filename, $zip_path.'/'.$filename);
                    }
                }
            }
        }
    }
	

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
	
}


