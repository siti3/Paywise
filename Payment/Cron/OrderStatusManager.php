<?php
namespace Paywise\Payment\Cron;

class OrderStatusManager
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

     /**
      * @param Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
      * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
      */

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * return api response and set order status
     *
     * @return mix | string | int
     * */
    public function execute()
    {
        $orderData = $this->collectionFactory->create();
        $status = 'pending';

        $orderCollection = $orderData->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq'=> $status]);

        if ($orderCollection) {
            // Iterating through each Order with pending status
            // @codingStandardsIgnoreStart
            foreach ($orderCollection as $order) {
                $trackid  = $order->getPaywiseTrackingId();
                $order_id = $order->getId();
                $action = 15;
                $terminalId = $this->scopeConfigInterface->getValue('payment/paywisepayment/terminal_id');
                $password = $this->scopeConfigInterface->getValue('payment/paywisepayment/password');
                $cronUrl = $this->scopeConfigInterface->getValue('payment/paywisepayment/cron_api_url');
                $curl = curl_init();
                curl_setopt_array($curl, [
                CURLOPT_URL => $cronUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "<request>\n<terminalid>".$terminalId."</terminalid>\n<password>".$password."</password>\n<action>".$action."</action>\n<trackid>".$trackid."</trackid>\n<udf1>".$order_id."</udf1>\n</request>",
                CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
                "content-type: application/xml",
                ],
                ]);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                // @codingStandardsIgnoreEnd
                if ($response) {
                    $xmldata = new \SimpleXMLElement($response);
                    $json_string = json_encode($xmldata);
                    $result_array = json_decode($json_string, true);
                    if ($result_array['responsecode'] == '000') {
                        $order->setStatus('processing')->save();
                    } else {
                        $order->setStatus('failed')->save();
                    }
                } elseif (isset($trackid) && $trackid) {
                        $order->setStatus('failed')->save();
                }
            }
        }
    }
}
