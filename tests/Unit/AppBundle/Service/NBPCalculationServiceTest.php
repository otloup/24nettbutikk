<?php
/**
 * Created by IntelliJ IDEA.
 * User: loup
 * Date: 03.12.17
 * Time: 06:46
 */

namespace Tests\AppBundle\Service;

use AppBundle\Service\NBPCalculationService;
use Tests\ContainerAwareTestCase;

class NBPCalculationServiceTest extends ContainerAwareTestCase
{

    /**
     * @var NBPCalculationService
     */
    private $NBPCalculationService;

    public function setUp()
    {
        $this->NBPCalculationService = $this->container->get(NBPCalculationService::class);
    }

    public function testGetDatePeriods()
    {
        $periods = $this->NBPCalculationService->getDatePeriods(new \DateTime(), new \DateTime());

        $this->assertEquals([], $periods);
    }

    public function testGetMinValueFromPeriod()
    {
        $minValue = $this->NBPCalculationService->getMinValueFromPeriod();

        $this->assertEquals(1, $minValue);
    }

    public function testGetMaxValueFromPeriod()
    {
        $maxValue = $this->NBPCalculationService->getMaxValueFromPeriod();

        $this->assertEquals(1, $maxValue);
    }

    public function testGetProfit()
    {
        $profitValue = $this->NBPCalculationService->getProfit(1,1,1);

        $this->assertEquals(1, $profitValue);
    }

    public function periodsDataProvider()
    {
        return [];
    }

    public function dateIntervalsDataProvider()
    {
        return [];
    }
}
