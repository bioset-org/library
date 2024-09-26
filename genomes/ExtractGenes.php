<?php
class ExtractGenes
{
    function GetSequences($file, $params)
    {
        $is_json=(isset($params["is_json"])) ? 1 : 0;
        if(!isset($params["gz"]))
            $f=fopen($file, "r");
        else
            $f=gzopen($file, "r");
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
                    $header["header"]=trim($line);
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