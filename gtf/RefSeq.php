<?php
// $chr_class = new CreateGene;
// $chr_class->CreateChrs("genomes/TAIR10.1");
// echo "The process is completed! Chromosome is sorted!!!";
// $chr_class->CreateGenes("genomes/TAIR10.1");
// echo "The process is completed! Genes are created!!!";
class RefSeq
{
    function __construct()
    {
        $this->genomes_folder="genomes";
    }
    function Retrieve($assembly)
    {
        $file=fopen(__DIR__."/assembly_summary_refseq.txt", "r");
        while($line=fgets($file, 1000000))
        {
            $pts=explode("\t", trim($line));
            if($pts[0]==$assembly)
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
        $this->download_file("$url/{$accession}_{$assembly}_genomic.fna.gz", "$this->genomes_folder/$folder/genomic.fna");
        $this->download_file("$url/{$accession}_{$assembly}_genomic.gtf.gz", "$this->genomes_folder/$folder/genomic.gtf");
        $path="$this->genomes_folder/$folder";
        $this->CreateChrs($path);
        $this->CreateGenes($path);
        if(file_exists("$path/genomic.fna"))
            unlink("$path/genomic.fna");
        if(file_exists("$path/genomic.gtf"))
            unlink("$path/genomic.gtf");
        return $folder;
    }
	function download_file($source, $dest)
	{
        echo "$source<br>";
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
        curl_setopt($curl, CURLOPT_PROXY,"192.168.192.1:3211");
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
