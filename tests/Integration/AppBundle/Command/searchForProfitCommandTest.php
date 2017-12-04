<?php
/**
 * Created by IntelliJ IDEA.
 * User: loup
 * Date: 03.12.17
 * Time: 05:53
 */

namespace Tests\AppBundle\Command;

use AppBundle\Command\searchForProfitCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

class searchForProfitCommandTest extends KernelTestCase
{
   /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->application = new Application();
        $this->application->add(new searchForProfitCommand());
    }

    /**
     * @param $from
     * @param $to
     * @param $investment
     * @param $cache
     * @dataProvider properInvestmentDataProvider
     */
    public function testExecute(
        $from,
        $to,
        $investment,
        $cache
    ){

        $command = $this->application->find('24net:search_for_profit');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'from' => $from,
            'to' => $to,
            'investment' => $investment,
            'cache' => $cache
        ]);
    }

    public function properInvestmentDataProvider()
    {
        return [
//            ['2014-13-12', '2015-12-14', '1000', null],
//            ['2013-12-13', '2013-12-14', '10000', null],
//            ['2016-01-1', '2017-12-14', '100000', null],
//            ['2001-33-02', '2005-12-14', '100', null],
        ];
    }
}
