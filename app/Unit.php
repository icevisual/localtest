<?php
class UnitBase implements ArrayAccess{

	protected  $_attr ;

	protected  $_name ;

	public static $_attributes = [
			'HP' => 100,
			'attack' => 1,
			'defence' => 1,//伤害减少 （装甲值 * 0.06）／（装甲值 * 0.06 ＋ 1）
			'miss rate'	=> 10, //攻击丢失率
			'crit rate'	=> 1, //暴击率
			'dodge rate' => 1, //闪避率
			'attack speed' => 1, //attack 1 time  per second
	];

	public function init(){
		$this->_attr = static::$_attributes;
	}

	public function __construct($name ,array $initData = []){
		$this->_name = $name;
		if(empty($initData)){
			$this->init();
		}else {
			$initData = $initData + self::$_attributes;
			if(count($initData) > count(self::$_attributes) ){
				throw new \Exception('Attr Number Error');
			}
			$this->_attr = $initData;
		}
	}

	public function injured($damage){
		echo $this->_name.' is hurted at '.
				$damage.' Point , decrease '.
				($damage > $this['HP'] ? $this['HP'] :$damage).' HP,';
		$this['HP'] -= floatval($damage);
		echo 'Rest HP '.$this['HP'].PHP_EOL;
		if($this['HP'] <= 0 ){
			$this->died();
		}
	}

	public function alive(){
		return $this['HP'] > 0 ;
	}

	public function died(){
		echo $this->_name.' Died'.PHP_EOL;
	}

	public function getName(){
		return $this->_name;
	}

	public function __get($key){
		if(isset($this->_attr[$key])){
			return $this->_attr[$key];
		}
		return false;
	}


	public function getAttack(){
		$waveRange = mt_rand(1,100) > 50 ? -1 : 1;
		return $this->_attr['attack'] + $waveRange * mt_rand(1,10);
	}

	/**
	 * @param offset
	 */
	public function offsetExists ($offset) {
		return isset($this->_attr[$offset]);
	}

	/**
	 * @param offset
	 */
	public function offsetGet ($offset) {
		$method = 'get'.ucfirst($offset);
		if(method_exists($this, 'get'.ucfirst($offset))){
			return call_user_func(array($this,$method));
		}
		return isset($this->_attr[$offset]) ? $this->_attr[$offset] : null;
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet ($offset, $value) {
		$this->_attr[$offset]  = $value;
	}

	/**
	 * @param offset
	 */
	public function offsetUnset ($offset) {
		unset($this->_attr[$offset]);
	}

}

class RPGPersonUnit extends \UnitBase{

}

class RPGCommon {

	public static function trace($msg){
		static $_trace = [];

		$_trace[] = $msg;
	}


	public static function getGCD($a,$b){
		$big = $a > $b ? $a : $b;
		$small = $a >= $b ? $b : $a;
		$mod = $big%$small ;
		while ($mod > 0 ){
			$big = $small;
			$small = $mod;
			$mod = $big%$small ;
		}
		return $small;
	}

	public static function battle2(\UnitBase $ua,\UnitBase $ub){
		$aSpeed = intval($ua['attack speed'] * 100) ;
		$bSpeed = intval($ub['attack speed'] * 100) ;

		$step = \RPGCommon::getGCD($aSpeed, $bSpeed);

		$timeLine = 0;

		while($ua->alive() && $ub->alive()){
			$timeLine += $step;
			if($timeLine % $aSpeed == 0){
				$damage = static::attack($ua, $ub);
			}
			if(! ($ua->alive() && $ub->alive() ) ){
				break;
			}
			if($timeLine % $bSpeed == 0){
				$damage = static::attack($ub, $ua);
			}
		}
		exit;

	}


	public static function multiple_time($time,callable $call,$param){
		$result = [];
		for($i = 0 ; $i < $time ;$i ++ ){
			$rt = call_user_func_array($call, $param);
			$rt = 'H '.$rt;
			if(isset($result[$rt])){
				$result[ $rt ] ++;
			}else{
				$result[ $rt ] = 1;
			}
		}
		return $result;
	}

	/**
	 * Determine whether you hit the rate or not
	 * @param float $rate
	 * rate (0-100)
	 * @return boolean
	 */
	public static function hitRandom($rate){
		if($rate >= 100) return true;
		$max = $rate * 1000000;
		if(mt_rand(1,100000000) <= $max){
			return true;
		}
		return false;
	}

	/**
	 * Calculate hit rate
	 * @param float $miss
	 * 	miss rate
	 * @param float $dodge
	 *  dodge rate
	 * @return <b>number</b> float from 0 to 100
	 */
	public static function hitRate($miss,$dodge){
			
		return 100 - ($miss * $dodge / 100 + $dodge ) ;//100 - 100 * $miss * $dodge /10000.0 ;
	}


	public static function critHit($critRate){
		$multiple = 1;
		if(static::hitRandom($critRate)){
			$multiple = 2;
			$critRate = $critRate * 1.25;
		}
		if(static::hitRandom($critRate * $critRate /100)){
			$multiple = 3;
		}
		if(static::hitRandom($critRate * $critRate * $critRate /10000)){
			$multiple = 4;
		}
		if(static::hitRandom($critRate * $critRate * $critRate * $critRate /1000000)){
			$multiple = 5;
		}
		return $multiple;
	}

	/**
	 * Calculate attack damage
	 * @param \UnitBase $attacker
	 * @param \UnitBase $defender
	 * @return number
	 */
	public static function attack(\UnitBase $attacker ,\UnitBase $defender){
		$def 	 = $defender['defence'];
		$atk 	 = $attacker['attack'];
		$miss 	 = $attacker['miss rate'];
		$dodge 	 = $defender['dodge rate'];
		$hitRate = static::hitRate($miss,$dodge);
		$damage  = 0;

		$msg 	 = [];
		$msg ['title']  = $attacker->getName().' attack '.$defender->getName();
		if(static::hitRandom($hitRate) > 0 ){
			$multiple = 1;
			$msg ['hit']  =  'Hit';
			$multiple = static ::critHit($attacker['crit rate']);
			$multiple > 1 && $msg ['hit'] = 'Crit Hit '.($multiple - 1);
			$damage = $multiple * $atk * (1 - ($def * 0.06 ) / ($def * 0.06 + 1 )  ) ;
			$damage = bcmul ( $damage, 1, 4);
		}else{
			$msg ['hit']  =  'Miss';
		}
		$msg ['damage']  = $damage;
		echo "{$msg['title']} , {$msg['hit']} ". ($msg ['damage'] ? ' '.$msg ['damage'] : '').PHP_EOL;
		//Miss
		//Crit
		$damage > 0 && $defender->injured($damage);
		return $damage;
	}
}