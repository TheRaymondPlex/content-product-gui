<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\ProductOptionCheckoutConnector\Communication\Plugin;

use Generated\Shared\Transfer\CheckoutRequestTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Zed\Checkout\Dependency\Plugin\CheckoutOrderHydrationInterface;
use Generated\Shared\ProductOption\ProductOptionInterface;
use Generated\Shared\ProductOptionCartConnector\ItemInterface;

class GroupKeyProductOptionHydrationPlugin extends AbstractPlugin implements CheckoutOrderHydrationInterface
{
    /**
     * @param OrderTransfer           $orderTransfer
     * @param CheckoutRequestTransfer $checkoutRequest
     */
    public function hydrateOrder(OrderTransfer $orderTransfer, CheckoutRequestTransfer $checkoutRequest)
    {
        foreach ($orderTransfer as $orderItem) {
            $orderItem->setGroupKey($this->buildGroupKey($orderItem));
        }
    }

    /**
     * @param ItemInterface $item
     *
     * @return string
     */
    protected function buildGroupKey(ItemInterface $item)
    {
        $currentGroupKey = $item->getGroupKey();
        if (empty($item->getProductOptions())) {
            return $currentGroupKey;
        }

        $sortedProductOptions = $this->sortOptions((array) $item->getProductOptions());
        $optionGroupKey = $this->combineOptionParts($sortedProductOptions);

        if (empty($optionGroupKey)) {
            return $currentGroupKey;
        }

        return !empty($currentGroupKey) ? $currentGroupKey . '-' . $optionGroupKey : $optionGroupKey;
    }

    /**
     * @param ProductOptionInterface[] $options
     *
     * @return array
     */
    protected function sortOptions(array $options)
    {
        usort(
            $options,
            function (ProductOptionInterface $productOption, ProductOptionInterface $productOption) {
                return ($productOption->getIdOptionValueUsage() < $productOption->getIdOptionValueUsage()) ? -1 : 1;
            }
        );

        return $options;
    }

    /**
     * @param ProductOptionInterface[] $sortedProductOptions
     *
     * @return string
     */
    protected function combineOptionParts(array $sortedProductOptions)
    {
        $groupKeyPart = [];
        foreach ($sortedProductOptions as $option) {
            $groupKeyPart[] = $option->getIdOptionValueUsage();
        }
        return implode('-', $groupKeyPart);
    }
}
