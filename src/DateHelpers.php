<?php
namespace PsgcLaravelPackages\Utils;

class DateHelpers
{
    public static function renderPeriod($periodYear,$periodMonth)
    {
        $html = '';
        if ( empty($periodYear) && empty($periodMonth) ) {
            $html = 'N/A';
        } else {
            $periodMonth = sprintf('%02d', $periodMonth);
            $html = $periodYear.'/'.$periodMonth;
        }
        return $html;
    }

    // http://stackoverflow.com/questions/3319386/php-get-last-week-number-in-year
    // http://stackoverflow.com/questions/3319386/php-get-last-week-number-in-year
    public static function getIsoWeeksInYear($year=null) 
    {
        $year = empty($year) ? date('Y') : $year;
        $date = new \DateTime;
        $date->setISODate($year, 53);
        return ($date->format('W') === '53' ? 53 : 52);
    }

    // returns dates in mysql format yyyy-mm-dd
    public static function getDateRangeByWeekNumber($weekNum, $year)
    {
        $range = ['start'=>null,'end'=>null];

        $dt = new \DateTime();
        $dt->setISODate($year, $weekNum-1,0);
        $range['start'] = $dt->format('Y-m-d');
        $dt->modify('+6 days');
        $range['end'] = $dt->format('Y-m-d');

        return $range;
    }

    // Checks if a mysql formatted date (yyyy-mm-dd) is within a given
    //   range, endpoints inclusive
    public static function isDateInRange($targetDate, $start, $end)
    {
        $startTS = strtotime($start);
        $endTS = strtotime($end);
        $targetTS = strtotime($targetDate);
    
        $is = (($targetTS >= $startTS) && ($targetTS <= $endTS));
        return $is;
    }
        

    public static function getWeekNumberOptions($year=null,$addCurrent=0)
    {
        $year = empty($year) ? date('Y') : $year;
        $numWeeks = self::getIsoWeeksInYear($year);
        $options = [];
        $options[''] = '';
        if ($addCurrent) {
            $options['current'] = 'Current Week';
        }
        for ($i = 1 ; $i <= $numWeeks ; ++$i) {
            $options[$i] = $i;
        }
        return $options;
    }

    public static function renderMonth($monthNumber) {
        return date('F', mktime(0, 0, 0, $monthNumber, 10));
    }

    public static function getMonthOptions($emptyElement = true)
    {
        $options = [];
        if($emptyElement) {
            $options[0] = '';
        }
        $options[1] = 'Jan';
        $options[2] = 'Feb';
        $options[3] = 'Mar';
        $options[4] = 'Apr';
        $options[5] = 'May';
        $options[6] = 'Jun';
        $options[7] = 'Jul';
        $options[8] = 'Aug';
        $options[9] = 'Sep';
        $options[10] = 'Oct';
        $options[11] = 'Nov';
        $options[12] = 'Dec';
        return $options;
    }

    public static function getFullMonthOptions($emptyElement = true) {
        $options = [];
        if($emptyElement) {
            $options[0] = '';
        }
        $options[1] = 'January';
        $options[2] = 'Febuary';
        $options[3] = 'March';
        $options[4] = 'April';
        $options[5] = 'May';
        $options[6] = 'June';
        $options[7] = 'July';
        $options[8] = 'August';
        $options[9] = 'September';
        $options[10] = 'October';
        $options[11] = 'November';
        $options[12] = 'December';
        return $options;
    }

    public static function getQuarterOptions($start = null, $emptyElement = true)
    {
        $options = [];
        if($emptyElement) {
            $options[] = '';
        }
        if($start){
            $thisYear = date("Y");
            $thisQtr = (int) (date("n") / 4) + 1;
            for ($o = $thisYear; $o > $start; --$o, $thisQtr = 4) {
                for ($q = $thisQtr; $q > 0; --$q) {
                    $options[] = 'Q'.$q.'-'.$o;
                }
            }
        } else {
            for ($q = 1; $q <= 4; $q++) {
                $options['Q'.$q] = 'Q'.$q;
            }
        }
        return $options;
    }

    public static function getQuarterMonths($quarter){
        switch($quarter){
            case 1;
            case 'Q1':
                return [1,2,3];
            case 2:
            case 'Q2':
                return [4,5,6];
            case 3:
            case 'Q3':
                return [7,8,9];
            case 4:
            case 'Q4':
                return [10,11,12];
        }
    }



    public static function getThisYear($isInt=0)
    {
        $year = date("Y");
        return $isInt ? intval($year) : $year;
    }

    public static function getThisMonth($isInt=0)
    {
        $month = date("n");
        return $isInt ? intval($month) : $month;
    }

    public static function getThisQuarter($isInt=0)
    {
        $month = date("n");
        $quarter = intval($month/12) + 1;
        return $isInt ? intval($quarter) : $quarter;
    }

    public static function getYearOptions($start = 2010, $emptyElement = true)
    {
        $thisYear = date("Y");
        $options = [];
        if($emptyElement) {
            $options[] = '';
        }
        for ($o = $thisYear ; $o > $start; --$o) {
            $options[$o] = $o;
        }
        return $options;
    }

    public static function renderMysqlDate($dateIn,$format=null)
    {

        // yyyy-mm-dd
        if ( preg_match("%^(\d{4})-(\d{2})-(\d{2})%", $dateIn, $results) ) {
            switch ($format) {
                default: // mm/dd/yyyy
                    $dateStr = $results[2].'/'.$results[3].'/'.$results[1];
            }
        } else {
            $dateStr = $dateIn;
        }
        return $dateStr;
    }

    public static function toMySqlDate($dateIn)
    {
        if ( empty($dateIn) ) {
            return null;
        }

        if ( preg_match("%^(\d{4})-(0?[1-9]|1[012])-(\d{1,2})%", $dateIn, $results) ) {
            // YYYY-MM-DD
            $results[2] = str_pad($results[2], 2, 0, STR_PAD_LEFT); // month: pad w/ zeros if ness.
            $results[3] = str_pad($results[3], 2, 0, STR_PAD_LEFT); // day: pad w/ zeros if ness.

            //  yyyy-mm-dd to yyyy-mm-dd
            $dateOut = $results[1].'-'.$results[2].'-'.$results[3];

        } else if ( preg_match("%^(0?[1-9]|1[012])/(\d{1,2})/(\d{2,4})%", $dateIn, $results) ) {
            // MM/DD/YY or MM/DD/YYYY

            $results[1] = str_pad($results[1], 2, 0, STR_PAD_LEFT); // month: pad w/ zeros if ness.
            $results[2] = str_pad($results[2], 2, 0, STR_PAD_LEFT); // day: pad w/ zeros if ness.
            if ($results[3] < 100) {
                // Date is in short format (last 2 digits of year only)
                $dt = \DateTime::createFromFormat('y', $results[3]);
                $results[3] =  $dt->format('Y'); // output: 2013
            }
            
            //  mm/dd/yyyy to yyyy-mm-dd
            $dateOut = $results[3].'-'.$results[1].'-'.$results[2];
        } else {
            throw new \Exception('Invalid date format for date '.$dateIn.', must be MM/DD/YYYY or MM/DD/YY');
        }
        return $dateOut;
    }
    
    public static function toWeekPeriodSlug($dateIn){
        if ( empty($dateIn) ) {
            return null;
        }
        $dt = new \DateTime($dateIn);
        return $dt->format('Y') . '-' . (integer) $dt->format('W'); //integer cast removes leading zeros
    }
    
    // input dates must be in format yyyy-mm-dd
    // date1 > date2 => 1
    // date1 = date2 => 0
    // date1 < date2 => -1
    public static function compare($date1,$date2)
    {
        if ( 
               !preg_match("%^(\d{4})-(\d{2})-(\d{2})%", $date1, $results1) 
            || !preg_match("%^(\d{4})-(\d{2})-(\d{2})%", $date2, $results2) 
        ) 
        {
            throw new \Exception("Date in incorrect format, must be yyyy-mm-dd: $date1, $date2");
        }
        str_replace("-", "", $date1);
        str_replace("-", "", $date2);

        if ( $date1 > $date2 ) {
            return 1;
        } else if ($date1 < $date2 ) {
            return -1;
        } else {
            return 0;
        }
    } // compare

    public static function getYear($date)
    {
        return date("Y", strtotime($date));
    }

    public static function getMonth($date)
    {
        return date("n", strtotime($date));
    }

    public static function getQuarter($date)
    {
        $month = date("n", strtotime($date));
        $quarter = intval($month/12) +1;
        return $quarter;
    }


}
