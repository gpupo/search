<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search;

use Gpupo\Search\Query\QueryInterface;

interface SearchInterface
{
    public function findByQuery(QueryInterface $query);
    public function factoryCollection(array $array);
    public function getSphinxClient();
}
