<?php
class KMers
{   
	function CalculateFASTAKMers($file, $size, $gz=0, $filters=[])
	{
		if($gz)
			$f=gzopen($file, "r");
		else
			$f=fopen($file, "r");
		$calculate=1;
		$last="";
		$id="";
		while($line=fgets($f))
		{
			$line=trim($line);
			if(strstr($line, ">"))
			{
				if(isset($kmers) and $id!="" and $calculate==1)
					$out["$id"]=$kmers;
				$pts=explode(" ", $line);
				$id=str_replace(">", "", $pts[0]);
				$kmers=$this->PrepareKMerList($size);
				if(count($filters) and !isset($filters["include"]["$id"]))
					$calculate=0;
				else
					$calculate=1;
				continue;
			}
			if(!$calculate)
				continue;
			$line=strtoupper($line);
			$line=$last.$line;
			$len=strlen($line);
			for($i=0;$i<$len-$size;$i++)
			{
				$kmer=substr($line, $i, $size);
				$pos=self::GetSequenceBlockNumber($kmer, $size);
				if($pos==-1)
					continue;
				$kmers[$pos]++;
			}
			$last=substr($line, $len-($size-1));
		}
		if(isset($kmers) and $id!="" and $calculate==1)
			$out["$id"]=$kmers;
		return $out;
	}
	function PrepareKMerList($size)
	{
		$out=[];
		$qt=pow(4, $size);
		for($i=0;$i<$qt;$i++)
		{
			$out[]=0;
		}
		return $out;
	}
	static function GetSequenceByNumber($num, $size)
	{
		$seq="";
		for ($i = 0; $i < $size; $i++)
		{
			$block_size = 1;
			for ($j = $i; $j < $size - 1; $j++)
				$block_size *= 4;
			$offset = floor($num / $block_size);
			// echo "i - $i - block_size - $block_size - offset - $offset - num - $num\n";
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
	static function GetSequenceBlockNumber($str, $size)
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