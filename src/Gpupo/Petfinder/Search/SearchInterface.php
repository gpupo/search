<?php
/*
 * This file is part of the sfs package.
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search;

use Gpupo\Petfinder\Search\Query\QueryInterface;

interface SearchInterface
{
    public function findByQuery(QueryInterface $query);
    public function factoryCollection(array $array);
    public function getSphinxClient();
}
