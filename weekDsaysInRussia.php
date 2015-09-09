<?php
	class weekDaysInRussia {

		function weekDaysInRussia() {
			/*
				Демо
			*/
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
			Возвращает список русских месяцев в предложном падеже
		*/
		function russianMonthInPrepositional() {
			return [
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
	}

	/*
		Возвращает текущий месяц на русском языке в предложном падеже
	*/
	function currentRussianMonthInPrepositional() {
		$month = date('j');
		$array = $this->russianMonthInPrepositional();
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
		return $array[1];		
	}

	/*
		Возращает текущий рабочий день в месяце
	*/
	function currentWorkingDay() {
		$array = $this->workAndWeekendDaysInThisMonth();
		return $array[2];
	}

	/*
		Метод для расчёта количества рабочих дней в текущем месяце
		Работает пока что только с "простыми" месяцами, в которых нет праздничных дней.
		Для просчёта "сложных" месяцев будет использоваться API http://basicdata.ru/api/calend/
		Возвращает одномерный простой массив
		- 0 - количество рабочих дней в текущем месяце
		- 1 - количество рабочих часов в текущем месяце
		- 2 - текущий будний день в этом месяце
		API будет возвращать данные в JSON:
			,"2015":{
			"1":{"1":{"isWorking":2},
			"2":{"isWorking":2},
			"5":{"isWorking":2},
			"6":{"isWorking":2},
			"7":{"isWorking":2},
			"8":{"isWorking":2},
			"9":{"isWorking":2}},
			"2":{"20":{"isWorking":3},
			"23":{"isWorking":2}},
			"3":{"6":{"isWorking":3},
			"9":{"isWorking":2}},
			"4":{"30":{"isWorking":3}},
			"5":{"1":{"isWorking":2},
			"4":{"isWorking":2},
			"8":{"isWorking":3},
			"11":{"isWorking":2}},
			"6":{"11":{"isWorking":3},
			"12":{"isWorking":2}},
			"11":{"3":{"isWorking":3},
			"4":{"isWorking":2}},
			"12":{
			"31":{"isWorking":3}}}}}
	*/
	function workAndWeekendDaysInThisMonth() {
		//Общее количество дней в текущем месяце
		$daysInTheMonth = date('t');

		//Текущий день месяца
		$currentDayInThisMonth = date('j');

		//Текущий месяц
		$currentMonth = date('n');

		//Текуший год
		$currentYear = date('Y');

		//Выходные дни: суббота и воскресенье
		$weekEndDays = ['Sat', 'Sun'];

		//Счётчик для подсчёта рабочих дней в этом месяце
		$numberOfWorkDaysInCurrentMonth = 1;

		//Текущая дата в формате день.месяц.год (без ведущих нулей)
		$currentDayString = $currentDayInThisMonth.".".$currentMonth.".".$currentYear;

		//Таймштамп текущего дня
		$timestampOfCurrentDay = strtotime($currentDayString);

		//Пробегаем все дни текущего месяца в цикле
		for ($day=0; $day < $daysInTheMonth; $day++) {
			//Поскольку отчёт начинается с нуля, прибавляем ко дню единицу
			$dayPlusOne = $day+1;

			//Строковое представление даты
			$dateString = $dayPlusOne.".".$currentMonth.".".$currentYear;

			//Таймштапм 
			$timestamp = strtotime($dateString);

			//День недели в формате "Sat"
			$dayOfWeek = date('D', $timestamp);

			//Если это не выходной день, то добавляем к счётчику будних
			//дней единицу
			if (!in_array($dayOfWeek, $weekEndDays)) {
				$numberOfWorkDaysInCurrentMonth++;
			}

			//Если текущий день совпадает с днём в текущей итерации,
			//то вычисляем порядковый номер рабочего дня в этом месяце
			if ($timestamp === $timestampOfCurrentDay) {
				//Текущий рабочий день в этом месяце
				$currentWorkingDay = $numberOfWorkDaysInCurrentMonth-1;
			}
		}

		//Количество рабочих дней в месяце
		$numberOfWorkDaysInCurrentMonth = $numberOfWorkDaysInCurrentMonth-1;

		//Количество рабочих часов в месяце
		$numberOfWorkingHoursInThisMonth = $numberOfWorkDaysInCurrentMonth*8;

		return [
			$numberOfWorkDaysInCurrentMonth,
			$numberOfWorkingHoursInThisMonth,
			$currentWorkingDay
		];
	}

	}

	$initialize = new weekDaysInRussia;
?>