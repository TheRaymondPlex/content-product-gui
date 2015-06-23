<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Calculation\Business\Model\Calculator;

use Generated\Shared\Calculation\ExpenseInterface;
use Generated\Shared\Calculation\OrderInterface;
use Generated\Shared\Transfer\ExpensesTransfer;
use Generated\Shared\Transfer\ExpenseTotalItemTransfer;
use Generated\Shared\Transfer\ExpenseTotalsTransfer;
use Generated\Shared\Calculation\TotalsInterface;
use SprykerFeature\Zed\Calculation\Dependency\Plugin\TotalsCalculatorPluginInterface;

class ExpenseTotalsCalculator implements
    TotalsCalculatorPluginInterface,
    ExpenseTotalsCalculatorInterface
{
    /**
     * @param TotalsInterface $totalsTransfer
     * @param OrderInterface $calculableContainer
     * @param \ArrayObject $calculableItems
     */
    public function recalculateTotals(
        TotalsInterface $totalsTransfer,
        OrderInterface $calculableContainer,
        \ArrayObject $calculableItems
    ) {
        $expenseTotal = $this->createExpenseTotalTransfer($calculableContainer, $calculableItems);
        $totalsTransfer->setExpenses($expenseTotal);
    }

    /**
     * @param OrderInterface $calculableContainer
     *
     * @return int
     */
    public function calculateExpenseTotal(OrderInterface $calculableContainer)
    {
        $orderExpensesTotal = 0;
        if ($calculableContainer->getExpenses() instanceof ExpensesTransfer) {
            $expenses = $calculableContainer->getExpenses()->getCalculationExpenses();
        } else {
            $expenses = $calculableContainer->getExpenses();
        }

        foreach ($expenses as $expense) {
            if (!is_null($expense->getGrossPrice())) {
                $orderExpensesTotal += $expense->getGrossPrice();
            }
        }

        return $orderExpensesTotal;
    }

    /**
     * @param \ArrayObject|CalculableItemInterface[] $calculableItems
     * @return int
     */
    protected function calculateItemExpenseTotal(\ArrayObject $calculableItems)
    {
        $itemExpenseTotal = 0;
        foreach ($calculableItems as $item) {
            if ($item->getExpenses() instanceof ExpensesTransfer) {
                $expenses = $item->getExpenses()->getCalculationExpenses();
            } else {
                $expenses = $item->getExpenses();
            }

            foreach ($expenses as $expense) {
                if (!is_null($expense->getGrossPrice())) {
                    $itemExpenseTotal += $expense->getGrossPrice();
                }
            }
        }

        return $itemExpenseTotal;
    }

    /**
     * @param OrderInterface $calculableContainer
     * @param \ArrayObject $calculableItems
     *
     * @return array
     */
    protected function sumExpenseItems(
        OrderInterface $calculableContainer,
        \ArrayObject $calculableItems
    ) {
        $orderExpenseItems = [];

        if ($calculableContainer->getExpenses() instanceof ExpensesTransfer) {
            foreach ($calculableContainer->getExpenses()->getCalculationExpenses() as $expense) {
                $this->transformExpenseToExpenseTotalItemInArray($expense, $orderExpenseItems);
            }
        }

        foreach ($calculableItems as $item) {
            foreach ($item->getExpenses() as $expense) {
                $this->transformExpenseToExpenseTotalItemInArray($expense, $orderExpenseItems);
            }
        }

        return $orderExpenseItems;
    }

    /**
     * @param ExpenseInterface $expense
     * @param array $arrayOfExpenseTotalItems
     */
    protected function transformExpenseToExpenseTotalItemInArray(ExpenseInterface $expense, array &$arrayOfExpenseTotalItems)
    {
        if (!isset($arrayOfExpenseTotalItems[$expense->getType()])) {
            $item = new ExpenseTotalItemTransfer();
            $item->setName($expense->getName());
            $item->setType($expense->getType());
        } else {
            $item = $arrayOfExpenseTotalItems[$expense->getType()];
        }

        $item->setGrossPrice($item->getGrossPrice() + $expense->getGrossPrice());
        $item->setPriceToPay($item->getPriceToPay() + $expense->getPriceToPay());
        $arrayOfExpenseTotalItems[$expense->getType()] = $item;
    }

    /**
     * @param OrderInterface $calculableContainer
     * @param \ArrayObject $calculableItems
     *
     * @return ExpenseTotalsTransfer
     */
    protected function createExpenseTotalTransfer(
        OrderInterface $calculableContainer,
        \ArrayObject $calculableItems
    ) {
        $expenseTotalTransfer = new ExpenseTotalsTransfer();
        $expenseTotalTransfer->setTotalOrderAmount($this->calculateExpenseTotal($calculableContainer));
        $expenseTotalTransfer->setTotalItemAmount($this->calculateItemExpenseTotal($calculableItems));

        foreach ($this->sumExpenseItems($calculableContainer, $calculableItems) as $expenseTotalItem) {
            $expenseTotalTransfer->addExpenseItem($expenseTotalItem);
        }

        return $expenseTotalTransfer;
    }
}
