<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\SaveAllProducts\Console\Command;

use Exception;
use GhostUnicorns\SaveAllProducts\Model\SaveAllProducts;
use GhostUnicorns\SaveAllProducts\Model\SetAreaCode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveAllProductsCommand extends Command
{
    const ARGUMENT_NAME = 'type';

    /**
     * @var SetAreaCode
     */
    private $setAreaCode;

    /**
     * @var SaveAllProducts
     */
    private $saveAllProducts;

    /**
     * @param SetAreaCode $setAreaCode
     * @param SaveAllProducts $saveAllProducts
     * @param string $name
     */
    public function __construct(
        SetAreaCode $setAreaCode,
        SaveAllProducts $saveAllProducts,
        $name = null
    ) {
        parent::__construct($name);
        $this->setAreaCode = $setAreaCode;
        $this->saveAllProducts = $saveAllProducts;
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->setAreaCode->execute('adminhtml');
            $output->setDecorated(true);
            $type = $input->getArgument(self::ARGUMENT_NAME);
            if (!$type) {
                $type = '';
            }

            $this->saveAllProducts->execute($type, $output);
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('save-all-products')
            ->setDescription('Trigger a save on all product')
            ->addArgument(
                self::ARGUMENT_NAME,
                InputArgument::OPTIONAL,
                'Save only specific product type: simple or configurable'
            );
        parent::configure();
    }
}
