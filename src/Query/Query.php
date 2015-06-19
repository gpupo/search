<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Query;

class Query extends QueryAbstract implements QueryInterface
{
    public function toLog()
    {
        return [
            'data'      => $this->toArray(),
            'keywords'  => $this->getQueries(),
            'filter'    => $this->getFilters(),
        ];
    }
}
