<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientBridge;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeBridge;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeBridge;
use SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceBridge;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 */
class ProductManagementAiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_CATEGORY = 'FACADE_CATEGORY';

    /**
     * @var string
     */
    public const FACADE_LOCALE = 'FACADE_LOCALE';

    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @var string
     */
    public const CLIENT_OPEN_AI = 'CLIENT_OPEN_AI';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = $this->addCategoryFacade($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addOpenAiClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = $this->addCategoryFacade($container);
        $container = $this->addLocaleFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryFacade(Container $container): Container
    {
        $container->set(static::FACADE_CATEGORY, function (Container $container) {
            return new ProductManagementAiToCategoryFacadeBridge($container->getLocator()->category()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addLocaleFacade(Container $container): Container
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return new ProductManagementAiToLocaleFacadeBridge($container->getLocator()->locale()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new ProductManagementAiToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOpenAiClient(Container $container): Container
    {
        $container->set(static::CLIENT_OPEN_AI, function (Container $container) {
            return new ProductManagementAiToOpenAiClientBridge($container->getLocator()->openAi()->client());
        });

        return $container;
    }
}
