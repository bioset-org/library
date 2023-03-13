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
	function GetComplementary($seq)
	{
		$out="";
		$len=strlen($seq);
		for($i=0;$i<$len;$i++)
		{
			$c="";
			$l=substr($seq, $i, 1);
			if($l=="A")
				$c="T";
			if($l=="C")
				$c="G";
			if($l=="G")
				$c="C";
			if($l=="T")
				$c="A";
			if($c=="")
				return;
			$out=$c.$out;
		}
		return $out;
	}
	function GetSequenceByNumber($num, $size)
	{
		$seq="";
		for ($i = 0; $i < $size; $i++)
		{
			$block_size = 1;
			for ($j = $i; $j < $size - 1; $j++)
				$block_size *= 4;
			$offset = $num / $block_size;
			//cout << i << " - "<<block_size<<" - "<<offset <<"- - "<< num<< "\n";
			if (!$offset)
				$seq .= 'A';
			else if ($offset == 1)
				$seq .= 'C';
			else if ($offset == 2)
				$seq .= 'G';
			else if ($offset == 3)
				$seq .= 'T';
			$num -= $block_size*$offset;
		}
		return $seq;
	}
	function GetSequenceBlockNumber($str, $size)
	{
		$loc = 0;
		for ($i = 0; $i<$size; $i++)
		{
			$offset = 1;
			for ($j = $i + 1; $j<$size; $j++)
				$offset *= 4;
			if ($str[$i] == 'N' || $str[$i] == 'N')
				return -1;
			if ($str[$i] == 'C')
				$loc += $offset;
			else if ($str[$i] == 'G')
				$loc += $offset * 2;
			else if ($str[$i] == 'T')
				$loc += $offset * 3;
			//cout << file_data[i + pos];
			//cout<<seq<<": "<<i<<", "<<offset<<", "<<loc<<"\n";
		}
		//blocks[loc]++;
		return $loc;
	}
}