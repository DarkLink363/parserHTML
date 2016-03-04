<?php
class ParserHTML{
	private $PATH;
	private $SITE;
	private $HTML;
	private $variables;
	private $functions;
	public 	$tags;
	
	public function __construct($page = null, $arg = null){
		$this->PATH = dirname(__FILE__);
		$thus->HTML = null;
		$tags = new stdClass();
		if($page !== null){
			if(file_exists($this->PATH . $page)){
				$this->SITE = $page;
			}else{
				throw new Exception('Site to parse not found.');
			}
		}else{
			throw new Exception('Site to parse is not set.');
		}
	}
	
	public function vars_ADD($before = null, $after = null){
		if($before !== null AND $after !== null){
			$this->variables[] = array('{'.$before.'}',$after);
		}else{
			throw new Exception('Bad method to add variable.');
		}
	}
	
	public function vars_SHOW(){
		foreach($this->variables as $vars){
			echo "Konwersja: ".$vars[0]." -> ".$vars[1]."<br>";
		}
	}
	
	public function vars_DUMP(){
		foreach($this->variables as $vars){
			var_dump($vars);
		}
	}
	
	public function functions_ADD($before = null, $func = null){
		if($before !== null AND $func !== null){
			$this->functions[] = array('{['.$before.']}',$func);
		}else{
			throw new Exception('Bad method to add function.');
		}
	}
	
	public function find($znacznik = null, $tryb = 0, $atr = null, $atrValue = null, $start = 0, $end = null){
		if($znacznik !== null){
			$html = '';
			if($this->HTML === null){
				$html = file_get_contents($this->PATH . $this->SITE);
			}else{
				$html = $this->HTML;
			}
			if($start > 0){
				if($end === null){
					$html = substr($html, $start);
				}else{
					$html = substr($html, $start, $end);
				}
			}
			preg_match_all('/<'.$znacznik.'(.*?)>(.*?)<\/'.$znacznik.'>/s', $html, $matches);
			if($tryb == 0){
				return $matches[2];
			}elseif($tryb == 1){
				if($atr !== null){
					$atr = strtolower($atr);
					$ret;
					$licznik = 0;
					foreach($matches[1] as $mat){
						$exp = explode('=', $mat);
						$c = count($exp);
						for($i=0;$i<$c;$i+=2){
							if($i<=$c){
								$exp[$i] = trim(strtolower($exp[$i]));
								if($atr == $exp[$i]){
									$ret[$licznik] = true;
								}
							}
						}
						if(!isset($ret[$licznik])){
							$ret[$licznik] = false;
						}
						$licznik++;
					}
					return $ret;
				}else{
					throw new Exception('Atribute is no set.');
				}
			}elseif($tryb == 2){
				if($atr !== null){
					$atr = strtolower($atr);
					$ret;
					$licznik = 0;
					foreach($matches[1] as $mat){
						$exp = explode('=', $mat);
						$c = count($exp);
						for($i=0;$i<$c;$i+=2){
							if($i<=$c){
								$exp[$i] = trim(strtolower($exp[$i]));
								if($atr == $exp[$i]){
									$ret[$licznik] = substr($exp[$i+1],1,-1);
								}
							}
						}
						if(!isset($ret[$licznik])){
							$ret[$licznik] = false;
						}
						$licznik++;
					}
					return $ret;
				}else{
					throw new Exception('Atribute is no set.');
				}
			}elseif($tryb == 3){
				if($atr !== null){
					$atr = strtolower($atr);
					$dane = $this->find($znacznik, 0);
					$atrybuty = $this->find($znacznik, 2, $atr);
					$a = count($atrybuty);
					$ret;
					for($i=0;$i<$a;$i++){
						if($atrybuty[$i] !== false){
							$ret[] = array($dane[$i],$atrybuty[$i]);
						}
					}
					return $ret;
				}else{
					throw new Exception('Atribute is no set.');
				}
			}elseif($tryb == 4){
				if($atr !== null AND $atrValue !== null){
					$atr = strtolower($atr);
					$dane = $this->find($znacznik, 0);
					$atrybuty = $this->find($znacznik, 2, $atr);
					$a = count($atrybuty);
					$ret;
					for($i=0;$i<$a;$i++){
						if($atrybuty[$i] == $atrValue){
							$ret[] = $dane[$i];
						}
					}
					return $ret;
				}else{
					throw new Exception('Atribute is no set.');
				}
			}
		}else{
			throw new Exception('Tag is not set.');
		}
	}
	
	public function pre_parse(){
		$html = file_get_contents($this->PATH . $this->SITE);
		$this->HTML = $html;
	}
	
	public function parse(){
		$html = '';
		if($this->HTML === null){
			$html = file_get_contents($this->PATH . $this->SITE);
		}else{
			$html = $this->HTML;
		}
		foreach($this->variables as $vars){
			$html = str_replace($vars[0], $vars[1], $html);
		}
		foreach($this->functions as $func){
			$out = eval($func[1]);
			$html = str_replace($func[0], $out, $html);
		}
		
		$this->HTML = $html;
	}
	
	public function show(){
		echo $this->HTML;
	}
}

?>
