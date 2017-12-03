<?php
/**
 * Created by IntelliJ IDEA.
 * User: loup
 * Date: 03.12.17
 * Time: 22:08
 */

namespace Tests;


use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ContainerAwareTestCase extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    public $container;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $kernel = static::createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }
}