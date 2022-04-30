<?php

  class calendarM
  {

    function getDates(): DatePeriod
    {
      $start = new DateTime('thursday');
      $end = clone $start;
      $end->modify('+90 days');
      $interval = new DateInterval('P7D');

      $period = new DatePeriod($start, $interval, $end);

      return $period;
    }

  }

?>