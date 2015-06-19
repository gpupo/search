<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Search\Query;

use Gpupo\Search\Query\Filters;
use Gpupo\Tests\Search\TestCaseAbstract;

class FiltersTest extends TestCaseAbstract
{
    public function testFiltraPorListaDeValoresDeUmaChave()
    {
        $filters = new Filters();
        $filters->addValuesFilter('chave', [1, 3, 6, 7]);
        $filters->addValuesFilter('outra_chave', [5, 3, 'abc', 7]);
        $this->assertInternalType('array', $filters->toArray());
        $this->assertEquals(2, count($filters->toArray()));
        foreach ($filters->toArray() as $filter) {
            $this->assertArrayHasKey('key', $filter);
            $this->assertArrayHasKey('values', $filter);
            $this->assertContains(7, $filter['values']);
            $this->assertContains(3, $filter['values']);
        }
    }

    public function testFiltraPorRangeDeValoresDeUmaChave()
    {
        $filters = new Filters();
        $filters->addRangeFilter('chave', 1, 10);
        $filters->addRangeFilter('outra_chave', 1, 10);
        $this->assertInternalType('array', $filters->toArray());
        $this->assertEquals(2, count($filters->toArray()));
        foreach ($filters->toArray() as $filter) {
            $this->assertArrayHasKey('key', $filter);
            $this->assertArrayHasKey('min', $filter);
            $this->assertArrayHasKey('max', $filter);
        }
    }

    public function testAdicionaUmValorAValuesFiltersExistente()
    {
        $filters = new Filters();

        foreach (range(1, 12) as $n) {
            $filters->appendValueFilter('number', $n);
        }

        $this->assertEquals(12, count($filters->getValues('number')), 'Numeros');
        $this->assertContains(3, $filters->getValues('number'));
        $this->assertContains(8, $filters->getValues('number'));

        foreach (\range('a', 'i') as $l) {
            $filters->appendValueFilter('letter', $l);
        }

        $this->assertEquals(9, count($filters->getValues('letter')), 'Letras');
        $this->assertContains('b', $filters->getValues('letter'));
        $this->assertContains('f', $filters->getValues('letter'));
    }
}
