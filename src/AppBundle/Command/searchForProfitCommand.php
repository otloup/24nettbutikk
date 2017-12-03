<?php

namespace AppBundle\Command;

use AppBundle\Service\NBPCalculationService;
use AppBundle\Service\NBPDataRetrievalService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class searchForProfitCommand extends ContainerAwareCommand
{
    const NBP_TIME_LIMIT = 367;
    const DEFAULT_INVESTMENT = 600000;

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
        $this->NBPCalculationService = $this->getContainer()->get(NBPCalculationService::class);
        $this->NBPDataRetrievalService = $this->getContainer()->get(NBPDataRetrievalService::class);

        $this
            ->setName('24net:search_for_profit')
            ->setDescription('search for most profitable time to invest in gold during last 5 years')
            ->addArgument(
                'from',
                InputArgument::REQUIRED,
                'date from which to perform search. Valid format: year-day-month'
            )
            ->addArgument(
                'to',
                InputArgument::REQUIRED,
                'date to which search is to be performed. Valid format: year-day-month'
            )
            ->addArgument(
                'investment',
                InputArgument::REQUIRED,
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
        if ($this->validateArguments($input, $output)) {
            $this->getDataForPeriods();
            $investment = $this->getInvestment();

            $output->write('return on your investment is' . $investment);
            exit(0);
        } else {
            $output->write('supplied arguments are invalid');
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
        $cache = $input->getOption('cache');

        //check if dates have valid format
        if (true === empty($argumentDateFrom) || true === empty($argumentDateTo)) {
            $output->writeln('Date from and date to has to be filled');
            return false;
        }

        /** \DateTime $convertedFrom */
        if (false === $convertedFrom = \DateTime::createFromFormat('Y-d-m', $argumentDateFrom)){
            $output->writeln("Date from doesn't have proper format of Year-Day-Month");
            return false;
        }

        /** \DateTime $convertedTo */
        if (false === ($convertedTo = \DateTime::createFromFormat('Y-d-m', $argumentDateTo))){
            $output->writeln("Date to doesn't have proper format of Year-Day-Month");
            return false;
        }

        if ($convertedTo < $convertedFrom) {
            $this->dateFrom = $convertedFrom;
            $this->dateTo = $convertedTo;
        } else {
            $output->writeln("Date to must be later than date from");
            return false;
        }

        //check if investment is numeric
        if (intval($investment) > 0) {
            $this->investment = intval($investment);
        } else {
            $output->writeln("Investment amount must be a number");
            return false;
        }

        return true;
    }

    /**
     * retrieve date periods from the API based upon calculated time chunks
     */
    private function getDataForPeriods()
    {
        return $this->NBPDataRetrievalService->getDataForTimespan();
        return true;
    }

    /**
     * calculate the best investment period for supplied time span
     */
    private function getInvestment()
    {
        return 1;
    }
}
