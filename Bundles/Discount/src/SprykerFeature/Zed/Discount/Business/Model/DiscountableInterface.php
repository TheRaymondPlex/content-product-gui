<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Discount\Business\Model;

use SprykerFeature\Shared\Discount\Dependency\Transfer\DiscountableItemCollectionInterface;

interface DiscountableInterface
{
    /**
     * @return float
     */
    public function getGrossPrice();

    /**
     * @return \ArrayObject
     */
    public function getDiscounts();

    /**
     * @param \ArrayObject $discountCollection
     *
     * @return $this
     */
    public function setDiscounts(\ArrayObject $discountCollection);
}
