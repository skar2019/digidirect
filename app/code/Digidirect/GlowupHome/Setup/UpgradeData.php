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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Rent</span></h1>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Print</span></h1>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Deals</span></h1>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Seconds</span></h1>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Direct</span><span class="digi-black-text small">Business</span></h1>
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
                            <h1><span class="digi-black-text">Click &amp; Collect</span></h1>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Protect</span></h1>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span></h1>
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
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <img src="{{media url='wysiwyg/glowup-digiservices/10.png'}}" alt="digiLife">
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Life</span></h1>
                            <a href="#" class="btn">Join</a>
                        </div>
                        <p>A photography community built to educate people on how to use their camera equipment and master their settings!</p>
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
                            <h1><span class="digi-black-text">Events</span></h1>
                            <a href="#" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
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

        $content = <<<HTML
<style>#html-body [data-pb-style=LXS65I0]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=HLS8RCL]{min-height:300px}#html-body [data-pb-style=K70LJQ3]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=R6SUCTP]{min-height:300px;background-color:transparent}#html-body [data-pb-style=S7PJ10M]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=BRHK2TV]{min-height:300px;background-color:transparent}#html-body [data-pb-style=SO8W1LF]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=BUR8R4M]{min-height:300px;background-color:transparent}#html-body [data-pb-style=AKAAMIM]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=F0YBAJJ]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="LXS65I0"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="HLS8RCL"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="K70LJQ3"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="R6SUCTP"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;<br>&nbsp; &nbsp; &lt;div class="digiservices-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;Do even more with digi products and services.&lt;/h2&gt;<br>&nbsp; &nbsp; &lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp;&lt;div class="digiservices-grid"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-rent"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/1.png" alt="digiRent"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Learn More&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-print"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/2.png" alt="digiPrint"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Shop Now&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-market"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/3.png" alt="digiDeals"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Explore&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt; <br>&nbsp; &nbsp; &nbsp;&lt;/div&gt;</p><p>&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="S7PJ10M"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="BRHK2TV"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;<br>&nbsp; &nbsp; &lt;div class="digiservices-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;Do even more with digi products and services.&lt;/h2&gt;<br>&nbsp; &nbsp; &lt;/div&gt;</p><p>&nbsp; &nbsp; &lt;div class="digiservices-grid"&gt;</p><p>&lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-seconds"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/4.png" alt="digiSeconds"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Shop Now&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-direct"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/6.png" alt="digiDirect Business"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Enter Now&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-click-collect"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/5.png" alt="Click &amp; Collect"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Learn More&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;</p><p>&lt;/div&gt;</p><p>&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="SO8W1LF"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="BUR8R4M"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;<br>&nbsp; &nbsp; &lt;div class="digiservices-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;Do even more with digi products and services.&lt;/h2&gt;<br>&nbsp; &nbsp; &lt;/div&gt;</p><p>&nbsp; &nbsp; &lt;div class="digiservices-grid"&gt;</p><p>&lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-trade"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/7.png" alt="Trade Up Program"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Trade Now&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-protect"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/9.png" alt="digiProtect"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Learn More&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-club"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/8.png" alt="digiClub"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Join&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;</p><p>&lt;/div&gt;</p><p>&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="AKAAMIM"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="F0YBAJJ"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;<br>&nbsp; &nbsp; &lt;div class="digiservices-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;Do even more with digi products and services.&lt;/h2&gt;<br>&nbsp; &nbsp; &lt;/div&gt;</p><p>&nbsp; &nbsp; &lt;div class="digiservices-grid"&gt;</p><p>&nbsp;&lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-price"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/12.png" alt="Price Match"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Learn More&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;</p><p>&lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-life"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/10.png" alt="digiLife"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Join&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-wrapper"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card digiservice-card-events"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digiservice-card-inner"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/11.png" alt="Events"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-body"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;div class="digicard-header"&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="#" class="btn"&gt;Explore&lt;/a&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;<br>&nbsp; &nbsp; &nbsp; &nbsp; &lt;/div&gt;</p><p>&lt;/div&gt;</p><p>&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $blockIdentifier = 'digi-services-glow-up-mobile';
            $block = $this->blockFactory->create()->load($blockIdentifier, 'identifier');

            if ($block->getId()) {
                $block->setContent($content)
                    ->save();
            } else {
                $block->setTitle('Digi Services Glow Up Mobile')
                    ->setIdentifier($blockIdentifier)
                    ->setContent($content)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }
        }

        $setup->endSetup();
    }
}
