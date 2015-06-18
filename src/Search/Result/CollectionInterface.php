<?php

/*
 * This file is part of gpupo/petfinder
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search\Result;

use Gpupo\Petfinder\Sphinx\ResultInterface as SphinResultInterface;

interface CollectionInterface extends SphinResultInterface
{
    public function factoryItem(array $array);
}
