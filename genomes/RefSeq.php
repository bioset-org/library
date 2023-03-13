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
        $file=fopen(__DIR__."/assembly_summary_refseq.txt", "r");
        while($line=fgets($file, 1000000))
        {
            $pts=explode("\t", trim($line));
            if($pts[0]==$assembly or strtolower($pts[7])==strtolower($assembly))
            {
                $url=$pts[19];
                $accession=$pts[0];
                $assembly=$pts[15];
                $folder=str_replace(" ", "_", $pts[7]);
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
            $this->download_file("$url/{$accession}_{$assembly}_$type.gz", "$this->genomes_folder/$folder/$out_name");
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
	function download_file($source, $dest)
	{
		$this->DownloadFile($source, "$dest.gz");
		$this->UnzipFile("$dest.gz", $dest);
		if(file_exists("$dest.gz"))
			unlink("$dest.gz");
	}
    function DownloadFile($source, $dest)
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
	function UnzipFile($source, $dest)
	{
		$zh = gzopen($source, 'r');
		$h = fopen($dest, 'w');
		if (!$zh) {
			return __('Downloaded file could not be opened for reading.', 'geoip-detect');
		}
		if (!$h) {
			return sprintf(__('Database could not be written (%s).', 'geoip-detect'), $outFile);
		}
		while (($string = gzread($zh, 4096)) != false) {
			fwrite($h, $string, strlen($string));
		}
		gzclose($zh);
		fclose($h);
	}
}
?>
