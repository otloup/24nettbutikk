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
     * check if periods are properly calculated
     *
     * @test
     * @param \DateTime $from
     * @param \DateTime $to
     * @param array $expectedPeriods
     * @dataProvider periodsDataProvider
     * @throws \Exception
     */
    public function testGetDatePeriods($from, $to, $expectedPeriods)
    {
        $periods = $this->NBPCalculationService->getDatePeriods($from, $to);

        foreach($periods as $index => $period) {
            $this->assertArraySubset($expectedPeriods[$index], $period);
        }
    }

    /**
     * check if improperly oriented dates are validated
     * @test
     * @throws \Exception
     */
    public function textGetDatePeriodsException()
    {
        $this->NBPCalculationService->getDatePeriods(new \DateTime('yesterday'), new \DateTime('now'));
        $this->expectException(\Exception::class);
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
        $now = new \DateTime('now');
        $yesterday = new \DateTime('yesterday');
        $jan1 = \DateTime::createFromFormat(NBPCalculationService::NBP_TIME_FORMAT, '2016-01-01');
        $dec13 = \DateTime::createFromFormat(NBPCalculationService::NBP_TIME_FORMAT, '2014-13-12');
        $dec14 = \DateTime::createFromFormat(NBPCalculationService::NBP_TIME_FORMAT, '2015-14-12');
        $dec15 = \DateTime::createFromFormat(NBPCalculationService::NBP_TIME_FORMAT, '2017-15-12');

        return [
            'now_to_now' => [$now, $now, [
                [
                    'from' => $now->format(NBPCalculationService::NBP_TIME_FORMAT),
                    'to' => $now->format(NBPCalculationService::NBP_TIME_FORMAT)
                ]
            ]],
            'yesterday_to_now' => [$yesterday, $now, [
                [
                    'from' => $yesterday->format(NBPCalculationService::NBP_TIME_FORMAT),
                    'to' => $now->format(NBPCalculationService::NBP_TIME_FORMAT)
                ]
            ]],
            'dec13_to_dec14' => [$dec13, $dec14, [
                [
                    'from' => '2014-13-12',
                    'to' => '2015-14-12'
                ]
            ]],
            'jan1_to_dec15' => [$jan1, $dec15, [
                [
                    'from' => '2016-01-01',
                    'to' => (clone $jan1)
                        ->modify('+'.NBPCalculationService::NBP_DAYS_LIMIT.' days')
                        ->format(NBPCalculationService::NBP_TIME_FORMAT)
                ],
                [
                    'from' => (clone $jan1)
                        ->modify('+'.NBPCalculationService::NBP_DAYS_LIMIT.' days')
                        ->format(NBPCalculationService::NBP_TIME_FORMAT),
                    'to' => '2017-15-12'
                ]
            ]]
        ];
    }

    public function dateIntervalsDataProvider()
    {
        return [];
    }
}
