<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\SaveAllProducts\Model;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Symfony\Component\Console\Output\OutputInterface;

class SaveAllProducts
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param OutputInterface|null $output
     */
    public function execute(string $type = '', OutputInterface $output = null)
    {
        $collectionCount = null;
        $productCollection = $this->collectionFactory->create();
        $currPage = 1;
        $lastPage = 0;
        $break = false;
        while ($break !== true) {
            $collection = clone $productCollection;
            $collection->setPageSize(100);
            $collection->setCurPage($currPage);
            $collection->load();
            if ($collectionCount === null) {
                $collectionCount = $collection->getSize();
                $lastPage = $collection->getLastPageNumber();
            }
            if ($currPage == $lastPage) {
                $break = true;
            }
            $currPage++;
            /** @var ProductInterface $product */
            foreach ($collection->getItems() as $product) {
                if (!empty($type) && $product->getTypeId() !== $type) {
                    continue;
                }

                try {
                    $product->load($product->getId());
                    $product->save();
                    if ($output) {
                        $output->writeln(__("Product sku %1 saved", $product->getSku()));
                    }
                } catch (Exception $e) {
                    if ($output) {
                        $output->writeln(__("Product sku %1 NOT saved, error: ", $product->getSku(), $e->getMessage()));
                    }
                }
            }
        }
    }
}
