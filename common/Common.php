<?php
class Common
{
	function __construct()
	{
	}
    static function DownloadFile($source, $dest)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,"$source");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_FTP_USE_EPSV, 0);
        // curl_setopt($curl, CURLOPT_PROXY,"192.168.192.1:3211");
		curl_setopt($curl, CURLOPT_TIMEOUT, 300);
		$outfile = fopen($dest, 'wb');
		curl_setopt($curl, CURLOPT_FILE, $outfile);
		$info = curl_exec($curl);
		fclose($outfile);
		curl_close($curl);
	}
	static function UnzipFile($source, $dest)
	{
		$zh = gzopen($source, 'r');
		$h = fopen($dest, 'w');
		if (!$zh) {
			echo 'Downloaded file could not be opened for reading';
			return;
		}
		if (!$h) {
			echo 'File could not be written';
			return;
		}
		while (($string = gzread($zh, 4096)) != false) {
			fwrite($h, $string, strlen($string));
		}
		gzclose($zh);
		fclose($h);
	}
}