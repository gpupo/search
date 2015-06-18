<?php

/*
 * This file is part of gpupo/petfinder
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Petfinder\Sphinx;

use Gpupo\Petfinder\Sphinx\SphinxClient;
use Gpupo\Petfinder\Sphinx\SphinxService;
use Gpupo\Tests\Petfinder\TestCaseAbstract;

class SphinxServiceTest extends TestCaseAbstract
{
    public function testPermiteAcessoAosParametrosDefault()
    {
        $parameters = SphinxService::getInstance()->getParameters();

        foreach (['host', 'port', 'timeout'] as $key) {
            $this->assertTrue($parameters->offsetExists($key));
        }
    }

    public function testPermiteDefinicaoDeParametrosPersonalizados()
    {
        $array = [
            'host'      => 'example.com',
            'port'      => 9312,
            'timeout'   => 5,
        ];

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