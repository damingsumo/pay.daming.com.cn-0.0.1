<?php
/**
 * 日期使用类
 * 不断完善中...
 * @author zq
 */
class usedate {
	
	/**
     * 获取上季度第一天
     *
     * @param $date
     * @return string
     * @author zy
     */
    static function getUpperQuarterFirstDay($date) {
        $month = date('n', strtotime($date));
		if(($month % 3) == 0) {
			$diffNum = 2;
		}else {
			$diffNum = ($month % 3) - 1;
		}
		$diffNum = $diffNum + 3;
        
        $quarterDate = date('Y-m-d', strtotime("$date -$diffNum month"));
        return self::getMonthFirstDay($quarterDate);
    }
	
	/**
     * 获取上季度最后一天
     *
     * @param $date
     * @return bool|string
     */
    static function getUpperQuarterLastDay($date) {
        $month = date('n', strtotime($date));
        $remainder = $month % 3;
        $diffNum = strval($remainder == 0 ? 0 : 3 - $remainder) - 3;
        $quarterDate = date('Y-m-d', strtotime("$date +$diffNum month"));
        return self::getMonthLastDay($quarterDate);
    }
	
    /**
     * 获取本季度第一天
     *
     * @param $date
     * @return string
     * @author zq
     */
    static function getQuarterFirstDay($date) {
        $month = date('n', strtotime($date));
		if(($month % 3) == 0) {
			$diffNum = 2;
		}else {
			$diffNum = ($month % 3) - 1;
		}
        
        $quarterDate = date('Y-m-d', strtotime("$date -$diffNum month"));
        return self::getMonthFirstDay($quarterDate);
    }
	
	/**
     * 获取季度最后一天
     *
     * @param $date
     * @return bool|string
     */
    static function getQuarterLastDay($date) {
        $month = date('n', strtotime($date));
        $remainder = $month % 3;
        $diffNum = strval($remainder == 0 ? 0 : 3 - $remainder);
        $quarterDate = date('Y-m-d', strtotime("$date +$diffNum month"));
        return self::getMonthLastDay($quarterDate);
    }
	
	/**
     * 获取下季度开始时间
     *
     * @param $date
     * @return bool|string
     */
    static function getNextQuarterFirstDay($date) {
        $month = date('n', strtotime($date));
        $remainder = $month % 3;
        $diffNum = strval($remainder == 0 ? 0 : 3 - $remainder);
        $quarterDate = date('Y-m-d', strtotime("$date +$diffNum month"));
        return self::getNextMonthFirstDay($quarterDate);
    }
    
    /**
     * 获取下季度结束一天
     *
     * @param $date
     * @return bool|string
     */
    static function getNextQuarterLastDay($date) {
        $month = date('n', strtotime($date));
        $remainder = $month % 3;
        $diffNum = strval($remainder == 0 ? 0 : 3 - $remainder) + 2;
        $quarterDate = date('Y-m-d', strtotime("$date +$diffNum month"));
        return self::getNextMonthLastDay($quarterDate);
    }
	
	/**
     * 获取上月度第一天
     *
     * @param $date Y-m-d
     * @return bool|string
     * @author zy
     */
    static function getUpperMonthFirstDay($date) {
        $firstDay = date('Y-m-01', strtotime($date));
        return date('Y-m-d', strtotime("$firstDay -1 month"));
    }
    
    /**
     * 获取上月度最后一天
     *
     * @param $date Y-m-d
     * @return bool|string
	 * @author zy
     */
    static function getUpperMonthLastDay($date) {
        $firstDay = date('Y-m-01', strtotime($date));
        return date('Y-m-d', strtotime("$firstDay -1 day"));
    }

    /**
     * 获取本月第一天
     *
     * @param $date
     * @return string
     * @author zq
     */
    static function getMonthFirstDay($date) {
        return date('Y-m-01', strtotime($date));
    }
	
	/**
     * 获取本月最后一天
     *
     * @param $date
     * @return bool|string
     */
    static function getMonthLastDay($date) {
        $firstDay = date('Y-m-01', strtotime($date));
        return date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
    }
	
	/**
     * 获取下月度第一天
     *
     * @param $date Y-m-d
     * @return bool|string
     * @author zq
     */
    static function getNextMonthFirstDay($date) {
        $firstDay = date('Y-m-01', strtotime($date));
        return date('Y-m-d', strtotime("$firstDay +1 month"));
    }
    
    /**
     * 获取下月度最后一天
     *
     * @param $date Y-m-d
     * @return bool|string
     */
    static function getNextMonthLastDay($date) {
        $firstDay = date('Y-m-01', strtotime($date));
        return date('Y-m-d', strtotime("$firstDay +2 month -1 day"));
    }
	
	/**
     * 获取上周开始时间
     * @param type $date
     * @return type
     */
    static function getUpperWeekFirstDay($date) {
        $week = date('N', strtotime($date));
        $diffNum = strval( 6 + $week );
        return date('Y-m-d', strtotime("$date - $diffNum day"));
    }
    
    /**
     * 获取上周结束时间
     * @param type $date Y-m-d
     * @return type
     */
    static function getUpperWeekLastDay($date) {
        $week = date('N', strtotime($date));
        $diffNum = strval($week);
        return date('Y-m-d', strtotime("$date - $diffNum day"));
    }

    /**
     * 获取本周第一天
     *
     * @param $date
     * @return string
     * @author zq
     */
    static function getWeekFirstDay($date) {
        $diffNum = strval(date('N', strtotime($date)) - 1);
        return date('Y-m-d', strtotime("$date -$diffNum day"));
    }
	
    /**
     * 获取周最后一天
     *
     * @param $date
     * @return bool|string
     */
    static function getWeekLastDay($date) {
        $week = date('N', strtotime($date));
        $diffNum = strval( 7 - $week );
        return date('Y-m-d', strtotime("$date +$diffNum day"));
    }
    
    /**
     * 获取下周开始时间
     * @param type $date
     * @return type
     */
    static function getNextWeekFirstDay($date) {
        $week = date('N', strtotime($date));
        $diffNum = strval( 8 - $week );
        return date('Y-m-d', strtotime("$date +$diffNum day"));
    }
    
    /**
     * 获取下周结束时间
     * @param type $date Y-m-d
     * @return type
     */
    static function getNextWeekLastDay($date) {
        $week = date('N', strtotime($date));
        $diffNum = strval( 14 - $week );
        return date('Y-m-d', strtotime("$date +$diffNum day"));
    }
    
    /**
     * 获取上年开始时间
     * @param type $date Y-m-d
     * @return type
     */
    static function getUpperYearFirstDay($date) {
        $currentYearFirstDay = self::getYearFirstDay($date);
        return date('Y-m-d', strtotime("$currentYearFirstDay - 1 year"));
    }
    
    /**
     * 获取上年结束时间
     * @param type $date Y-m-d
     * @return type Y-m-d
     */
    static function getUpperYearLastDay($date) {
        $currentYearFirstDay = self::getYearFirstDay($date);
        return date('Y-m-d', strtotime("$currentYearFirstDay - 1 day"));;
    }
    
	/**
     * 获取本年开始时间
     * @param type $date Y-m-d
     * @return type
     */
    static function getYearFirstDay($date) {
        return date('Y-01-01', strtotime($date));
    }
	
    /**
     * 获取本年结束时间
     * @param type $date Y-m-d
     * @return type
     */
    static function getYearLastDay($date) {
        return date('Y-12-31', strtotime($date));
    }

    /**
     * 获取下年开始时间
     * @param type $date Y-m-d
     * @return type
     */
    static function getNextYearFirstDay($date) {
        $nextYearDay = self::getYearLastDay($date);
        return date('Y-m-d', strtotime("$nextYearDay + 1 day"));
    }
    
    /**
     * 获取下年结束时间
     * @param type $date Y-m-d
     * @return type Y-m-d
     */
    static function getNextYearLastDay($date) {
        $thisYearLastDay = self::getYearLastDay($date);
        return date('Y-m-d', strtotime("$thisYearLastDay + 1 year"));;
    }

}
?>
