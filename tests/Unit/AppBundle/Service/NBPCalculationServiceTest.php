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

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param array $expectedPeriods
     * @dataProvider periodsDataProvider
     */
    public function testGetDatePeriods($from, $to, $expectedPeriods)
    {
        $periods = $this->NBPCalculationService->getDatePeriods($from, $to);

        foreach($periods as $index => $period) {
            $this->assertArraySubset($expectedPeriods[$index], $period);
        }
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
        return [
            [new \DateTime('now'), new \DateTime('now'), [
                [
                    'from' => (new \DateTime('now'))->format('Y-d-m'),
                    'to' => (new \DateTime('now'))->format('Y-d-m')
                ]
            ]],
            [new \DateTime('now'), new \DateTime('yesterday'), [
                [
                    'from' => (new \DateTime('now'))->format('Y-d-m'),
                    'to' => (new \DateTime('yesterday'))->format('Y-d-m')
                ]
            ]],
            [new \DateTime('2014-12-12'), new \DateTime('2015-12-14'), [
                [
                    'from' => '2014-12-12',
                    'to' => '2015-12-14'
                ]
            ]],
            [new \DateTime('2016-01-1'), new \DateTime('2017-12-14'), [
                [
                    'from' => '2016-01-1',
                    'to' => (new \DateTime('2016-01-1'))
                        ->modify('+'.NBPCalculationService::NBP_DAYS_LIMIT.' days')
                        ->format('Y-d-m')
                ],
                [
                    'from' => (new \DateTime('2016-01-1'))
                        ->modify('+'.NBPCalculationService::NBP_DAYS_LIMIT.' days')
                        ->format('Y-d-m'),
                    'to' => '2017-12-14'
                ]
            ]]
        ];
    }

    public function dateIntervalsDataProvider()
    {
        return [];
    }
}
