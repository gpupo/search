<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Search\Result;

use Gpupo\Search\Result\Collection;
use Gpupo\Search;
use Gpupo\Tests\Search\TestCaseAbstract;

class CollectionTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderProdutosComMarcaNoNome
     */
    public function testResultadosComPropriedadesProcessadas($keyword)
    {
        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $collection = new Collection();
        $results = Search::getInstance()->getResultsByKeyword($keyword, $collection);

        $this->assertInstanceOf('\Gpupo\Search\Result\CollectionInterface', $results);

        $this->assertGreaterThan(2, $results->count());

        foreach ($results->toArray() as $produto) {
            $this->assertInstanceOf('\Gpupo\Search\Result\ItemInterface', $produto);
        }

        return $results;
    }

    public function dataProviderProdutosComMarcaNoNome()
    {
        return [
            ['herrera', 'carolina'],
        ];
    }
}
