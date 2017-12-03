<?php
/**
 * Created by IntelliJ IDEA.
 * User: loup
 * Date: 03.12.17
 * Time: 05:57
 */

namespace AppBundle\Service;

class NBPCalculationService
{
    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function getDatePeriods(\DateTime $from, \DateTime $to)
    {
        return [];
    }

    public function getMinValueFromPeriod()
    {
        return 1;
    }

    public function getMaxValueFromPeriod()
    {
        return 1;
    }

    public function getProfit($min, $max, $investment)
    {
        return 1;
    }
}