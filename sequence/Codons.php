<?php
class Codons
{    
	function GetCodonConcentration($data)
	{
		$this->set_codon2acid();
		foreach($this->acid2codon as $acid=>$codons)
		{
			$total=0;
			foreach($codons as $codon)
			{
				if(isset($data["$codon"]))
					$total+=$data["$codon"];
			}
			//echo "$acid - $total - $selected<br>";
			foreach($codons as $codon)
			{
				//echo "$acid - $codon<br>";
				if(!isset($data["$codon"]))
					$total_cn=0;
				else
					$total_cn=round($data["$codon"]/$total, 2);
				$out["$acid: $codon"]=$total_cn;
			}
		}
		return $out;
	}
	static function CheckORF($gene)
	{
		if(substr($gene, 0, 3)!="ATG")
			return false;
		if(strlen($gene)%3!=0)
			return false;
		$stop_codon=substr($gene, strlen($gene)-3, 3);
		if($stop_codon!="TGA" and $stop_codon!="TAA" and $stop_codon!="TAG")
			return false;
		for($i=0;$i<strlen($gene);$i+=3)
		{
			$codon=substr($gene, $i, 3);
			if(!isset($this->codon2acid["$codon"]))
			{
				return false;
			}
		}
		return true;
	}
	function CDS2Acids($seq)
	{
		$out="";
		$acids=array();
		$this->set_codon2acid();
		$codons=str_split($seq, 3);
		$codon_qt=count($codons);
		for($j=0;$j<count($codons)-1;$j++)
		{
			$acid=$this->codon2acid["$codons[$j]"];
			$code=$this->acid2code["$acid"];
			//echo "$codons[$j]: $acid: $code<br>";
			$out.=$code;
		}
		return $out;
	}
	function set_codon2acid()
	{
		$this->codon2acid["TCA"] = "Serine";
		$this->codon2acid["TCC"] = "Serine";
		$this->codon2acid["TCG"] = "Serine";
		$this->codon2acid["TCT"] = "Serine";
		$this->codon2acid["TTC"] = "Phenylalanine";
		$this->codon2acid["TTT"] = "Phenylalanine";
		$this->codon2acid["TTA"] = "Leucine";
		$this->codon2acid["TTG"] = "Leucine";
		$this->codon2acid["TAC"] = "Tyrosine";
		$this->codon2acid["TAT"] = "Tyrosine";
		$this->codon2acid["TAA"] = "Stop";
		$this->codon2acid["TAG"] = "Stop";
		$this->codon2acid["TGC"] = "Cysteine";
		$this->codon2acid["TGT"] = "Cysteine";
		$this->codon2acid["TGA"] = "Stop";
		$this->codon2acid["TGG"] = "Tryptophan";
		$this->codon2acid["CTA"] = "Leucine";
		$this->codon2acid["CTC"] = "Leucine";
		$this->codon2acid["CTG"] = "Leucine";
		$this->codon2acid["CTT"] = "Leucine";
		$this->codon2acid["CCA"] = "Proline";
		$this->codon2acid["CCC"] = "Proline";
		$this->codon2acid["CCG"] = "Proline";
		$this->codon2acid["CCT"] = "Proline";
		$this->codon2acid["CAC"] = "Histidine";
		$this->codon2acid["CAT"] = "Histidine";
		$this->codon2acid["CAA"] = "Glutamine";
		$this->codon2acid["CAG"] = "Glutamine";
		$this->codon2acid["CGA"] = "Arginine";
		$this->codon2acid["CGC"] = "Arginine";
		$this->codon2acid["CGG"] = "Arginine";
		$this->codon2acid["CGT"] = "Arginine";
		$this->codon2acid["ATA"] = "Isoleucine";
		$this->codon2acid["ATC"] = "Isoleucine";
		$this->codon2acid["ATT"] = "Isoleucine";
		$this->codon2acid["ATG"] = "Methionine";
		$this->codon2acid["ACA"] = "Threonine";
		$this->codon2acid["ACC"] = "Threonine";
		$this->codon2acid["ACG"] = "Threonine";
		$this->codon2acid["ACT"] = "Threonine";
		$this->codon2acid["AAC"] = "Asparagine";
		$this->codon2acid["AAT"] = "Asparagine";
		$this->codon2acid["AAA"] = "Lysine";
		$this->codon2acid["AAG"] = "Lysine";
		$this->codon2acid["AGC"] = "Serine";
		$this->codon2acid["AGT"] = "Serine";
		$this->codon2acid["AGA"] = "Arginine";
		$this->codon2acid["AGG"] = "Arginine";
		$this->codon2acid["GTA"] = "Valine";
		$this->codon2acid["GTC"] = "Valine";
		$this->codon2acid["GTG"] = "Valine";
		$this->codon2acid["GTT"] = "Valine";
		$this->codon2acid["GCA"] = "Alanine";
		$this->codon2acid["GCC"] = "Alanine";
		$this->codon2acid["GCG"] = "Alanine";
		$this->codon2acid["GCT"] = "Alanine";
		$this->codon2acid["GAC"] = "Aspartic";
		$this->codon2acid["GAT"] = "Aspartic";
		$this->codon2acid["GAA"] = "Glutamic";
		$this->codon2acid["GAG"] = "Glutamic";
		$this->codon2acid["GGA"] = "Glycine";
		$this->codon2acid["GGC"] = "Glycine";
		$this->codon2acid["GGG"] = "Glycine";
		$this->codon2acid["GGT"] = "Glycine";

		$this->acid2codon["Serine"]=array("TCA", "TCC", "TCG", "TCT", "AGC", "AGT");
		$this->acid2codon["Phenylalanine"]=array("TTC", "TTT");
		$this->acid2codon["Leucine"]=array("TTA", "TTG", "CTA", "CTC", "CTG", "CTT");
		$this->acid2codon["Tyrosine"]=array("TAC", "TAT");
		$this->acid2codon["Stop"]=array("TAA", "TAG", "TGA");
		$this->acid2codon["Cysteine"]=array("TGC", "TGT");
		$this->acid2codon["Tryptophan"]=array("TGG");
		$this->acid2codon["Proline"]=array("CCA", "CCC", "CCG", "CCT");
		$this->acid2codon["Histidine"]=array("CAC", "CAT");
		$this->acid2codon["Glutamine"]=array("CAA", "CAG");
		$this->acid2codon["Arginine"]=array("CGA", "CGC", "CGG", "CGT", "AGA", "AGG");
		$this->acid2codon["Isoleucine"]=array("ATA", "ATC", "ATT");
		$this->acid2codon["Methionine"]=array("ATG");
		$this->acid2codon["Threonine"]=array("ACA", "ACC", "ACG", "ACT");
		$this->acid2codon["Asparagine"]=array("AAC", "AAT");
		$this->acid2codon["Lysine"]=array("AAA", "AAG");
		$this->acid2codon["Valine"]=array("GTA", "GTC", "GTG", "GTT");
		$this->acid2codon["Alanine"]=array("GCA", "GCC", "GCG", "GCT");
		$this->acid2codon["Aspartic"]=array("GAC", "GAT");
		$this->acid2codon["Glutamic"]=array("GAA", "GAG");
		$this->acid2codon["Glycine"]=array("GGA", "GGC", "GGG", "GGT");
		
		$this->acid2code["Serine"] = "S";
		$this->acid2code["Phenylalanine"] = "F";
		$this->acid2code["Tyrosine"] = "Y";
		$this->acid2code["Stop"] = "_";
		$this->acid2code["Cysteine"] = "C";
		$this->acid2code["Tryptophan"] = "W";
		$this->acid2code["Leucine"] = "L";
		$this->acid2code["Proline"] = "P";
		$this->acid2code["Histidine"] = "H";
		$this->acid2code["Glutamine"] = "Q";
		$this->acid2code["Isoleucine"] = "I";
		$this->acid2code["Methionine"] = "M";
		$this->acid2code["Threonine"] = "T";
		$this->acid2code["Asparagine"] = "N";
		$this->acid2code["Lysine"] = "K";
		$this->acid2code["Arginine"] = "R";
		$this->acid2code["Valine"] = "V";
		$this->acid2code["Alanine"] = "A";
		$this->acid2code["Aspartic"] = "D";
		$this->acid2code["Glutamic"] = "E";
		$this->acid2code["Glycine"] = "G";
	}
}