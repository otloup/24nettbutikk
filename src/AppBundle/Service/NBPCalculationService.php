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
        if ($from > $to) {
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

    /**
     * get lowest price in given period
     *
     * @param array $period
     * @return mixed
     */
    public function getMinValueFromPeriod(array $period)
    {
        return $this->getExtreme($period, 'min');
    }

    /**
     * get highest price in given period
     *
     * @param array $period
     * @return mixed
     */
    public function getMaxValueFromPeriod(array $period)
    {
        return $this->getExtreme($period, 'max');
    }

    /**
     * count maximal yield of investment based on purchasing power and stock price
     *
     * @param float $min
     * @param float $max
     * @param float $investment
     * @return float
     */
    public function getProfit($min, $max, $investment)
    {
        /*
         * count the maximum number of available purchases
         * multiply it by the highest fount price
         */
        $availableStocks = $investment / $min;
        $profit = $availableStocks * $max;

        /*
         * since number_format rounds everything after requested decimal length, overshoot point placement
         * convert format to string and retrieve un-rounded decimals
         */
        $profitFormat = explode('.', (string)number_format($profit, 100, '.', ''));
        return floatval($profitFormat[0] . '.' . substr($profitFormat[1], 0, 2));
    }

    /**
     * get extreme value out of haystack
     *
     * @param $haystack
     * @param string $mode
     * @return mixed
     */
    private function getExtreme($haystack, $mode='min')
    {
        /*
         * in case of further development allow for other methods for checking extremes
         */
        $isExtreme = function($value, $comparison) use ($mode){
            switch ($mode) {
                case 'min':
                    return ($value < $comparison);
                    break;

                case 'max':
                    return ($value > $comparison);
                    break;

                default:
                    return false;
            }
        };

        /*
         * assume that first value is extreme
         * if other value validates the check, mark it as extreme and check the rest against it
         */
        $extreme = $haystack[0]['cena'];
        foreach ($haystack as $price) {
            if ($isExtreme($price['cena'], $extreme)) {
                $extreme = $price['cena'];
            }
        }

        return $extreme;
    }
}