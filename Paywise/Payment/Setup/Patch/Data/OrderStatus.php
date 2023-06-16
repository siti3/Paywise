<?php
namespace Paywise\Payment\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class OrderStatus implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Add eav attributes
     */
    public function apply()
    {
        $installer = $this->moduleDataSetup;
        $installer->startSetup();
        $data[] = ['status' => 'failed', 'label' => 'Failed'];
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sales_order_status'),
            ['status', 'label'],
            $data
        );
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default','visible_on_front'],
            [
                ['failed','failed', '0', '1']
            ]
        );
        $installer->endSetup();
    }

    /**
     * Get dependencies
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get Aliases
     */
    public function getAliases()
    {
        return [];
    }
}
