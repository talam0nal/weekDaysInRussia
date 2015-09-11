<?php
	class weekDaysInRussia {
		protected $workingHoursPerDay = 8;
		protected $redLettersApiURL = 'http://basicdata.ru/api/json/calend/';
		protected $weekEndDays = ['Sat', 'Sun'];
		protected $russianMonthInPrepositional = [
			'',
			'январе',
			'феврале',
			'марте',
			'апреле',
			'мае',
			'июне',
			'июле',
			'августе',
			'сентябре',
			'октябре',
			'ноябре',
			'декабре'
		];

		/*
			Демо
		*/
		function weekDaysInRussia() {
			echo "В ".$this->currentRussianMonthInPrepositional();
			echo " ";
			echo $this->numberOfWorkDaysInCurrentMonth();
			echo " рабочих дня и ";
			echo $this->numberOfWorkingHoursInThisMonth();
			echo " рабочих часов<br>";
			echo "Текущий рабочий день: ";
			echo $this->currentWorkingDay();
		}

		/*
			Возвращает список месяцев в которых есть праздники
		*/
		function monthInThisYearWithRedLetterDays() {
			$apiResponse = $this->getRussianRedLetterDays();
			$months = [];
			foreach ($apiResponse->data->{date('Y')} as $key => $value) $months[] = $key;
			return $months;
		}

		/* 
			Если в текущем месяце есть праздники, возвращает true
		*/
		function currentMonthHaveRedLettersDays() {
			return in_array(11, $this->monthInThisYearWithRedLetterDays());
		}

		/*
			Возвращает сокращённые и выходные дни в текущем месяце
		*/
		function redLetterDaysInThisMonth() {
			$apiResponse = $this->getRussianRedLetterDays();
			$days = [];
			foreach ($apiResponse->data->{date('Y')}->{11} as $key => $value) {
				if ($value->isWorking === 2) $days[] = $key;
			}
			return $days;
		}

		function thisDayIsHoliday($day) {
			return in_array($day, $this->redLetterDaysInThisMonth());
		}

		/*
			Делает запрос к API со списком праздничных дней в России
		*/
		function getRussianRedLetterDays() {
			$data = json_decode(file_get_contents($this->redLettersApiURL));
			return $data;
		}

		/*
			Возвращает текущий месяц на русском языке в предложном падеже
		*/
		function currentRussianMonthInPrepositional() {
			$month = date('n');
			$array = $this->russianMonthInPrepositional;
			return $array[$month];
		}

		/*
			Возращает количество рабочих дней в текущем месяце
		*/		
		function numberOfWorkDaysInCurrentMonth() {
			$array = $this->workAndWeekendDaysInThisMonth();
			return $array[0];
		}

		/*
			Возвращает количество рабочих часов в текущем месяце 
		*/
		function numberOfWorkingHoursInThisMonth() {
			$array = $this->workAndWeekendDaysInThisMonth();
			return $array[0]*$this->workingHoursPerDay;		
		}

		/*
			Возращает текущий рабочий день в месяце
		*/
		function currentWorkingDay() {
			$array = $this->workAndWeekendDaysInThisMonth();
			return $array[1];
		}

		/*
			Метод для расчёта количества рабочих дней в текущем месяце
			Работает пока что только с "простыми" месяцами, в которых нет праздничных дней.
			Для просчёта "сложных" месяцев будет использоваться API
			Возвращает одномерный простой массив
			- 0 - количество рабочих дней в текущем месяце
			- 1 - текущий будний день в этом месяце
		*/
		function workAndWeekendDaysInThisMonth() {
			$numberOfWorkDaysInCurrentMonth = 1;
			
			for ($day=0; $day < date('t'); $day++) {
				$dayPlusOne = $day+1;
				$timestamp = strtotime($dayPlusOne."."."11".".".date('Y'));

				if ( $this->currentMonthHaveRedLettersDays() ) {
					if ( $this->thisDayIsHoliday($dayPlusOne) ) {

					} else {
						if ( !in_array(date('D', $timestamp), $this->weekEndDays) ) {
							$numberOfWorkDaysInCurrentMonth++;
						}

						if ($timestamp === strtotime(date('j.11.Y'))) {
							$currentWorkingDayInThisMonth = $numberOfWorkDaysInCurrentMonth-1;
						}

					}
				} else {
					//особой проверки не требуется, следовательно работаем по обычному алгоритму
					if ( !in_array(date('D', $timestamp), $this->weekEndDays) ) {
						$numberOfWorkDaysInCurrentMonth++;
					}

					//Если текущий день совпадает с днём в текущей итерации,
					//то вычисляем порядковый номер рабочего дня в этом месяце
					if ($timestamp === strtotime(date('j.n.Y'))) {
						$currentWorkingDayInThisMonth = $numberOfWorkDaysInCurrentMonth-1;
					}

				}


			}

			$numberOfWorkDaysInCurrentMonth = $numberOfWorkDaysInCurrentMonth-1;

			return [
				$numberOfWorkDaysInCurrentMonth,
				$currentWorkingDayInThisMonth
			];
		}
	}

	$initialize = new weekDaysInRussia;
?>