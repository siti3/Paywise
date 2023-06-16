<?php
namespace Paywise\Payment\Block;
use Magento\Framework\View\Element\Template;

class Orderstatus extends Template
{
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $orderinterface
    ) {
        $this->orderinterface = $orderinterface;
    }

    public function updateOrderStatus($incrementId,$status)
    {
        $order = $this->orderinterface->loadByIncrementId($incrementId);
        $order->setStatus($status)->save();
        return true;
    }
}