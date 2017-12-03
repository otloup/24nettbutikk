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

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getDataForTimespan()
    {

    }
}