<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Result;

use Gpupo\Search\Sphinx\ResultInterface as SphinResultInterface;

interface CollectionInterface extends SphinResultInterface
{
    public function factoryItem(array $array);
}
