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
     * @throws \Exception
     */
    public function getDatePeriods(\DateTime $from, \DateTime $to)
    {
        /*
         * validate if dates are properly oriented
         */
        if ($to < $from) {
            throw new \Exception('date to cannot be greater than date from');
        }

        /*
         * all periods have the same humble beginning - from is always defined
         */
        $periods = [];
        $periods[] = [
            'from' => $from->format(self::NBP_TIME_FORMAT),
            'to' => null
        ];

        /*
         * count difference between days
         * if difference is greater than the allowed number of days in one search period
         * return multiple periods
         *
         * otherwise, create only one period
         */
        $daysDiff = $from->diff($to)->days;

        if ($daysDiff / self::NBP_DAYS_LIMIT > 1) {
            $interval = new \DateInterval('P' . self::NBP_DAYS_LIMIT . 'D');
            $period = new \DatePeriod($from, $interval, $to);

            /**
             * iterate through generated periods.
             * Append 'to' date to the previous one and generate next one with empty 'to' date
             * @var integer $index
             * @var \DateTime $date
             */
            foreach ($period as $index => $date) {
                $current = $date->format(self::NBP_TIME_FORMAT);
                $previous = $index === 0 ? 0 : $index - 1;

                $periods[$previous]['to'] = $current;
                $periods[$index] = [
                    'from' => $current,
                    'to' => null
                ];
            }
        }

        /*
         * append 'to' date for the last period, since it always will be defined
         */
        $periods[count($periods) - 1]['to'] = $to->format(self::NBP_TIME_FORMAT);

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