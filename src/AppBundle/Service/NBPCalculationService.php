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
    const NBP_DAYS_LIMIT = 367;
    const NBP_TIME_FORMAT = 'Y-d-m';

    /**
     * chop time frame between two dates into periods consisting of max number of days
     * allowed by the NBP API request
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function getDatePeriods(\DateTime $from, \DateTime $to)
    {
        $dayDiff = ($to->diff($from))->days;
        $periods = [];

        /*
         * if the difference between start and end date is less than max number of days
         * set one period to be created
         *
         * otherwise, create as many periods as the fixed number of days
         * fits between the two dates
         */
        if ($dayDiff < self::NBP_DAYS_LIMIT) {
            $periodsCount = 1;
        } else {
            $periodsCount = ceil($dayDiff / self::NBP_DAYS_LIMIT);
        }

        for ($quantifier = 0; $quantifier < $periodsCount; $quantifier ++) {
            /*
             * count days by which start date is to be incremented
             */
            $daysIncrement = self::NBP_DAYS_LIMIT * $quantifier;

            $fromDate = $from->modify('+'.$daysIncrement.' days');

            /*
             * if the script is on it's last period iteration, return end date
             * otherwise, add days to the start date
             */
            if ($quantifier === $periodsCount - 1) {
                $toDate = $to;
            } else {
                $toDate = self::NBP_DAYS_LIMIT * ($quantifier - 1);
            }

            $periods[$quantifier] = [
                'from' => $fromDate->format(self::NBP_TIME_FORMAT),
                'to' => $toDate->format(self::NBP_TIME_FORMAT)
            ];
        }

        return $periods;
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