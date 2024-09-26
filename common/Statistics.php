<?php 
class Statistics
{
	function __construct()
	{
		
	}
	static function Correlation($x_arr, $y_arr)
	{
		//echo count($x)."<br>";
		//print_r($x);
		//print_r($y);
		$x=array_values($x_arr);
		$y=array_values($y_arr);
		$size=count($x);
		$total_x=array_sum($x);
		$total_y=array_sum($y);		
		$mean_x=$total_x/$size;
		$mean_y=$total_y/$size;
		$sum_1=0;
		$sum_2=0;
		$sum_3=0;
		for($i=0;$i<$size;$i++)
		{
			$sum_1+=($x[$i]-$mean_x)*($y[$i]-$mean_y);
			$sum_2+=pow(($x[$i]-$mean_x), 2);
			$sum_3+=pow(($y[$i]-$mean_y), 2);
		}
		if($sum_2==0 or $sum_3==0)
		{
			return "";
		}
		$corr=$sum_1/sqrt($sum_2*$sum_3);
		return $corr;
	}
}