<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Console;

use Exception;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\Indexer\RuleIndexer;
use Mageplaza\ProductLabels\Model\MetaFactory;
use Mageplaza\ProductLabels\Model\Rule;
use Mageplaza\ProductLabels\Model\RuleFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ApplyRule
 * @package Mageplaza\SeoRule\Console
 */
class ApplyRule extends Command
{
    /**
     * Name of input option
     */
    const INPUT_KEY_RULE_ID = 'id';

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var RuleIndexer
     */
    protected $ruleIndexer;

    /**
     * ApplyRule constructor.
     *
     * @param RuleFactory $ruleFactory
     * @param MetaFactory $metaFactory
     * @param HelperData $helperData
     * @param State $state
     * @param RuleIndexer $ruleIndexer
     * @param null $name
     */
    public function __construct(
        RuleFactory $ruleFactory,
        MetaFactory $metaFactory,
        HelperData $helperData,
        State $state,
        RuleIndexer $ruleIndexer,
        $name = null
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->metaFactory = $metaFactory;
        $this->helperData  = $helperData;
        $this->state       = $state;
        $this->ruleIndexer = $ruleIndexer;

        parent::__construct($name);
    }

    /**
     * Config command line
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::INPUT_KEY_RULE_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Apply rule id'
            )
        ];
        $this->setName('mpproductlabels:applyrule')
            ->setDescription('Apply rule')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return $this|int|null
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('adminhtml');

        if ($id = $input->getOption(self::INPUT_KEY_RULE_ID)) {
            /** @var Rule $ruleModel */
            $ruleModel = $this->ruleFactory->create()->load($id);
            $metaModel = $this->metaFactory->create();

            if (!$ruleModel->getId()) {
                $output->writeln('Not found. Please check this rule again!');

                return $this;
            }

            try {
                $this->helperData->applyRuleId($ruleModel, $metaModel);
                $output->writeln('Rule id has been applied.');
            } catch (Exception $e) {
                $output->writeln('Cannot apply rule!');
            }
        } else {
            $this->ruleIndexer->executeFull();
            $output->writeln('The rule has been applied.');
        }

        return $this;
    }
}
