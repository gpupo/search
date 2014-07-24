<?php

namespace Gpupo\Tests\Sfs\Sphinx;

use Gpupo\Tests\Sfs\TestCaseAbstract;
use Gpupo\Sfs\Sphinx\SphinxService;
use Gpupo\Sfs\Sphinx\SphinxClient;

class SphinxServiceTest extends TestCaseAbstract
{
    public function testPermiteAcessoAosParametrosDefault()
    {
        $parameters = SphinxService::getInstance()->getParameters();
        
        foreach (array('host', 'port', 'timeout') as $key) {
            $this->assertTrue($parameters->offsetExists($key));
        }
    }
    
    public function testPermiteDefinicaoDeParametrosPersonalizados()
    {
        $array = array(
            'host'      => 'example.com',
            'port'      => 9312,
            'timeout'   => 5,
        );
            
        $parameters = SphinxService::getInstance()->setParameters($array)
            ->getParameters();
        
        foreach ($array as $key => $value) {
            $this->assertTrue($parameters->offsetExists($key));
            $this->assertEquals($value, $parameters->get($key));
        }
    }

    public function testDisponibilizaAcessoAoClient()
    {
        $client = SphinxService::getInstance()->getClient();
        $this->assertTrue($client instanceof SphinxClient);
    }

    public function testAcessoSingletonAoClientComReset()
    {
        $client = SphinxService::getInstance()->getFreshClient();
        $this->assertTrue($client instanceof SphinxClient);
    }
}
