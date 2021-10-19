<?php 
require_once(dirname(__FILE__)."/../component.php");

class Event
{
	public static function drawEventList($eventDate, $managerID)
	{
		if (!$periods = self::prepareData($eventDate, $managerID)) 
			return '<center><h4>Список пуст</h4></center>';

		return self::renderHtml($periods);
	}

	public static function isIntersectedEvent($dateFromStr, $dateToStr, $managerId)
	{
		$stmt     = GetStatement();
		$dateFrom = date('Y-m-d H:i:s', $dateFromStr);
		$dateTo   = date('Y-m-d H:i:s', $dateToStr);

		$today = date('Y-m-d', $dateFromStr);
		$query = "SELECT * 
			FROM crm_event 
			WHERE ManagerID = " . $managerId . " AND
			EventDateFrom >= '" . $today . ' 00:00:00' . "' AND EventDateTo <= '" . $today . ' 23:59:59' . "' ORDER BY EventDateFrom";

		$events = $stmt->fetchList($query);

		foreach ($events as $event) {
			if (($dateFrom < $event['EventDateTo']) && ($dateTo > $event['EventDateFrom'])) {
				return true;
			}
		}

		return false;
	}

	private static function prepareData($eventDate, $managerID)
	{
		$stmt      = GetStatement();
		$startDate = $eventDate . ' 00:00:00';
		$endDate   = $eventDate . ' 23:59:59';

		$query = "SELECT * 
					FROM crm_event 
					WHERE ManagerID = " . Connection::GetSQLString($managerID) . " AND
				EventDateFrom >= '" . $startDate . "' AND EventDateTo <= '" . $endDate . "' ORDER BY EventDateFrom";

		$events = $stmt->fetchList($query);

		if (empty($events)) 
			return false;

		$periods = self::getPeriods();
		$isEmpty = true;

		foreach ($periods as $time => $row) {

			foreach ($events as $event) {

				$comparedDatetime = strtotime($eventDate . ' ' .$time);
				$comparedFrom     = strtotime($event['EventDateFrom']);
				$comparedTo       = strtotime($event['EventDateTo']);

				if ($comparedFrom >= $comparedTo) continue;

				if ($comparedDatetime >= $comparedFrom && $comparedDatetime < $comparedTo) {
					$periods[$time][] = $event;
					$isEmpty = false;
				}
			}
		}

		if ($isEmpty) return false;

		return $periods;
	}

	private static function renderHtml($periods)
	{
		$eventListHtml = '<table class="table table-condensed event-list">';
		$nicePeriods = self::getPeriods();
		$logFields = array();

		foreach ($periods as $time => $periodData) 
		{
			foreach ($periodData as $event) 
			{
				if (!isset($logFields[$event['EventID']])) 
				{
					if (count($logFields) == 0 || !$maxValue = max(array_values($logFields)))
						$maxValue = 0;

					$logFields[$event['EventID']] = $maxValue + 1;
				}
			}
		}
		
		foreach ($periods as $time => $periodData) {

			foreach ($logFields as $eventId => $value) {
				$nicePeriods[$time][$value] = null;
			}

			foreach ($periodData as $event) {

				foreach ($logFields as $eventId => $value) {
					if ($eventId == $event['EventID']) {
						$nicePeriods[$time][$value] = $event;
					}
				}
			}
		}

		foreach ($nicePeriods as $time => $periodData) {

			$prevTime = self::getPreviousPeriod($time);
			$nextTime = self::getNextPeriod($time);

			$timeRow = strpos($time, '30') ? '<br>' : $time;
			$eventListHtml .= '<tr>';
			$eventListHtml .= '<td style="width:50px;"><center><b>' . $timeRow . '</b></center></td>';

			foreach ($periodData as $logKey => $event) {

				$eventType = ($event['EventType'] == 'private') ? 'class="danger my-event-edit event-td"' : 'class="success my-event-edit event-td"';

				if (!is_null($event)) {

					//На первой строчке задачи пишем название задачи
					if ($prevTime == NULL || $nicePeriods[$prevTime][$logKey]['EventID'] !== $event['EventID']) {

						if ($event['EventType'] == 'private') {
							$icon = '<i class="fa fa-user"></i>';
						} else {
							$icon = '<i class="fa fa-users"></i>';
						}

						if (strtotime($event['EventDateTo']) < strtotime(date('Y-m-d H:i:s'))) {
							$icon = '<i class="fa fa-check"></i>';
						}

						$timeStart = date('H:i', strtotime($event['EventDateFrom']));
						$timeEnd = date('H:i', strtotime($event['EventDateTo']));


						$eventListHtml .= '<td '.$eventType.' EntityID="'.$event['EventID'].'">';
						$eventListHtml .= '<span class="pull-left">' . $timeStart. '-' . $timeEnd . ', ' . $event['Title'] . '</span>';
						$eventListHtml .= '</span><span class="pull-right">' . $icon . '</span>';
						$eventListHtml .= '</td>';

					} else {
						$eventListHtml .= '<td '.$eventType.' EntityID="'.$event['EventID'].'"></td>';
					}
				}
				// else {
				// 	$eventListHtml .= '<td></td>';
				// }
			}

			$eventListHtml .= '</tr>';
		}

		$eventListHtml .= '</table>';

		return $eventListHtml;
	}

	private static function getPreviousPeriod($time)
	{
		$periods = self::getPeriods();
		while(key($periods) !== $time) next($periods);
		$prevVal = prev($periods);
		$prevTime = key($periods);

		return $prevTime;
	}

	private static function getNextPeriod($time)
	{
		$periods = self::getPeriods();
		while(key($periods) !== $time) next($periods);
		$nextVal = next($periods);
		$nextTime = key($periods);

		return $nextTime;
	}

	private static function getPeriods()
	{
		$periods['09:00'] = array();
		$periods['09:30'] = array();
		$periods['10:00'] = array();
		$periods['10:30'] = array();
		$periods['11:00'] = array();
		$periods['11:30'] = array();
		$periods['12:00'] = array();
		$periods['12:30'] = array();
		$periods['13:00'] = array();
		$periods['13:30'] = array();
		$periods['14:00'] = array();
		$periods['14:30'] = array();
		$periods['15:00'] = array();
		$periods['15:30'] = array();
		$periods['16:00'] = array();
		$periods['16:30'] = array();
		$periods['17:00'] = array();
		$periods['17:30'] = array();
		$periods['18:00'] = array();
		$periods['18:30'] = array();
		$periods['19:00'] = array();
		$periods['19:30'] = array();
		$periods['20:00'] = array();
		$periods['20:30'] = array();
		$periods['21:00'] = array();

		return $periods;
	}
}