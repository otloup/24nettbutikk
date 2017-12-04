<?php
/**
 * Created by IntelliJ IDEA.
 * User: loup
 * Date: 03.12.17
 * Time: 06:37
 */

namespace AppBundle\Service;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;

class NBPDataRetrievalService
{
    const NBP_API_TEMPLATE = 'http://api.nbp.pl/api/cenyzlota/%s/%s?format=json';

    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var NBPCalculationService
     */
    private $NBPCalculationService;

    public function __construct(NBPCalculationService $NBPCalculationService)
    {
        $this->client = new Client();
        $this->NBPCalculationService = $NBPCalculationService;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     * @throws \Exception
     */
    public function getDataForTimespan(\DateTime $from, \DateTime $to)
    {
        $periods = $this->NBPCalculationService->getDatePeriods($from, $to);
        return $this->getDataForPeriods($periods);
    }

    /**
     * @param array $periods
     * @return array
     * @throws \Exception
     */
    private function getDataForPeriods(array $periods)
    {
        $minPrice = PHP_INT_MAX;
        $maxPrice = 0;

        $minPriceDay = null;
        $maxPriceDay = null;

        foreach ($periods as $period) {
            $response = $this->client->get(
                new Uri(sprintf(self::NBP_API_TEMPLATE, $period['from'], $period['to']))
            );

            if ($response->getStatusCode() === 200) {
                echo "requesting data for " . $period['from'] . " to " . $period['to'] . "\n";

                $retrievedData = json_decode($response->getBody()->getContents(), true);

                $minPeriodValue = $this->NBPCalculationService->getMinValueFromPeriod($retrievedData);
                $maxPeriodValue = $this->NBPCalculationService->getMaxValueFromPeriod($retrievedData);

                if ($minPeriodValue['price'] < $minPrice) {
                    $minPrice = $minPeriodValue['price'];
                    $minPriceDay = $minPeriodValue['date'];
                }

                if ($maxPeriodValue['price'] > $maxPrice) {
                    $maxPrice = $maxPeriodValue['price'];
                    $maxPriceDay = $maxPeriodValue['date'];
                }
            } else {
                throw new \Exception('NBP API has returned response with code' . $response->getStatusCode());
            }
        }

        return [
            'min' => [
                'price' => $minPrice,
                'date' => $minPriceDay
            ],
            'max' => [
                'price' => $maxPrice,
                'date' => $maxPriceDay
            ]
        ];
    }
}