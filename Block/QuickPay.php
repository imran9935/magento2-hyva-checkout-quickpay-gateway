<?php

declare(strict_types=1);

namespace Magebit\CheckoutQuickPayPayment\Block;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Store\Model\ScopeInterface;

class QuickPay extends Template
{
    public const CODE = 'quickpay_gateway';
    public const CODE_KLARNA = 'quickpay_klarna';
    public const CODE_APPLEPAY = 'quickpay_applepay';
    public const CODE_MOBILEPAY = 'quickpay_mobilepay';
    public const CODE_VIPPS = 'quickpay_vipps';
    public const CODE_PAYPAL = 'quickpay_paypal';
    public const CODE_VIABILL = 'quickpay_viabill';
    public const CODE_SWISH = 'quickpay_swish';
    public const CODE_TRUSTLY = 'quickpay_trustly';
    public const CODE_ANYDAY = 'quickpay_anyday';
    public const CODE_GOOGLEPAY = 'quickpay_googlepay';

    private const XML_PATH_CARD_LOGO = 'payment/quickpay_gateway/cardlogos';
    private const XML_PATH_DESCRIPTION = 'payment/%s/description';

    /**
     * @var ScopeConfigInterface
     *
    protected  $scopeConfig;

    /**
     * @var AssetRepository
     */
    protected  $assetRepo;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * QuickPay constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param AssetRepository $assetRepo
     * @param Escaper $escaper       
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        AssetRepository $assetRepo,
        Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->assetRepo = $assetRepo;
        $this->escaper = $escaper;
    }

    /**
     * Return the description for a payment method
     */
    public function getDescription($methodCode)
    {
        return (string)$this->scopeConfig->getValue(
            sprintf(self::XML_PATH_DESCRIPTION, $methodCode),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return payment logo and description based on selected payment method
     */
    public function getPaymentConfigByMethod($methodCode)
    {
        $logo = [];
        $description = $this->getDescription($methodCode);

        switch ($methodCode) {
            case self::CODE:
                $logo = $this->getQuickPayCardLogo();
                break;

            case self::CODE_KLARNA:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/klarna.svg')];
                break;

            case self::CODE_APPLEPAY:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/apple-pay.svg')];
                break;

            case self::CODE_MOBILEPAY:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/mobilepay_payment.png')];
                break;

            case self::CODE_VIPPS:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/vipps.png')];
                break;

            case self::CODE_PAYPAL:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/paypal.svg')];
                break;

            case self::CODE_VIABILL:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/viabill.png')];
                break;

            case self::CODE_SWISH:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/swish.png')];
                break;

            case self::CODE_TRUSTLY:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/trustly.svg')];
                break;

            case self::CODE_ANYDAY:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/anydaysplit.svg')];
                break;

            case self::CODE_GOOGLEPAY:
                $logo = [$this->assetRepo->getUrl('QuickPay_Gateway::images/google-pay.svg')];
                break;
        }
        return [
            'paymentLogo' => $logo,
            'description' => $description
        ];
    }

    /**
     * Return logos for main QuickPay gateway
     */
    protected function getQuickPayCardLogo()
    {
        $cards = explode(',', (string)$this->scopeConfig->getValue(self::XML_PATH_CARD_LOGO, ScopeInterface::SCOPE_STORE));
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
