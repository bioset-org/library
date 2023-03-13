<?php
//File types: genomic.fna, assembly_report.txt, rna_from_genomic.fna.gz, cds_from_genomic.fna.gz
class RefSeq
{
    function __construct()
    {
    }
    function Retrieve($assembly, $folder, $file_types)
    {
        $this->genomes_folder=$folder;
        $file=gzopen(__DIR__."/refseq_summary.gz", "r");
        while($line=fgets($file, 1000000))
        {
            $pts=explode("\t", trim($line));
            if($pts[0]==$assembly or strtolower($pts[1])==strtolower($assembly))
            {
                $url="https://ftp.ncbi.nlm.nih.gov/genomes/all/".$pts[3];
                $accession=$pts[0];
                $assembly=$pts[2];
                $folder=str_replace(" ", "_", $pts[1]);
                $folder=str_replace("'", "", $folder);
                $folder=str_replace("/", "", $folder);
            }
        }
        if(!isset($url) or $url=="")
        {
            echo "Genome for $assembly not found!";
            return;
        }
        if(!file_exists("$this->genomes_folder/$folder"))
            mkdir("$this->genomes_folder/$folder");
        $assembly=str_replace(" ", "_", $assembly);
        foreach($file_types as $type=>$out_name)
            $this->download_file("$url/{$accession}_{$assembly}_$type", "$this->genomes_folder/$folder/$out_name");
        $path="$this->genomes_folder/$folder";
        return $folder;
    }
    function ParseReport($file, $filters)
    {
        $out=[];
        $data=file($file);
        foreach($data as $str)
        {
            $pts=explode("\t", $str);
            if(count($pts)<5)
                continue;
            $record=array("name"=>$pts[0], "type"=>$pts[3], "refseq_id"=>$pts[6]);
            if(count($filters))
            {
                $found=0;
                foreach($filters as $p=>$v)
                {
                    if($record["$p"]==$v)
                    {
                        $found=1;
                        break;
                    }
                }
                if(!$found)
                    continue;
            }
            $out[]=$record;
        }
    }
    function UpdateSummary()
    {
        $tmp=__DIR__."/summary.txt";
        $this->download_file("https://ftp.ncbi.nlm.nih.gov/genomes/refseq/assembly_summary_refseq.txt", $tmp);
        $f=fopen($tmp, "r");
        $out=gzopen(__DIR__."/refseq_summary.gz", "w");
        while($line=fgets($f))
        {
            $pts=explode("\t", $line);
            $pts[19]=str_replace("https://ftp.ncbi.nlm.nih.gov/genomes/all/", "", $pts[19]);
            gzwrite($out, "$pts[0]\t$pts[7]\t$pts[15]\t$pts[19]\n");
        }
        gzclose($out);
        unlink($tmp);
    }
	function download_file($source, $dest)
	{
        $tmp_dest=$dest;
        if(strstr($source, ".gz"))
            $tmp_dest="$dest.gz";
		Common::DownloadFile($source, $tmp_dest);
        if(strstr($source, ".gz"))
        {
		    Common::UnzipFile($tmp_dest, $dest);
            if(file_exists($tmp_dest))
                unlink($tmp_dest);
        }
	}
}
?>
