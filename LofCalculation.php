<?php

class LofCalculation{
    public $data;
    public $k;
    public $distances;
    public $dis;
    public $nk=array();
    public $maxValue=array();
    public $neighbors=array();
    public $lrd=array();
    public $lof=array();
    public $outlier;
    function __construct($data,$k) {
       $this->data=$data;
       $this->k=$k;
       //print_r($this->data);
    }
    public function FindLof(){
        $this->distances=$this->data;
        array_walk($this->distances,array($this,'ManhattenDistance'),$this->data);
        
        
        for($i=0;$i<count($this->distances);$i++){
            $this->neighbors[$i] = $this->getNearestNeighbors($this->distances, $i, $this->k);
            $this->maxValue[$i]=max($this->neighbors[$i]);
    
            //print_r($this->neighbors);
            //Lrdk($maxValue[$i],$nk[$i],$distances,$neighbors,$i);
        }
        
        for($i=0;$i<count($this->distances);$i++){
            $this->nk[$i]=$this->NthK($this->neighbors[$i],$this->maxValue[$i]);
        }
        
        for($i=0;$i<count($this->distances);$i++){
            $this->lrd[$i]=$this->Lrdk($this->maxValue,$this->nk,$this->distances,$this->neighbors[$i],$i);
        }
        
        for($i=0;$i<count($this->distances);$i++){
            $this->lof[$i]=$this->Lof($this->lrd,$this->maxValue,$this->nk,$this->distances,$this->neighbors[$i],$i);
        }
        $this->outlier = max(array_keys($this->lof));
        echo $this->data[$this->outlier][2];
    }
    
    public function ManhattenDistance(&$sourceCoords, $sourceKey, $data){   
        $this->dis = array();
        list ($x1, $y1) = $sourceCoords;        

        foreach ($data as $destinationKey => $destinationCoords) {
        // Same point, ignore
            if ($sourceKey == $destinationKey) {
                continue;
            }
            list ($x2, $y2) = $destinationCoords;
            
            $this->dis[$destinationKey] = abs((int)$x1-(int)$x2)+abs((int)$y1-(int)$y2);
        }
        asort($this->dis);
        $sourceCoords = $this->dis;
    }
    
    public function getNearestNeighbors($distances, $key, $num){
        return array_slice($distances[$key], 0, $num, true);
    }
    
    public function NthK($neighbors,$nk){
        $count=0;
        foreach ($neighbors as $key=>$value) {
            if($value<=$nk){
                $count++;
            //echo "[".$key."] =>".$value." ";
            }
        }
    //echo "\n";
    /*for($i=0;$i<count($neighbors);$i++){
        if($neighbors[$i]<=$nk){
            $count++;
        }
    }*/
        return $count;
    }
    
    public function Lrdk($maxValue,$nk,$distances,$neighbors,$i){
        $sum=0;
        //print_r($maxValue);echo "\n";
        //print_r($neighbors);echo "\n";
        foreach($neighbors as $key=>$value){
            $sum+=max($maxValue[$key],$distances[$key][$i]);
        }
        return round($nk[$i]/$sum,3);
    }
    
    function Lof($lrd,$maxValue,$nk,$distances,$neighbors,$i){
        $sum1=0;
        $sum2=0;
        foreach($neighbors as $key=>$value){
            $sum1+=$lrd[$key];
        }
        foreach($neighbors as $key=>$value){
            $sum2+=max($maxValue[$key],$distances[$key][$i]);
        }
        return $sum1*$sum2;
    }
        
}

$csvFile='data.csv';
$data = readCsv($csvFile);


function readCsv($csvFile){
    $file_handle= fopen($csvFile,'r');
    while(!feof($file_handle)){
        $data[]= fgetcsv($file_handle,1024);
    }
    fclose($file_handle);
    return $data;
}

print_r($data);
$k=2;
$lof = new LofCalculation($data,$k);
$lof->FindLof();

?>