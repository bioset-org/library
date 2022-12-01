<?php
class GTFConverter
{
    function CreateChrs($fasta_file, $out_path)
    {
        mkdir("$out_path/chrs");
        $fna=fopen($fasta_file, "r");
        while($line=fgets($fna, 1000000))
        {
            $line=trim($line);
            if (strstr($line, '>'))
            {
                $pts=explode(" ", $line);
                $chr=$pts[0];
                // if(strstr($chr, "NW"))
                //     continue;
                $chr=str_replace(".","_", $chr);
                $chr=str_replace(">","",$chr);
                $chr_stat["$chr"]=array("A"=>0, "C"=>0, "T"=>0, "G"=>0);
                $out_file=fopen("$out_path/chrs/$chr.fa", "w");
                continue;
            }
            // if(strstr($chr, "NW"))
            //         continue;
            $n_line=strtoupper($line);
            for($i=0;$i<strlen($line);$i++)
            {
                if(!isset($chr_stat["$chr"]["$line[$i]"]))
                    continue;
                $chr_stat["$chr"]["$line[$i]"]++;
            }
            fwrite($out_file, $line);
        }
        file_put_contents("$out_path/chr_stat",json_encode($chr_stat));
    }    
    function CreateGenes($path, $gtf_file, $gene_key="gene_id")
    {
        mkdir("$path/genes");//
        mkdir("$path/intergenes");//
        $out_file=fopen("$path/genes/gene.fa", "w");//
        $intergene_file=fopen("$path/intergenes/intergene.fa", "w");//
        $gtf=fopen($gtf_file, "r");//
        $error=fopen("$path/error.txt", "w");
        $qt=0;
        $findline=0;
        while($line=fgets($gtf, 1000000))//
        {
            $findline++;
            if ($line[0]=="#")
                continue;
            $pts = explode("\t", $line);
            if($pts[2]=="gene")
            {
                $start=$pts[3]-1;
                $end=$pts[4];
                $strand=$pts[6];              //
                $description = $this->ParseDescription($pts[8]);//
                // if ($description==false)
                //     {continue;}
                $gene_name=$description["$gene_key"];//
                $chr=str_replace(".","_",$pts[0]);//                
                // if(strstr($chr, "NW"))
                //     continue;
                $r_start=$start;
                if(!isset($last_inter["$chr"]))
                {
                    $intergene_size["$chr"]=0;
                    $gene_size["$chr"]=0;
                    $last_inter["$chr"]=0;
                }
                if($start<$last_inter["$chr"] and $end<$last_inter["$chr"])
                {
                    // echo "overlaps, continue<br>";
                    continue;
                }
                $t=$start-$last_inter["$chr"];
                // echo "$chr, $gene_name: $start - $end: $t ({$intergene_size["$chr"]}, {$gene_size["$chr"]})<br>";
                if($start<$last_inter["$chr"] and $end>$last_inter["$chr"])
                    $r_start=$last_inter["$chr"];
                if(!isset($files["$chr"]))
                    $files["$chr"]=fopen("$path/chrs/$chr.fa", "r");//
                fseek($files["$chr"], $last_inter["$chr"]);
                $intergene=fread($files["$chr"], $start-$last_inter["$chr"]);
                $intergene_size["$chr"]+=$start-$last_inter["$chr"];
                $intergene_data=array("Next Gene"=>$gene_name,"Chromosome"=>$chr,"Strand"=>$strand,"Start"=>$last_inter["$chr"],"End"=>$start);
                $json_data=json_encode($intergene_data);
                fwrite($intergene_file,">$json_data\n$intergene\n");
                $last_inter["$chr"]=$end;              //
                unset($intergene);

                fseek($files["$chr"], $r_start);
                if($end<$start)
                {
                    echo "\n Mechanical ERR: ". $description["$gene_key"] . " \n";
                    $mes="\n Mechanical ERR: ". $description["$gene_key"] . " \n";
                    fwrite($error, $mes);
                }
                $gene=fread($files["$chr"], $end-$r_start);//
                $gene_size["$chr"]+=$end-$r_start;
                // if(strlen($gene)>10000)
                // {
                //     if($strand=="+")
                //     {
                //         $gene=substr($gene,0,10000);
                //     }
                //     else
                //     {
                //         $gene=substr($gene, -10000);
                //     }
                // }
                if($strand=="-")
                    $gene=$this->Get_Complementary($gene);
                // if(isset($added["$gene_name"]))
                //     continue;
                $qt++;
                $added["$gene_name"]=1;
                $gene_data=array("Gene"=>$gene_name,"Chromosome"=>$chr,"Strand"=>$strand,"Start"=>$start,"End"=>$end);
                $json_data=json_encode($gene_data);
                if($qt%1000==0)
                    echo "$qt\n";
                fwrite($out_file,">$json_data\n$gene\n");                //
            }
        }
        fclose($error);
    }
    function ParseDescription($descript)//
    {
        $descript = trim($descript);
        $pts = explode(";", $descript);
        if ($pts[0]=="")
            return false;
        $pts=explode("\"",$pts[0]);
        if(count($pts)<2)
            $pts=explode("=", $pts[0]);
        $parametr=trim($pts[0]);
        $value=trim($pts[1]);
        $out["$parametr"]=$value;
        return $out;
    }
    function Get_Complementary($sequence)
    {
        $out="";
        $len=strlen($sequence);
        for ($i=0;$i<$len;$i++)
        {
            $c="";
            $l=substr($sequence, $i, 1);
            if($l=="A")
                $c="T";
            else if($l=="C")
                $c="G";
            else if($l=="G")
                $c="C";
            else if($l=="T")
                $c="A";
            else if($l=="a")
                $c="t";
            else if($l=="c")
                $c="g";
            else if($l=="g")
                $c="c";
            else if($l=="t")
                $c="a";
            else if($l=="")
                $c="N";
            else
                $c="N";
            $out=$c.$out;
        }
        return $out;
    }	
}