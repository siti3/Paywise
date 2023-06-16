<?php
namespace Paywise\Payment\Controller\Index;

use Magento\Framework\App\Action\Context;

class PaywiseInterface extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectUrl;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Framework\Module\Dir
     */
    protected $dir;

    /**
     * @param Magento\Framework\App\Action\Context $context
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Magento\Framework\Controller\Result\RedirectFactory $redirectUrl
     * @param Magento\Checkout\Model\Session $session
     * @param Magento\Sales\Model\Order $order
     * @param Magento\Framework\Module\Dir $dir
     */
    
    public function __construct(
        Context  $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectUrl,
        \Magento\Checkout\Model\Session $session,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Module\Dir $dir,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
        $this->redirectUrl = $redirectUrl;
        $this->session = $session;
        $this->order = $order;
        $this->dir = $dir;
        $this->filesystemDriver = $filesystemDriver;
        $this->filesystem = $filesystem;
    }

    /**
     * return paywise API url
     *
     * @return mix | string | int
     */
    public function execute()
    {
        $redirectUrl = $this->redirectUrl->create();
        $session = $this->session;
        $orderId = $session->getData('last_order_id');
        $order = $this->order->load($orderId);
        $trackid = 'ORDER-'.rand(10000000000, 99999999999);
        $order->setPaywiseTrackingId($trackid);
        $order->save();
        $grandTotal = $order->getGrandTotal();
        $terminalId = $this->scopeConfig->getValue('payment/paywisepayment/terminal_id');
        $configfilepath = $this->scopeConfig->getValue('payment/paywisepayment/custom_file_upload');
        $fileUpload = $this->scopeConfig->getValue('payment/paywisepayment/custom_file_upload');
        $currencyValue = $this->scopeConfig->getValue('payment/paywisepayment/currencys');
        $disableAddressParam = $this->scopeConfig->getValue('payment/paywisepayment/disableAddressParam');
        // @codingStandardsIgnoreStart
        $enableDynamicBillingDescriptor = $this->scopeConfig->getValue('payment/paywisepayment/enableDynamicBillingDescriptor');
        // @codingStandardsIgnoreEnd
        $colors = $this->scopeConfig->getValue('payment/paywisepayment/color');
        $bgcolors = $this->scopeConfig->getValue('payment/paywisepayment/background_color');
        $btntxtcolors = $this->scopeConfig->getValue('payment/paywisepayment/btntxtcolor');
        $api_url = $this->scopeConfig->getValue('payment/paywisepayment/api_url');
        $mReader = $this->dir;
        $color = trim($colors, '#');
        $bgcolor = trim($bgcolors, '#');
        $btntxtcolor = trim($btntxtcolors, '#');
        $disableAddressParam ? $disableAddressParam = 'false' : $disableAddressParam =  'true';
        // @codingStandardsIgnoreStart
        $enableDynamicBillingDescriptor ? $enableDynamicBillingDescriptor = 'true' : $enableDynamicBillingDescriptor =  'false';
        $url = "currency=" . $currencyValue .'&terminalId='. $terminalId . '&trackId='.$trackid .'&amount=' . $grandTotal . '&disableAddressParam=' . $disableAddressParam . '&color=' . $color . '&bgcolor=' . $bgcolor . '&btntxtcolor=' . $btntxtcolor . '&enableDynamicBillingDescriptor='.$enableDynamicBillingDescriptor.'&udfs1='.$orderId;
        // @codingStandardsIgnoreEnd
        $filepath = $mReader->getDir('Paywise_Payment');
        $mediapath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('services/'.$configfilepath);
        $public_key = $this->filesystemDriver->fileGetContents($mediapath);
         // @codingStandardsIgnoreStart
        require_once $filepath.'/Service/BigInteger.php';
        require_once $filepath.'/Service/Hash.php';
        require_once $filepath.'/Service/Random.php';
        require_once $filepath.'/Service/RSA.php';
        $enctptUrl = $this->rsaEncrypt($url, $public_key);
        // @codingStandardsIgnoreEnd
        $epay = urlencode($enctptUrl);
        $redirectUrl->setUrl($api_url.'?epay='.$epay);
        return $redirectUrl;
    }

    /**
     * return encode key
     *
     * @return  int
     */
    public function rsaEncrypt($string, $public_key)
    {
        $cipher = new \Crypt_RSA();
        $cipher->loadKey($public_key);
        $cipher->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $ciphertext = base64_encode($cipher->encrypt($string));
        return $ciphertext;
    }
}
