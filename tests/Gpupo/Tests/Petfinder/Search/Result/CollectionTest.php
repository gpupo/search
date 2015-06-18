<?php

/*
 * This file is part of gpupo/petfinder
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Petfinder\Search\Result;

use Gpupo\Petfinder\Search\Result\Collection;
use Gpupo\Petfinder\Search\Search;
use Gpupo\Tests\Petfinder\TestCaseAbstract;

class CollectionTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderProdutosComMarcaNoNome
     */
    public function testResultadosComPropriedadesProcessadas($keyword)
    {
        $collection = new Collection();
        $results = Search::getInstance()->getResultsByKeyword($keyword, $collection);

        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Result\CollectionInterface', $results);

        $this->assertGreaterThan(2, $results->count());

        foreach ($results->toArray() as $produto) {
            $this->assertInstanceOf('\Gpupo\Petfinder\Search\Result\ItemInterface', $produto);
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
