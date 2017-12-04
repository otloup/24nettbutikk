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
     * @expectedException \Exception
     */
    public function textGetDatePeriodsException()
    {
        $this->NBPCalculationService->getDatePeriods(new \DateTime('now'), new \DateTime('yesterday'));
        $this->expectException(\Exception::class);
    }

    /**
     * @test
     * @dataProvider periodPricesDataProvider
     * @param array $period
     * @param float $expectedValue
     */
    public function testGetMinValueFromPeriod($period, $expectedValue)
    {
        $minValue = $this->NBPCalculationService->getMinValueFromPeriod($period);

        $this->assertEquals($expectedValue['min'], $minValue);
    }

    /**
     * @test
     * @dataProvider periodPricesDataProvider
     * @param array $period
     * @param float $expectedValue
     */
    public function testGetMaxValueFromPeriod($period, $expectedValue)
    {
        $maxValue = $this->NBPCalculationService->getMaxValueFromPeriod($period);

        $this->assertEquals($expectedValue['max'], $maxValue);
    }

    /**
     * @param float $min
     * @param float $max
     * @param float $investment
     * @param float $expectedValue
     * @dataProvider profitDataProvider
     */
    public function testGetProfit($min, $max, $investment, $expectedValue)
    {
        $profitValue = $this->NBPCalculationService->getProfit($min,$max,$investment);

        $this->assertEquals($expectedValue, $profitValue);
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

    public function periodPricesDataProvider()
    {
        return [
                [
                    [
                        [
                            "data" => "2016-12-01",
                            "cena" => 158.04
                        ],
                        [
                            "data" => "2016-12-02",
                            "cena" => 156.82
                        ],
                        [
                            "data" => "2016-12-05",
                            "cena" => 159.03
                        ]
                    ],
                    [
                        'min' => 156.82,
                        'max' => 159.03
                    ]
                ],
                [
                    [
                        [
                            "data" => "2016-12-06",
                            "cena" => 157.75
                        ],
                        [
                            "data" => "2016-12-07",
                            "cena" => 157.68
                        ],
                        [
                            "data" => "2016-12-08",
                            "cena" => 156.24
                        ],
                        [
                            "data" => "2016-12-09",
                            "cena" => 154.77
                        ]
                    ],
                    [
                        'min' => 154.77,
                        'max' => 157.75
                    ],
                ]
        ];
    }

    public function profitDataProvider()
    {
        return [
            [12.15, 15.4, 100000, 126748.97],
            [1.15, 5.4, 1000.6, 4698.46],
            [120.09, 121, 500, 503.78],
        ];
    }
}
