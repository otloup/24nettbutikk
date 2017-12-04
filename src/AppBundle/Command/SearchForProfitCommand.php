<?php

namespace AppBundle\Command;

use AppBundle\Service\NBPCalculationService;
use AppBundle\Service\NBPDataRetrievalService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchForProfitCommand extends ContainerAwareCommand
{
    const DEFAULT_INVESTMENT = 600000;
    const DEFAULT_TIME_SPAN = '-4 years';
    const MIN_DATE = '2013-01-02';

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var \DateTime
     */
    private $dateFrom;

    /**
     * @var \DateTime
     */
    private $dateTo;

    /**
     * @var int
     */
    private $investment;

    /**
     * @var NBPCalculationService
     */
    private $NBPCalculationService;

    /**
     * @var NBPDataRetrievalService
     */
    private $NBPDataRetrievalService;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('24net:search-for-profit')
            ->setDescription('search for most profitable time to invest in gold during last 5 years')
            ->addArgument(
                'from',
                InputArgument::OPTIONAL,
                'date from which to perform search. Valid format: year-month-day'
            )
            ->addArgument(
                'to',
                InputArgument::OPTIONAL,
                'date to which search is to be performed. Valid format: year-month-day'
            )
            ->addArgument(
                'investment',
                InputArgument::OPTIONAL,
                'amount to be invested', self::DEFAULT_INVESTMENT
            )
            ->addOption(
                'cache',
                'c',
                InputArgument::OPTIONAL,
                'save search in cache to hasten the process next time',
                false
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->NBPCalculationService = $this->getContainer()->get(NBPCalculationService::class);
        $this->NBPDataRetrievalService = $this->getContainer()->get(NBPDataRetrievalService::class);


        $this->input = $input;
        if ($this->validateArguments($input, $this->output)) {
            $prices = $this->getDataForPeriods();

            if (true === empty($prices)) {
                $this->output->writeln('no data has been retrieved. Please try again later');
                exit(1);
            } else {
                $investment = $this->getInvestmentReturn($prices['min']['price'], $prices['max']['price']);
            }

            $yield = $investment - $this->investment;

            $this->output->writeln('Optimal day to buy was on ' .
                $prices['min']['date']
                . '(at price ' .
                $prices['min']['price']
                . ')');
            $this->output->writeln('Optimal day to sell was on ' .
                $prices['max']['date']
                . '(at price ' .
                $prices['max']['price']
                . ')');
            $this->output->writeln($this->investment . 'PLN invested in such way would yield ' . $investment . 'PLN');
            exit(0);
        } else {
            $this->output->writeln('supplied arguments are invalid');
            exit(1);
        }
    }

    /**
     * check if all arguments are valid
     *
     * @param $input
     * @param $output
     * @return bool
     */
    private function validateArguments(InputInterface $input, OutputInterface $output)
    {
        //retrieve passed arguments
        $argumentDateFrom = $input->getArgument('from');
        $argumentDateTo = $input->getArgument('to');
        $investment = $input->getArgument('investment');

        //check if dates have valid format
        if (true === empty($argumentDateFrom) || true === empty($argumentDateTo)) {
            $this->dateFrom = (new \DateTime())->modify(self::DEFAULT_TIME_SPAN);
            $this->dateTo = new \DateTime();
        } else {
            /** \DateTime $convertedFrom */
            if (false === $convertedFrom = \DateTime::createFromFormat(
                    NBPCalculationService::NBP_TIME_FORMAT, $argumentDateFrom
                )
            ) {
                $output->writeln("Date from doesn't have proper format of Year-Month-Day");
                return false;
            }

            /** \DateTime $convertedTo */
            if (false === ($convertedTo = \DateTime::createFromFormat(
                    NBPCalculationService::NBP_TIME_FORMAT, $argumentDateTo)
                )
            ) {
                $output->writeln("Date to doesn't have proper format of Year-Month-Day");
                return false;
            }

            if ($convertedFrom < \DateTime::createFromFormat(
                    NBPCalculationService::NBP_TIME_FORMAT, self::MIN_DATE
                )
            ) {
                $output->writeln("Date from cannot be earlier than " . self::MIN_DATE);
                return false;
            }

            if ($convertedTo > new \DateTime()) {
                $output->writeln("Date to cannot be in the future");
                return false;
            }

            if ($convertedTo < $convertedFrom) {
                $this->dateFrom = $convertedFrom;
                $this->dateTo = $convertedTo;
            } else {
                $output->writeln("Date to must be later than date from");
                return false;
            }
        }

        if (true === empty($investment)) {
            $this->investment = self::DEFAULT_INVESTMENT;
        } else {
            //check if investment is numeric
            if (intval($investment) > 0) {
                $this->investment = intval($investment);
            } else {
                $output->writeln("Investment amount must be a number");
                return false;
            }
        }

        return true;
    }

    /**
     * retrieve date periods from the API based upon calculated time chunks
     * @throws \Exception
     */
    private function getDataForPeriods()
    {
        try {
            return $this->NBPDataRetrievalService->getDataForTimespan($this->dateFrom, $this->dateTo);
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
            return false;
        }
    }

    /**
     * calculate the best investment period for supplied time span
     * @param $minPrice
     * @param $maxPrice
     * @return float
     */
    private function getInvestmentReturn($minPrice, $maxPrice)
    {
        return $this->NBPCalculationService->getProfit($minPrice, $maxPrice, $this->investment);
    }
}
