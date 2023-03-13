<?php
class Sequence
{
    static function GetComplementary($seq)
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
}