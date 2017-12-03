<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class searchForProfitCommand extends ContainerAwareCommand
{
    const NBP_TIME_LIMIT = 367;
    const DEFAULT_INVESTMENT = 600000;
    const SAVE_SEARCH_RESULT = 1;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('24net:search_for_profit_command')
            ->setDescription('search for most profitable time to invest in gold during last 5 years')
            ->addArgument(
                'from',
                InputArgument::REQUIRED,
                'date from which to perform search'
            )
            ->addArgument(
                'to',
                InputArgument::REQUIRED,
                'date to which search is to be performed'
            )
            ->addArgument(
                'investment',
                InputArgument::REQUIRED,
                'amount to be invested', self::DEFAULT_INVESTMENT
            )
            ->addArgument(
                'cache',
                InputArgument::OPTIONAL,
                'save search in cache to hasten the process next time',
                self::SAVE_SEARCH_RESULT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->validateArguments($input, $output)) {
            $this->calculateDatePeriods();
            $this->getDataForPeriods();
            $this->setReceivedData();
            $this->calculateInvestment();
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
        return true;
    }

    /**
     * divide time span into chunks consisting of number of days allowed by the API
     */
    private function calculateDatePeriods()
    {
    }

    /**
     * retrieve date periods from the API based upon calculated time chunks
     */
    private function getDataForPeriods()
    {
    }

    /**
     * save data into cache
     */
    private function setReceivedData()
    {
    }

    /**
     * calculate the best investment period for supplied time span
     */
    private function calculateInvestment()
    {
    }
}
