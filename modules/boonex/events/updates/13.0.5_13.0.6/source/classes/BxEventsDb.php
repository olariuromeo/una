<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Events Events
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Groups module database queries
 */
class BxEventsDb extends BxBaseModGroupsDb
{
    public function __construct(&$oConfig)
    {
        parent::__construct($oConfig);
    }

    public function getContentInfoByIntervalId ($iIntervalId)
    {
        $iContentId = $this->getOne("SELECT `event_id` FROM `bx_events_intervals` WHERE `interval_id` = :interval", array(
            'interval' => $iIntervalId,
        ));
        if (!$iContentId)
            return 0;
        return $this->getContentInfoById ($iContentId);
    }

    public function getContentInfoById ($iContentId)
    {
        $sQuery = $this->prepare ("SELECT `c`.*, `p`.`account_id`, `p`.`id` AS `profile_id`, `p`.`status` AS `profile_status`, MAX(`i`.`repeat_stop`) AS `repeat_stop` FROM `" . $this->_oConfig->CNF['TABLE_ENTRIES'] . "` AS `c` 
            INNER JOIN `sys_profiles` AS `p` ON (`p`.`content_id` = `c`.`id` AND `p`.`type` = ?) 
            LEFT JOIN `bx_events_intervals` as `i` ON (`c`.`id` = `i`.`event_id`)
            WHERE `c`.`id` = ?
            GROUP BY `c`.`id`", $this->_oConfig->getName(), $iContentId);
        return $this->getRow($sQuery);
    }

    public function deleteIntervalById ($iIntervalId)
    {
        return $this->query("DELETE FROM `bx_events_intervals` WHERE `interval_id` = :interval", array(
            'interval' => $iIntervalId,
        ));
    }
    
    public function getIntervals ($iContentId) 
    {
        return $this->getAllWithKey("SELECT * FROM `bx_events_intervals` WHERE `event_id` = :event_id", 'interval_id', array(
            'event_id' => $iContentId,
        ));
    }

    public function getEntriesByDate($sDateFrom, $sDateTo, $iEventId = 0, $aSQLPart = array())
    {
        // validate input data
        if (false === ($oDateFrom = date_create($sDateFrom, new DateTimeZone('UTC'))))
            return array();
        if (false === ($oDateTo = date_create($sDateTo, new DateTimeZone('UTC'))))
            return array();
        if ($oDateFrom > $oDateTo)
            return array();

        $sModule = $this->_oConfig->getName();

        // increase start and end date to cover timezones
        $oDateFrom = $oDateFrom->sub(new DateInterval("P1D"));
        $oDateTo = $oDateTo->add(new DateInterval("P1D"));

        // look throught all days in the interval
        $oDateIter = clone($oDateFrom);
        $aEntries = array();
        while ($oDateIter->format('Y-m-d') != $oDateTo->format('Y-m-d')) {

            $oDateMin = date_create($oDateIter->format('Y-m-d') . '00:00:00', new DateTimeZone('UTC'));
            $oDateMax = date_create($oDateIter->format('Y-m-d') . '23:59:59', new DateTimeZone('UTC'));
                
            // get all events for the specific day            
            $oDateMonthBegin = date_create($oDateIter->format('Y-m-01'), new DateTimeZone('UTC'));
            $iWeekOfMonth = $oDateIter->format('W') - $oDateMonthBegin->format('W') + 1;
            $aBindings = array(
                'timestamp_min' => $oDateMin->getTimestamp(),
                'timestamp_max' => $oDateMax->getTimestamp(),
            );
            $aBindingsRepeating = array(
                'timestamp_min' => $oDateMin->getTimestamp(),
                'timestamp_max' => $oDateMax->getTimestamp(),
                'year_start' => $oDateFrom->format('Y'),
                'year' => $oDateIter->format('Y'),
                'month' => $oDateIter->format('n'),
                'week_of_month' => $iWeekOfMonth > 0 ? $iWeekOfMonth : $oDateIter->format('W') + 1,
                'day_of_month' => $oDateIter->format('j'),
                'day_of_week' => $oDateIter->format('N'),
            );
            $sJoin = isset($aSQLPart['join']) ? $aSQLPart['join'] : '';
            $sWhere = isset($aSQLPart['where']) ? $aSQLPart['where'] : '';
            if ((int)$iEventId) {
                $aBindings['event'] = (int)$iEventId;
                $aBindingsRepeating['event'] = (int)$iEventId;
                $sWhere .= " AND `bx_events_data`.`id` = :event ";
            }

            // search for regular events
            $a = $this->getAllWithKey("SELECT DISTINCT `bx_events_data`.`id`, `bx_events_data`.`event_name` AS `title`, `bx_events_data`.`date_start`, `bx_events_data`.`date_end`, `bx_events_data`.`timezone`, `bx_events_data`.`reminder`, 0 AS `repeating`
                FROM `bx_events_data` $sJoin 
                WHERE `bx_events_data`.`date_start` >= :timestamp_min AND `bx_events_data`.`date_start` <= :timestamp_max $sWhere
            ", 'id', $aBindings);

            if ($a)
                $sWhere .= " AND `bx_events_data`.`id` NOT IN(" . $this->implode_escape(array_keys($a)) . ") ";

            $aRepeating = $this->getAllWithKey("SELECT DISTINCT `bx_events_data`.`id`, `bx_events_data`.`event_name` AS `title`, `bx_events_data`.`date_start`, `bx_events_data`.`date_end`, `bx_events_data`.`timezone`, `bx_events_data`.`reminder`, 1 AS `repeating`
                FROM `bx_events_data`
                LEFT JOIN `bx_events_intervals` AS `i` ON (
                    `bx_events_data`.`id` = `i`.`event_id`
                    AND
                    `bx_events_data`.`date_start` < :timestamp_max
                    AND
                    (0 = `i`.`repeat_stop` OR `i`.`repeat_stop` >= :timestamp_min)
                    AND (
                        (0 = `i`.`repeat_year` OR 0 = (:year - :year_start) % `i`.`repeat_year`)
                        AND 
                        (0 = `i`.`repeat_month` OR :month = `i`.`repeat_month`)
                        AND 
                        (0 = `i`.`repeat_week_of_month` OR :week_of_month = `i`.`repeat_week_of_month`)
                        AND 
                        (0 = `i`.`repeat_day_of_month` OR :day_of_month = `i`.`repeat_day_of_month`)
                        AND 
                        (0 = `i`.`repeat_day_of_week` OR :day_of_week = `i`.`repeat_day_of_week`)
                    )
                ) $sJoin 
                WHERE `i`.`interval_id` IS NOT NULL $sWhere
            ", 'id', $aBindingsRepeating);

            $a = array_merge($aRepeating ? $aRepeating : array(), $a ? $a : array());

            // prepare variables for each event            
            foreach ($a as $k => $r) {
                $oDateStart = new DateTime('@' . $r['date_start']);
                $oDateEnd = new DateTime('@' . $r['date_end']);
                $oDuration = $oDateStart->diff($oDateEnd);

                if ($r['repeating']) {
                    // since repeating interval values are stored in specific timezones then we need to convert it
                    $oTz = new DateTimeZone($r['timezone'] ? $r['timezone'] : 'UTC');
                    $sHoursStart = $oDateStart->format('H:i:s');
                    $iSecondsStart = (clone($oDateStart))->setTimezone($oTz)->format('H') * 3600 + $oDateStart->format('i') * 60 +  $oDateStart->format('s');
                    $iSecondsOffset = $oTz->getOffset($oDateIter);

                    $sCurrentDay = $oDateIter->format('Y-m-d');
                    if ($iSecondsOffset > 0 && $iSecondsOffset > $iSecondsStart)
                        $sCurrentDay = (clone($oDateIter))->sub(new DateInterval("P1D"))->format('Y-m-d');
                    if ($iSecondsOffset < 0 && -$iSecondsOffset > (86400 - $iSecondsStart))
                        $sCurrentDay = (clone($oDateIter))->add(new DateInterval("P1D"))->format('Y-m-d');

                    $oStart = date_create($sCurrentDay . ' ' . $sHoursStart);
                    $oEnd = $oStart ? clone($oStart) : null;
                    $oEnd = $oEnd ? $oEnd->add($oDuration) : null;
                } 
                else {
                    $oStart = $oDateStart;
                    $oEnd = $oDateEnd;
                }

                $a[$k]['start'] = $oStart ? $oStart->format('c') : 0;
                $a[$k]['end'] = $oEnd ? $oEnd->format('c') : 0;
                $a[$k]['start_utc'] = $oDateStart ? $oDateStart->getTimestamp() : 0;
                $a[$k]['end_utc'] = $oDateEnd ? $oDateEnd->getTimestamp() : 0;
                $a[$k]['url'] = bx_absolute_url(BxDolPermalinks::getInstance()->permalink('page.php?i=' . $this->_oConfig->CNF['URI_VIEW_ENTRY'] . '&id=' . $r['id']));
                if(($oEvent = BxDolProfile::getInstanceByContentAndType($r['id'], $sModule)) !== false)
                    $a[$k]['extendedProps'] = [
                        'class' => 'bx-events-calendar-unit',
                        'card' => $oEvent->getUnit()
                    ];
            }

            // merge with all other events
            $aEntries = array_merge($aEntries, $a);

            // go to the next day
            $oDateIter = $oDateIter->add(new DateInterval("P1D"));
        }

        return $aEntries;
    }
}

/** @} */
