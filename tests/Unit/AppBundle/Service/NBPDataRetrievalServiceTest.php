<?php
/**
 * Created by IntelliJ IDEA.
 * User: loup
 * Date: 03.12.17
 * Time: 06:48
 */

namespace Tests\AppBundle\Service;

use AppBundle\Service\NBPDataRetrievalService;
use Tests\ContainerAwareTestCase;

class NBPDataRetrievalServiceTest extends ContainerAwareTestCase
{

    /**
     * @var NBPDataRetrievalService
     */
    private $NBPDataRetrievalService;

    public function setUp()
    {
        $this->NBPDataRetrievalService = $this->container->get(NBPDataRetrievalService::class);
    }

    public function testGetDataForTimespan()
    {
        $result = $this->NBPDataRetrievalService->getDataForTimespan(new \DateTime(), new \DateTime());
        $this->assertEquals(1, $result);
    }
}
