<?php
/**
 * Created by IntelliJ IDEA.
 * User: loup
 * Date: 03.12.17
 * Time: 06:37
 */

namespace AppBundle\Service;


use GuzzleHttp\ClientInterface;

class NBPDataRetrievalService
{
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var NBPCalculationService
     */
    private $NBPCalculationService;

    public function __construct(ClientInterface $client, NBPCalculationService $NBPCalculationService)
    {
        $this->client = $client;
        $this->NBPCalculationService = $NBPCalculationService;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function getDataForTimespan(\DateTime $from, \DateTime $to)
    {
        $periods = $this->NBPCalculationService->getDatePeriods($from, $to);
        return $this->getDataForPeriods($periods);
    }

    /**
     * @param array $periods
     * @return int
     */
    private function getDataForPeriods(array $periods)
    {
        return 1;
    }
}