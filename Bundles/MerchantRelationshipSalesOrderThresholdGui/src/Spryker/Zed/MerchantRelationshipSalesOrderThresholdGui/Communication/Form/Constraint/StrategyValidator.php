<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantRelationshipSalesOrderThresholdGui\Communication\Form\Constraint;

use Spryker\Zed\MerchantRelationshipSalesOrderThresholdGui\Communication\Form\Type\ThresholdGroup\AbstractMerchantRelationshipThresholdType;
use Spryker\Zed\MerchantRelationshipSalesOrderThresholdGuiExtension\Dependency\Plugin\SalesOrderThresholdFormFieldDependenciesPluginInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StrategyValidator extends ConstraintValidator
{
    protected const MESSAGE_UPDATE_SOFT_STRATEGY_ERROR = 'To save {{strategy_group}} threshold - enter value that is higher than 0 in this field. To delete threshold set all fields equal to 0 or left them empty and save.';
    protected const MESSAGE_KEY = '{{strategy_group}}';

    /**
     * @param string $value
     * @param \Spryker\Zed\MerchantRelationshipSalesOrderThresholdGui\Communication\Form\Constraint\Strategy $constraint
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Strategy) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\StrategyConstraint');
        }

        /** @var \Symfony\Component\Form\Form $form */
        $form = $this->context->getObject();
        $data = $form->getParent()->getData();
        $salesOrderThresholdFormExpanderPlugins = $constraint->getSalesOrderThresholdFormExpanderPlugins();

        foreach ($salesOrderThresholdFormExpanderPlugins as $salesOrderThresholdFormExpanderPlugin) {
            if (!$salesOrderThresholdFormExpanderPlugin instanceof  SalesOrderThresholdFormFieldDependenciesPluginInterface
                || $salesOrderThresholdFormExpanderPlugin->getThresholdKey() !== $data[AbstractMerchantRelationshipThresholdType::FIELD_STRATEGY]
            ) {
                continue;
            }

            /** @var \Spryker\Zed\MerchantRelationshipSalesOrderThresholdGuiExtension\Dependency\Plugin\SalesOrderThresholdFormFieldDependenciesPluginInterface $salesOrderThresholdFormExpanderPlugin */
            if (!$salesOrderThresholdFormExpanderPlugin->getThresholdFieldDependentFieldNames()) {
                continue;
            }

            foreach ($salesOrderThresholdFormExpanderPlugin->getThresholdFieldDependentFieldNames() as $field) {
                if ($data[$field] && !$data[AbstractMerchantRelationshipThresholdType::FIELD_THRESHOLD]) {
                    $message = strtr(static::MESSAGE_UPDATE_SOFT_STRATEGY_ERROR, [static::MESSAGE_KEY => $salesOrderThresholdFormExpanderPlugin->getThresholdGroup()]);
                    $this->context->addViolation($message);
                    return;
                }
            }
        }
    }
}
