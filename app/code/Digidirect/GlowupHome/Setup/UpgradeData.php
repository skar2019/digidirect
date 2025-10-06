<?php

namespace Digidirect\GlowupHome\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

class UpgradeData implements UpgradeDataInterface
{
    protected $blockFactory;

    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $content = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><span class="digi-orange-text">digi</span><span class="digi-black-text">Services.</span>Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/1.png'}}" alt="digiRent">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span>Rent</h1>
                            <a href="#" class="btn">Learn More</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/2.png'}}" alt="digiPrint">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span>Print</h1>
                            <a href="#" class="btn">Shop Now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/3.png'}}" alt="digiDeals">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span>Deals</h1>
                            <a href="#" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/4.png'}}" alt="digiSeconds">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span>Seconds</h1>
                            <a href="#" class="btn">Shop Now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/6.png'}}" alt="digiDirect Business">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Direct</span> <span class="digi-black-text small">Business</span></h1>
                            <a href="#" class="btn">Enter Now</a>
                        </div>
                        <p>Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-click-collect">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/5.png'}}" alt="Click & Collect">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1>Click & Collect</h1>
                            <a href="#" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/7.png'}}" alt="Trade Up Program">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Trade</span><span class="digi-black-text">Up</span><span class="digi-black-text small">Program</span></h1>
                            <a href="#" class="btn">Trade Now</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/9.png'}}" alt="digiProtect">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span>Protect</h1>
                            <a href="#" class="btn">Learn More</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/8.png'}}" alt="digiClub">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span>Club</h1>
                            <a href="#" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/12.png'}}" alt="Price Match">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Price</span><span class="digi-black-text">Match</span></h1>
                            <a href="#" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/11.png'}}" alt="Events">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1>Events</h1>
                            <a href="#" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/10.png'}}" alt="digiLife">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span>Life</h1>
                            <a href="#" class="btn">Join</a>
                        </div>
                        <p>A photography community built to educate people on how to use their camera equipment and master their settings!</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

HTML;

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $blockIdentifier = 'digi-services-glow-up';
            $block = $this->blockFactory->create()->load($blockIdentifier, 'identifier');

            if ($block->getId()) {
                $block->setContent($content)
                    ->save();
            }
        }

        $setup->endSetup();
    }
}
