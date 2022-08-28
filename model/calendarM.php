<?php

  class calendarM
  {

    function getDates(int $numDays): DatePeriod
    {
      $start = new DateTime('thursday');
      $end = clone $start;
      $end->modify('+'.$numDays.' days');
      $interval = new DateInterval('P7D');

      $period = new DatePeriod($start, $interval, $end);

      return $period;
    }

  }

?>