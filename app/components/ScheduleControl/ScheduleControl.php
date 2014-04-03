<?php

namespace SokolMor\Components;

define('QUARTERS',96);
define('DAYS', 7);

/**
 * ScheduleControl
 *
 * @author Michal Fucik
 * @package SokolMor
 */

class ScheduleControl extends \Nette\Application\UI\Control {

	/** @var mixed array Schedule */
	private $__schedule;
	
	/** @var array of DibiRow Occupation data. */ // dibi fluent temporarily
	private $__occupation;
	
	/** @var array of quarters time definiton */
	private $__timeTable;
	
	/** @var IScheduleModel; */
	private $__model;
	
	/** @var Template file */
	private $__templateFile;
	
	private $__templateSectionFile;
	
	/** @var array Week days names. */
		private $__days = array(
			1 => 'pondělí',
			2 => 'úterý',
			3 => 'středa',
			4 => 'čtvrtek',
			5 => 'pátek',
			6 => 'sobota',
			7 => 'neděle'
		);
	
	public function setTemplate($fileName) {
		if (!file_exists($fileName))
			throw new \Nette\FileNotFoundException("Template \'$fileName\' not found."); 
		$this->__templateFile = $fileName;
	}
	
	public function setSectionTemplate($fileName) {
		if (!file_exists($fileName))
			throw new \Nette\FileNotFoundException("Template \'$fileName\' not found."); 
		$this->__templateSectionFile = $fileName;
	}
	
	public function setModel ($model) {
		if (!($model instanceof \SokolMor\Models\IScheduleModel))
			throw new \Nette\InvalidArgumentException("Argument has to be an \SokolMor\Models\IScheduleModel instance. Instance of ".get_class($model).' given.');
			
		$this->__model = $model;
		//$this->__occupation = $this->__model->getOccupation();
	}
	
	public function __construct($parent, $name) {
		parent::__construct($parent, $name);
		$this->__timeTable = array();
		
		for ($i = 1; $i <= QUARTERS; $i++) {
			
			$this->__timeTable[$i] = array(
									'begins' => $this->__getTime(($i-1)*15),
									'ends'=> $this->__getTime($i*15),
				//					'belongs' => $i // tohle je udano klicem v timeTable
				);
		}
	}
			
	public function render() {
		
		if (!isset($this->__model))
			throw new \Nette\InvalidStateException("Property \"__model\" is not set.");
		
		if (!isset($this->__templateFile))
			$this->__templateFile = '/schedule.latte';
		
		$this->beforeRender();
		
		$this->template->setFile(__DIR__ . $this->__templateFile);
		$this->template->occupation = $this->__occupation;
//		$affectedHours = array();
//		foreach ($this->__occupation as $o) {
//			array_push($affectedHours, $o['schedule_hour']);
//		}
//		$this->template->affected = $affectedHours; // quarters
		
		$this->template->render();
	}
	
	public function renderSection($id) {
		if (!isset($this->__model))
			throw new \Nette\InvalidStateException("Property \"__model\" is not set.");
		
		if (!isset($this->__templateSectionFile))
			$this->__templateSectionFile = '/scheduleSection.latte';
		
		$this->template->setFile(__DIR__ . $this->__templateSectionFile);
		$this->template->timeTable = $this->__timeTable;
		$this->template->days = $this->__days;
		$this->template->occupation = $this->__occupation = $this->__model
					->getOccupation()
					->where('schedule_section_id = %i', $id)
					->orderBy('schedule_day,','schedule_hour')
					->execute()->fetchAll();
		//$this->beforeRender();
		$occ = array();
		$lastPart = 0; // index pole v $occ[index_dne][$lastPart], do ktereho se naposledy vkladalo
		$lastQuarter = 0; // hodnota posledni vkladane ctvrthodiny
		
		foreach ($this->__occupation as $oc) {
				// kdyz je v $occ nastaveny den
				if (isset($occ[$oc['schedule_day']])) {
					
					// tak pridam do toho pole $oc
					// ale nejdriv se ptam, jestli neni prerusena casova souvislost casti
					if ($oc['schedule_hour'] - 1 == $lastQuarter || $lastQuarter == 0) {
						
					} else {
						// kdyz je prerusena (rozdil je vetsi jak 1), 
						// tak se zacne ukladat do dalsiho pole s indexem $lastPart
						$lastPart++;
					}
					// kdyz neni nastavena cast, 
					if (!isset($occ[$oc['schedule_day']][$lastPart]))
						//tak ji zalozim
						$occ[$oc['schedule_day']][$lastPart] = array();
						// a hned do ni vlozim
					array_push($occ[$oc['schedule_day']][$lastPart],$oc);
					$lastQuarter = $oc['schedule_hour'];

				} else {
					// kdyz neni nastaveny den, tak zalozim novy
					$occ[$oc['schedule_day']][$lastPart] = array($oc);
					$lastQuarter = $oc['schedule_hour'];
				}
		}
		
		//pole hodin musim na konci cyklu prohodit ptz budou serazena sestupne (kvuli array_push)
		foreach ($occ as $oc) {
			foreach ($oc as $o) {
				$o = array_reverse($o);
			}
		}
		
		$this->template->occDays = $occ;
		
		$this->template->render();
	}
	
	private function beforeRender() {}
	
	/**
		* Converts minutes to day time HH:MM. 
		* @param int minutes count
		*/
		protected function __getTime($minutes) {

			if ($minutes > 1440)
				throw new \RangeException('Value of $minutes argument has to be lower than 1440 (per day). It was %i.',$minutes);

			$mins = $minutes;
			$res = array('hrs' => 0, 'mins' => 0);
			if ($minutes == 0) return $res['hrs'].'0:0'.$res['mins'];
			while ($mins != 0) {
				if ($res['mins'] < 59) {
					$res['mins']++;				
				} else {
					$res['mins'] = 0;
					$res['hrs']++;
				}
				$mins -= 1;
			}
			
			$res['hrs'] = $res['hrs'] < 10 ? '0'.$res['hrs']: $res['hrs'];
			$res['mins'] = $res['mins'] < 10 ? '0'.$res['mins']: $res['mins'];
			
			return $res['hrs'].':'.$res['mins'];
		}		
}
//		/**
//		 * Schedule class constructor.
//		 * @param array DibiRow Occupation querters.
//		 */
//		public function __construct (array $occupation = array()) {
//			
//			if (!is_array($occupation))
//				throw new \Nette\InvalidArgumentException('Argument has to be array of \DibiRow instances and probably it is not.');
//			
//			$this->__occupation = $occupation;
//			
//			$r = array();
//			//dump($this->__occupation);
//			// pro kazdy den
//			for ($i = 1; $i <= DAYS; $i++) {
//				$quarters = array();
//				
//				// pro kazdou ctvrthodinu
//				for ($j = 1; $j <= QUARTERS; $j++) {
//					if (isset($this->__occupation[$j]) && $this->__occupation[$j]['schedule_day'] == $i-1) {	
//						//dump($this->__occupation[$j]);
//						dump($this->__occupation[$j]);
//						$quarters[$j] = ($this->__occupation[$j]->begins = $this->__getTime(($j-1)*15));
//						$quarters[$j] = ($this->__occupation[$j]->ends = $this->__getTime($j*15));
//					} else {
//						
//						$quarters[$j] = array(
//									'begins' => ($this->__getTime(($j-1)*15)),
//									'ends'=> ($this->__getTime($j*15)));
//					}
//				}
//				$r[$i] = //array(
//					//'label' => $this->__days[$i],
//				//	'quarters' =>
//				$quarters
//				//)
//				;
//			}

		
