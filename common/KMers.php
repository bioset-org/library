<?php
class KMers
{    
	static function GetSequenceByNumber($num, $size)
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