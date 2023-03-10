<?php
class ExtractGenes
{
    function ExtractGenes($file, $params)
    {
        $is_json=(isset($params["is_json"])) ? 1 : 0;
        $f=fopen($file, "r");
        $seq="";
        $out=[];
        while($line=fgets($f))
        {
            if(strstr($line, ">"))
            {
                if($is_json)
                {
                    $header=json_decode(str_replace(">", "", trim($line)), true);
                    $seq_id=(isset($params["use_key"])) ? $header["{$params["use_key"]}"] :array_keys($header)[0];
                }
                else
                {
                    $pts=explode(" ", str_replace(">", "", trim($line)));
                    $header=array("sequence_id"=>$pts[0]);
                    $seq_id=$pts[0];
                }
                if($seq!="")
                {
                    $header["sequence"]=$seq;
                    $seq="";
                    $out["$seq_id"]=$header;
                }
                continue;
            }
            $seq.=trim($line);
        }
        return $out;
    }
}