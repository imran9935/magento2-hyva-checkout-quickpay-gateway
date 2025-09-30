<?php

declare(strict_types=1);

namespace Magebit\CheckoutQuickPayPayment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Store\Model\ScopeInterface;
use QuickPay\Gateway\Model\Ui\ConfigProvider;

class QuickPay extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AssetRepository
     */
    protected $assetRepo;

    /**
     * QuickPay constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param AssetRepository $assetRepo
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        AssetRepository $assetRepo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Get the description for a specific payment method
     *
     * @param string $methodCode
     * @return string
     */
    public function getDescription(string $methodCode)
    {
        return (string)$this->scopeConfig->getValue(
            sprintf(ConfigProvider::XML_PATH_DESCRIPTION, $methodCode),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return payment logo, label, and description based on selected payment method
     *
     * @param string $methodCode
     * @return array
     */
    public function getPaymentConfigByMethod(string $methodCode)
    {
        $description = $this->getDescription($methodCode);
        $label = '';
        $logo = [];

        if ($methodCode === ConfigProvider::CODE) {
            $logo = $this->getQuickPayCardLogo();
            $label = __('QuickPay'); // Default label for main gateway
        } else {
            $map = $this->getMethodLogoMap();
            if (isset($map[$methodCode])) {
                $logo = [$this->assetRepo->getUrl("QuickPay_Gateway::images/{$map[$methodCode]['logo']}")];
                $label = $map[$methodCode]['label'];
            }
        }

        return [
            'paymentLogo' => $logo,
            'description' => $description,
            'label'       => $label,
        ];
    }

    /**
     * Get mapping of payment methods to their logo files and labels
     *
     * @return array
     */
    private function getMethodLogoMap()
    {
        return [
            ConfigProvider::CODE_KLARNA    => ['logo' => 'klarna.svg', 'label' => 'Klarna'],
            ConfigProvider::CODE_APPLEPAY  => ['logo' => 'apple-pay.svg', 'label' => 'Apple Pay'],
            ConfigProvider::CODE_MOBILEPAY => ['logo' => 'mobilepay_payment.png', 'label' => 'MobilePay'],
            ConfigProvider::CODE_VIPPS     => ['logo' => 'vipps.png', 'label' => 'Vipps'],
            ConfigProvider::CODE_PAYPAL    => ['logo' => 'paypal.svg', 'label' => 'PayPal'],
            ConfigProvider::CODE_VIABILL   => ['logo' => 'viabill.png', 'label' => 'ViaBill'],
            ConfigProvider::CODE_SWISH     => ['logo' => 'swish.png', 'label' => 'Swish'],
            ConfigProvider::CODE_TRUSTLY   => ['logo' => 'trustly.svg', 'label' => 'Trustly'],
            ConfigProvider::CODE_ANYDAY    => ['logo' => 'anydaysplit.svg', 'label' => 'Anyday'],
            ConfigProvider::CODE_GOOGLEPAY => ['logo' => 'google-pay.svg', 'label' => 'Google Pay'],
        ];
    }

    /**
     * Return logos for main QuickPay gateway
     *
     * @return array
     */
    protected function getQuickPayCardLogo()
    {
        $cards = explode(',', (string)$this->scopeConfig->getValue(ConfigProvider::XML_PATH_CARD_LOGO, ScopeInterface::SCOPE_STORE));
        $cardsSvg = ['maestro', 'mastercard', 'visa'];
        $items = [];

        foreach ($cards as $card) {
            $card = trim($card);
            if ($card !== '') {
                $ext = in_array($card, $cardsSvg, true) ? 'svg' : 'png';
                $items[] = $this->assetRepo->getUrl("QuickPay_Gateway::images/logo/{$card}.{$ext}");
            }
        }

        return $items;
    }
}
