<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Dependency\Client;

interface ProductManagementAiToStorageClientInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     *
     * @return mixed
     */
    public function set($key, $value, $ttl = null);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);
}
