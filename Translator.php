<?php
/**
* Translator
* @auth: Monzurul Hasan
* @file: Translator.php
* @date: 02/06/2021
* @type: class
*/

class Translator{

  private $_langs = ['az','id','ms','bs','ca','ceb','ny','cy','cs','co','da','de','yo','et','en','es','eu','fil','jv','tk','fr','fy','ga','gl','gd','ha','hr','ht','sn','ig','zu','is','it','sw','ku','lv','haw','lt','hu','mg','mt','nl','no','pl','ro','sq','sk','sl','so','st','su','fi','sv','mi','vi','tr','xh','el','be','bg','kk','ky','mk','mn','ru','sr','tg','uk','uz','ka','hy','iw','yi','ur','ar','ps','fa','sd','am','ne','mr','hi','bn','pa','gu','ta','te','kn','ml','si','th','lo','my','km','ko','ja'];
  private $_source = null;
  private $_target = null;
  private $_query = null;
  public $error = null;


  public function setSource($source){
    if(in_array($source, $this->_langs) or strtolower($source) == 'auto'){
      $this->_source = $source;
    } else if(empty(trim($source))) {
      $this->error = "Source language is not defined";
    } else {
      $this->error = "Source language is not available";
    }
  }


  public function setTarget($target){
    if(in_array($target, $this->_langs)){
      $this->_target = $target;
    } else if(empty(trim($target))) {
      $this->error = "Target language is not defined";
    } else {
      $this->error = "Target language is not available";
    }
  }


  public function setQuery($query){
    if(!empty(trim($query))){
      $this->_query = $query;
    } else {
      $this->error = "Query not set";
    }
  }


  public function getTranslation(){
    $data = $this->getArrangedData();
    if($this->error){
      return json_encode(array('error' => true, 'message' => $this->error));
    } else {
      return $data;
    } 
  }

  private function getArrangedData(){
    $data = $this->requestTranslation();
    if($this->error){
      return;
    }
    if(empty(trim($data))){
      $this->error = "No data in response";
    } else {
      $jsonarray = json_decode($data);
      $trans_datas = $jsonarray[0];
      
      if(count($trans_datas) > 1){
        $last_index = count($trans_datas) - 1;
        
        if(empty(trim($trans_datas[$last_index][0]))){
          $main_pronunciation = "";
          for($j = 0; $j < count($trans_datas[$last_index]); $j++){
            $main_pronunciation .= $trans_datas[$last_index][$j];
          }
          
          if(count($trans_datas) - 1 > 1){
            $main_translation = "";
            for($i = 0; $i < count($trans_datas) - 1; $i++){
              $main_translation .= $trans_datas[$i][0];
            }
          } else {
            $main_translation = $trans_datas[0][0];
          }
        } else {
          if(count($trans_datas) > 1){
            $main_translation = "";
            for($i = 0; $i < count($trans_datas); $i++){
              $main_translation .= $trans_datas[$i][0];
            }
          } else {
            $main_translation = $trans_datas[0][0];
          }
        }
        
      } else if(count($trans_datas) == 1) {
        $main_translation = $trans_datas[0];
        $main_pronunciation = false;
      }
      
      return array(
        'error' => false,
        'source_lang' => ($this->_source == 'auto') ? 'auto ('.$jsonarray[2].')' : $this->_source,
        'target_lang' => $this->_target,
        'query_text' => $this->_query,
        'translation' => $main_translation,
        'pronunciation' => ($main_pronunciation) ? $main_pronunciation : "N/A"
      );
    }
  }

  private function requestTranslation(){
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&dt=rm&dt=t";
    
    $fields = array(
      'sl' => $this->_source,
      'tl' => $this->_target,
      'q' => $this->_query
    );

    $cu_opts = [
      CURLOPT_URL => $url,
      CURLOPT_POST => count($fields),
      CURLOPT_POSTFIELDS => http_build_query($fields),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "UTF-8",
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_HEADER => false,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_AUTOREFERER => true,
      CURLOPT_CONNECTTIMEOUT => 120,
      CURLOPT_TIMEOUT => 120,
      CURLOPT_MAXREDIRS => 10
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $cu_opts);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
  }
}
