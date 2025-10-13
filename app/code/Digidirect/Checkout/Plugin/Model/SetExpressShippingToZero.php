<?php
namespace Digidirect\Checkout\Plugin\Model;

use Magento\Quote\Model\Quote\Item;
use Magento\Shipping\Model\Shipping;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class SetExpressShippingToZero
{
    protected array $targetSkus = [
        '164929','129575','120156','109388','120365','134594','134405','108475','146709','155256',
        '133565','136415','164930','145469','108753','108764','159530','171344','123530','125138',
        '169749','169748','133563','109391','161484','139189','129576','124146','123528','108759',
        '171120','129707','119367','145899','136413','139156','172478','146710','133566','108760',
        '171345','108756','125137','126554','119366','122533','137577','132656','138600','108763',
        '126556','124437','153688','163296','168420','162423','139190','132658','150598','137663',
        '139191','119365','108761','138422','163669','155748','129708','166742','148841','155751',
        '172479','136414','146093','158108','123844','123422','155747','149837','123421','139192',
        '125733','128315','108768','139188','142716','139303','149031','139305','149030','139304',
        '132657','108765','132515','163670','146094','146092','135537','141197','151036','141200',
        '171118','137552','148836','145400','141199','141198','152677','108762','152676','119362',
        '119364','130444','133096','135079','108758','166520','108769','161423','161421','132625',
        '145065','171117','127909','150597','160069','133095','139193','158888','122534','161422',
        '161424','167286','125732','139613','129547','153205','171119','122254','138642','119363',
        '148837','150941','123253','126738','172627','122255','153687','130999','153686','128351',
        '170027','132191','145468','155592','123420','139182','118491','126737','162783','148816',
        '151631','151632','153204','126553','155102','155103','155105','142517','148815','172571',
        '150812','142686','145316','172572','167287','139143','155104','139401','136111','123928',
        '136339','165683','169914','155591','169915','149246','162635','133394','168876','170068',
        '162634','171314','138765','141846','138479','156566','156720','165682','150811','127908',
        '145315','132192'
    ];

    protected $logger;
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function aroundCollectRates(
        Shipping $subject,
        \Closure $proceed,
        ...$args
    ) {
        // Run original collectRates() first
        $result = $proceed(...$args);

        try {
            $request = $args[0] ?? null;
            if (!$request || !$request->getAllItems()) {
                return $result;
            }

            $items = $request->getAllItems();
            $hasTargetSku = false;

            foreach ($items as $item) {
                if (!$item instanceof Item) {
                    continue;
                }
                $sku = $item->getSku();
                if (in_array($sku, $this->targetSkus, true)) {
                    $hasTargetSku = true;
                    break;
                }
                $this->logger->info("hasTargetSku, " . $hasTargetSku);
            }

            if ($hasTargetSku && $result && method_exists($result, 'getAllRates')) {
                foreach ($result->getAllRates() as $rate) {
                    $this->logger->info("rate->getMethod(), " . $rate->getMethod());
                    if ($rate instanceof Method && $rate->getMethod() === 'express') {
                        $rate->setPrice(0);
                        $rate->setCost(0);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Log but don't break checkout
            error_log('SetExpressShippingToZero error: ' . $e->getMessage());
        }

        return $result;
    }
}
