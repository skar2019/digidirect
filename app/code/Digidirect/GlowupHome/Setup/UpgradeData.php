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
                            <a href="#" class="btn">Learn more</a>
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
                            <a href="#" class="btn">Shop now</a>
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
                            <a href="#" class="btn">Shop now</a>
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
                            <a href="#" class="btn">Enter now</a>
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
                            <a href="#" class="btn">Learn more</a>
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
                            <a href="#" class="btn">Trade now</a>
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
                            <a href="#" class="btn">Learn more</a>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span><span class="registered">®</span></h1>
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
                            <a href="#" class="btn">Learn more</a>
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

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $contentMobile = <<<HTML
<style>#html-body [data-pb-style=VSLCMAS]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=UR1IQSP]{min-height:300px}#html-body [data-pb-style=SRIVRL7]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=MJM5IKV]{min-height:300px;background-color:transparent}#html-body [data-pb-style=KE3AEF2]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=LGMXK1R]{min-height:300px;background-color:transparent}#html-body [data-pb-style=BD0FWMT]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=FRWALUD]{min-height:300px;background-color:transparent}#html-body [data-pb-style=LARFDCP]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=YGL1S99]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="VSLCMAS"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="UR1IQSP"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="SRIVRL7"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="MJM5IKV"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/1.png" alt="digiRent"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/2.png" alt="digiPrint"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Shop now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/3.png" alt="digiDeals"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="KE3AEF2"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="LGMXK1R"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/4.png" alt="digiSeconds"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Shop now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/6.png" alt="digiDirect Business"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Enter now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/5.png" alt="Click &amp; Collect"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="BD0FWMT"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="FRWALUD"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/7.png" alt="Trade Up Program"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Trade now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/9.png" alt="digiProtect"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/8.png" alt="digiClub"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="LARFDCP"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="YGL1S99"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/12.png" alt="Price Match"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/10.png" alt="digiLife"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/11.png" alt="Events"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

            $blockIdentifierMobile = 'digi-services-glow-up-mobile';
            $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

            if ($blockMobile->getId()) {
                $blockMobile->setContent($contentMobile)
                    ->save();
            } else {
                $blockMobile->setTitle('Digi Services Glow Up Mobile')
                    ->setIdentifier($blockIdentifierMobile)
                    ->setContent($contentMobile)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

            $contentDesktop = <<<HTML
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
                            <a href="#" class="btn">Learn more</a>
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
                            <a href="#" class="btn">Shop now</a>
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
                            <a href="#" class="btn">Shop now</a>
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
                            <a href="#" class="btn">Enter now</a>
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
                            <a href="#" class="btn">Learn more</a>
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
                            <a href="#" class="btn">Trade now</a>
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
                            <a href="#" class="btn">Learn more</a>
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
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span><span class="registered">®</span></h1>
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
                            <a href="#" class="btn">Learn more</a>
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

            $blockIdentifierDesktop = 'digi-services-glow-up';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Digi Services Glow Up')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

            if (version_compare($context->getVersion(), '1.0.4', '<')) {
                $contentMobile = <<<HTML
<style>#html-body [data-pb-style=DJ832MR]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=UME36IS]{min-height:300px}#html-body [data-pb-style=PMMQVV9]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=BY07F0I]{min-height:300px;background-color:transparent}#html-body [data-pb-style=P0GDCDM]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=QLR5R8A]{min-height:300px;background-color:transparent}#html-body [data-pb-style=RVX9HSB]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=RF59JCO]{min-height:300px;background-color:transparent}#html-body [data-pb-style=CUN1K0Q]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=XI60QS4]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="DJ832MR"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="UME36IS"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="PMMQVV9"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="BY07F0I"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/1.png" alt="digiRent"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/2.png" alt="digiPrint"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Shop now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/3.png" alt="digiDeals"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="P0GDCDM"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="QLR5R8A"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/4.png" alt="digiSeconds"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Shop now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/6.png" alt="digiDirect Business"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;br/&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Enter now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/5.png" alt="Click &amp; Collect"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="RVX9HSB"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="RF59JCO"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/7.png" alt="Trade Up Program"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;br/&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Trade now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/9.png" alt="digiProtect"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/8.png" alt="digiClub"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="CUN1K0Q"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="XI60QS4"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/12.png" alt="Price Match"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/10.png" alt="digiLife"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/11.png" alt="Events"&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

                $blockIdentifierMobile = 'digi-services-glow-up-mobile';
                $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

                if ($blockMobile->getId()) {
                    $blockMobile->setContent($contentMobile)
                        ->save();
                } else {
                    $blockMobile->setTitle('Digi Services Glow Up Mobile')
                        ->setIdentifier($blockIdentifierMobile)
                        ->setContent($contentMobile)
                        ->setIsActive(true)
                        ->setStores([0])
                        ->save();
                }
            }

            if (version_compare($context->getVersion(), '1.0.5', '<')) {

                $contentDesktop = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><span class="digi-orange-text">digi</span><span class="digi-black-text">Services.</span>Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digirent"><img src="{{media url='wysiwyg/glowup-digiservices/1.png'}}" alt="digiRent"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Rent</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digirent" class="btn">Learn more</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprint"><img src="{{media url='wysiwyg/glowup-digiservices/2.png'}}" alt="digiPrint"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Print</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiprint" class="btn">Shop now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}deals"><img src="{{media url='wysiwyg/glowup-digiservices/3.png'}}" alt="digiDeals"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Deals</span></h1>
                            <a href="{{config path='web/secure/base_url'}}deals" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds"><img src="{{media url='wysiwyg/glowup-digiservices/4.png'}}" alt="digiSeconds"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Seconds</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds" class="btn">Shop now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <a href="#"><img src="{{media url='wysiwyg/glowup-digiservices/6.png'}}" alt="digiDirect Business"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Direct</span><span class="digi-black-text small">Business</span></h1>
                            <a href="#" class="btn">Enter now</a>
                        </div>
                        <p>Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-click-collect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}pickup-instore"><img src="{{media url='wysiwyg/glowup-digiservices/5.png'}}" alt="Click & Collect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Click &amp; Collect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}pickup-instore" class="btn">Learn more</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up"><img src="{{media url='wysiwyg/glowup-digiservices/7.png'}}" alt="Trade Up Program"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Trade</span><span class="digi-black-text">Up</span><span class="digi-black-text small">Program</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up" class="btn">Trade now</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprotect"><img src="{{media url='wysiwyg/glowup-digiservices/9.png'}}" alt="digiProtect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Protect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiprotect" class="btn">Learn more</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiclub"><img src="{{media url='wysiwyg/glowup-digiservices/8.png'}}" alt="digiClub"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span><span class="registered">®</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiclub" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}price-match-guarantee"><img src="{{media url='wysiwyg/glowup-digiservices/12.png'}}" alt="Price Match"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Price</span><span class="digi-black-text">Match</span></h1>
                            <a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn">Learn more</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}blog"><img src="{{media url='wysiwyg/glowup-digiservices/10.png'}}" alt="digiLife"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Life</span></h1>
                            <a href="{{config path='web/secure/base_url'}}blog" class="btn">Join</a>
                        </div>
                        <p>A photography community built to educate people on how to use their camera equipment and master their settings!</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}events"><img src="{{media url='wysiwyg/glowup-digiservices/11.png'}}" alt="Events"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Events</span></h1>
                            <a href="{{config path='web/secure/base_url'}}events" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

HTML;

                $blockIdentifierDesktop = 'digi-services-glow-up';
                $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

                if ($blockDesktop->getId()) {
                    $blockDesktop->setContent($contentDesktop)
                        ->save();
                } else {
                    $blockDesktop->setTitle('Digi Services Glow Up')
                        ->setIdentifier($blockIdentifierDesktop)
                        ->setContent($contentDesktop)
                        ->setIsActive(true)
                        ->setStores([0])
                        ->save();
                }


                $contentMobile = <<<HTML
<style>#html-body [data-pb-style=CCNOH1N]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=BP3QSIR]{min-height:300px}#html-body [data-pb-style=M1W2ATY]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=XTX71W2]{min-height:300px;background-color:transparent}#html-body [data-pb-style=JVH8H5G]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=IUL4KC0]{min-height:300px;background-color:transparent}#html-body [data-pb-style=BYER3OF]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=OTVRT2I]{min-height:300px;background-color:transparent}#html-body [data-pb-style=HNU6IJP]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=GWB17G9]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="CCNOH1N"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="BP3QSIR"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="M1W2ATY"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="XTX71W2"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digirent"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/1.png" alt="digiRent"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digirent" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprint"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/2.png" alt="digiPrint"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprint" class="btn"&gt;Shop now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}deals"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/3.png" alt="digiDeals"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}deals" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="JVH8H5G"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="IUL4KC0"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/4.png" alt="digiSeconds"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds" class="btn"&gt;Shop now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="#"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/6.png" alt="digiDirect Business"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;br/&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Enter now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}pickup-instore"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/5.png" alt="Click &amp; Collect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}pickup-instore" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="BYER3OF"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="OTVRT2I"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds-trade-up"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/7.png" alt="Trade Up Program"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;br/&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds-trade-up" class="btn"&gt;Trade now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/9.png" alt="digiProtect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiclub"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/8.png" alt="digiClub"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiclub" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="HNU6IJP"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="GWB17G9"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match-guarantee"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/12.png" alt="Price Match"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn"&gt;Learn more&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}blog"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/10.png" alt="digiLife"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}blog" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}events"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/11.png" alt="Events"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}events" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

                $blockIdentifierMobile = 'digi-services-glow-up-mobile';
                $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

                if ($blockMobile->getId()) {
                    $blockMobile->setContent($contentMobile)
                        ->save();
                } else {
                    $blockMobile->setTitle('Digi Services Glow Up Mobile')
                        ->setIdentifier($blockIdentifierMobile)
                        ->setContent($contentMobile)
                        ->setIsActive(true)
                        ->setStores([0])
                        ->save();
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {

            $contentDesktop = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><a href="#digiservices"><span class="digi-orange-text">digi</span><span class="digi-black-text">Services.</span></a>Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digirent"><img src="{{media url='wysiwyg/glowup-digiservices/1.png'}}" alt="digiRent"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Rent</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digirent" class="btn">Learn More</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprint"><img src="{{media url='wysiwyg/glowup-digiservices/2.png'}}" alt="digiPrint"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Print</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiprint" class="btn">Shop Now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}deals"><img src="{{media url='wysiwyg/glowup-digiservices/3.png'}}" alt="digiDeals"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Deals</span></h1>
                            <a href="{{config path='web/secure/base_url'}}deals" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds"><img src="{{media url='wysiwyg/glowup-digiservices/4.png'}}" alt="digiSeconds"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Seconds</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds" class="btn">Shop Now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <a href="#"><img src="{{media url='wysiwyg/glowup-digiservices/6.png'}}" alt="digiDirect Business"></a>
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
                    <a href="{{config path='web/secure/base_url'}}pickup-instore"><img src="{{media url='wysiwyg/glowup-digiservices/5.png'}}" alt="Click & Collect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Click &amp; Collect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}pickup-instore" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprotect"><img src="{{media url='wysiwyg/glowup-digiservices/9.png'}}" alt="digiProtect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Protect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiprotect" class="btn">Learn More</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up"><img src="{{media url='wysiwyg/glowup-digiservices/7.png'}}" alt="Trade Up Program"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Trade</span><span class="digi-black-text">Up</span><span class="digi-black-text small">Program</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up" class="btn">Trade Now</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiclub"><img src="{{media url='wysiwyg/glowup-digiservices/8.png'}}" alt="digiClub"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span><span class="registered">®</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiclub" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

 	<div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}price-match-guarantee"><img src="{{media url='wysiwyg/glowup-digiservices/12.png'}}" alt="Price Match"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Price</span><span class="digi-black-text">Match</span></h1>
                            <a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}events"><img src="{{media url='wysiwyg/glowup-digiservices/11.png'}}" alt="Events"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Events</span></h1>
                            <a href="{{config path='web/secure/base_url'}}events" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>

       <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}blog"><img src="{{media url='wysiwyg/glowup-digiservices/10.png'}}" alt="digiLife"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Life</span></h1>
                            <a href="{{config path='web/secure/base_url'}}blog" class="btn">Join</a>
                        </div>
                        <p>A photography community built to educate people on how to use their camera equipment and master their settings!</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
HTML;

            $blockIdentifierDesktop = 'digi-services-glow-up';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Digi Services Glow Up')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $contentMobile = <<<HTML
<style>#html-body [data-pb-style=XKS04TV]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=RIJ928Q]{min-height:300px}#html-body [data-pb-style=DG5TYIA]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=VGDPBWH]{min-height:300px;background-color:transparent}#html-body [data-pb-style=AD2XIYR]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=G9B3R3G]{min-height:300px;background-color:transparent}#html-body [data-pb-style=JE770G6]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=RE38N5U]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="XKS04TV"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="RIJ928Q"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="DG5TYIA"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="VGDPBWH"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digirent"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/1.png" alt="digiRent"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digirent" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/4.png" alt="digiSeconds"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds-trade-up"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/7.png" alt="Trade Up Program"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds-trade-up" class="btn"&gt;Trade Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}events"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/11.png" alt="Events"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}events" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="AD2XIYR"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="G9B3R3G"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprint"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/2.png" alt="digiPrint"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprint" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}pickup-instore"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/5.png" alt="Click &amp; Collect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}pickup-instore" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiclub"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/8.png" alt="digiClub"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiclub" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}blog"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/10.png" alt="digiLife"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}blog" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="JE770G6"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="RE38N5U"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}deals"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/3.png" alt="digiDeals"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}deals" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="#"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/6.png" alt="digiDirect Business"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Enter Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/9.png" alt="digiProtect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match-guarantee"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/12.png" alt="Price Match"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

            $blockIdentifierMobile = 'digi-services-glow-up-mobile';
            $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

            if ($blockMobile->getId()) {
                $blockMobile->setContent($contentMobile)
                    ->save();
            } else {
                $blockMobile->setTitle('Digi Services Glow Up Mobile')
                    ->setIdentifier($blockIdentifierMobile)
                    ->setContent($contentMobile)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }
        }

        if (version_compare($context->getVersion(), '1.0.9', '<')) {

            $contentDesktop = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><a href="#digiservices"><span class="digi-orange-text">digi</span><span class="digi-black-text">Services.</span></a>Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digirent"><img src="{{media url='wysiwyg/glowup-digiservices/1.png'}}" alt="digiRent"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Rent</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digirent" class="btn">Learn More</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digi-print"><img src="{{media url='wysiwyg/glowup-digiservices/2.png'}}" alt="digiPrint"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Print</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digi-print" class="btn">Shop Now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}deals"><img src="{{media url='wysiwyg/glowup-digiservices/3.png'}}" alt="digiDeals"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Deals</span></h1>
                            <a href="{{config path='web/secure/base_url'}}deals" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds"><img src="{{media url='wysiwyg/glowup-digiservices/4.png'}}" alt="digiSeconds"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Seconds</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds" class="btn">Shop Now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}b2b"><img src="{{media url='wysiwyg/glowup-digiservices/6.png'}}" alt="digiDirect Business"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Direct</span><span class="digi-black-text small">Business</span></h1>
                            <a href="{{config path='web/secure/base_url'}}b2b" class="btn">Enter Now</a>
                        </div>
                        <p>Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-click-collect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}pickup-instore"><img src="{{media url='wysiwyg/glowup-digiservices/5.png'}}" alt="Click & Collect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Click &amp; Collect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}pickup-instore" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprotect"><img src="{{media url='wysiwyg/glowup-digiservices/9.png'}}" alt="digiProtect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Protect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiprotect" class="btn">Learn More</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up"><img src="{{media url='wysiwyg/glowup-digiservices/7.png'}}" alt="Trade Up Program"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Trade</span><span class="digi-black-text">Up</span><span class="digi-black-text small">Program</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up" class="btn">Trade Now</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiclub"><img src="{{media url='wysiwyg/glowup-digiservices/8.png'}}" alt="digiClub"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span><span class="registered">®</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiclub" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

 	<div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}price-match-guarantee"><img src="{{media url='wysiwyg/glowup-digiservices/12.png'}}" alt="Price Match"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Price</span><span class="digi-black-text">Match</span></h1>
                            <a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}events"><img src="{{media url='wysiwyg/glowup-digiservices/11.png'}}" alt="Events"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Events</span></h1>
                            <a href="{{config path='web/secure/base_url'}}events" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>

       <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}blog"><img src="{{media url='wysiwyg/glowup-digiservices/10.png'}}" alt="digiLife"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Life</span></h1>
                            <a href="{{config path='web/secure/base_url'}}blog" class="btn">Join</a>
                        </div>
                        <p>A photography community built to educate people on how to use their camera equipment and master their settings!</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
HTML;

            $blockIdentifierDesktop = 'digi-services-glow-up';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Digi Services Glow Up')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

            $contentMobile = <<<HTML
<style>#html-body [data-pb-style=X9E4RXQ]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=JVNYEKD]{min-height:300px}#html-body [data-pb-style=HH5X9M9]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=EXJXE40]{min-height:300px;background-color:transparent}#html-body [data-pb-style=I9KM0C6]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=UJHDLL3]{min-height:300px;background-color:transparent}#html-body [data-pb-style=JGO6H06]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=WMCKI7G]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="X9E4RXQ"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="JVNYEKD"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="HH5X9M9"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="EXJXE40"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digirent"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/1.png" alt="digiRent"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digirent" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/4.png" alt="digiSeconds"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds-trade-up"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/7.png" alt="Trade Up Program"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiseconds-trade-up" class="btn"&gt;Trade Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}events"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/11.png" alt="Events"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}events" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="I9KM0C6"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="UJHDLL3"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digi-print"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/2.png" alt="digiPrint"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digi-print" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}pickup-instore"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/5.png" alt="Click &amp; Collect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}pickup-instore" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiclub"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/8.png" alt="digiClub"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiclub" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}blog"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/10.png" alt="digiLife"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}blog" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="JGO6H06"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="WMCKI7G"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}deals"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/3.png" alt="digiDeals"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}deals" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="https://www.digidirect.com.au/b2b"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/6.png" alt="digiDirect Business"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="https://www.digidirect.com.au/b2b" class="btn"&gt;Enter Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/9.png" alt="digiProtect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match-guarantee"&gt;&lt;img src="https://www.digidirect.com.au/media/wysiwyg/glowup-digiservices/12.png" alt="Price Match"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

            $blockIdentifierMobile = 'digi-services-glow-up-mobile';
            $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

            if ($blockMobile->getId()) {
                $blockMobile->setContent($contentMobile)
                    ->save();
            } else {
                $blockMobile->setTitle('Digi Services Glow Up Mobile')
                    ->setIdentifier($blockIdentifierMobile)
                    ->setContent($contentMobile)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

        }


        if (version_compare($context->getVersion(), '1.0.10', '<')) {

            $contentDesktop = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><a href="#digiservices"><span class="digi-orange-text">digi</span><span class="digi-black-text">Services.</span></a>Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digirent"><img src="{{media url='wysiwyg/glowup-digiservices/digi-rent.png'}}" alt="digiRent"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Rent</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digirent" class="btn">Learn More</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digi-print"><img src="{{media url='wysiwyg/glowup-digiservices/digi-print.png'}}" alt="digiPrint"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Print</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digi-print" class="btn">Shop Now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}deals"><img src="{{media url='wysiwyg/glowup-digiservices/digi-deals.png'}}" alt="digiDeals"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Deals</span></h1>
                            <a href="{{config path='web/secure/base_url'}}deals" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds"><img src="{{media url='wysiwyg/glowup-digiservices/digi-seconds.png'}}" alt="digiSeconds"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Seconds</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds" class="btn">Shop Now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}b2b"><img src="{{media url='wysiwyg/glowup-digiservices/digidirect-business.png'}}" alt="digiDirect Business"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Direct</span><span class="digi-black-text small">Business</span></h1>
                            <a href="{{config path='web/secure/base_url'}}b2b" class="btn">Enter Now</a>
                        </div>
                        <p>Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-click-collect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}pickup-instore"><img src="{{media url='wysiwyg/glowup-digiservices/click-and-collect.png'}}" alt="Click & Collect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Click &amp; Collect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}pickup-instore" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprotect"><img src="{{media url='wysiwyg/glowup-digiservices/digi-protect.png'}}" alt="digiProtect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Protect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiprotect" class="btn">Learn More</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up"><img src="{{media url='wysiwyg/glowup-digiservices/trade-up-program.png'}}" alt="Trade Up Program"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Trade</span><span class="digi-black-text">Up</span><span class="digi-black-text small">Program</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds-trade-up" class="btn">Trade Now</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiclub"><img src="{{media url='wysiwyg/glowup-digiservices/digi-club.png'}}" alt="digiClub"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span><span class="registered">®</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiclub" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

 	<div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}price-match-guarantee"><img src="{{media url='wysiwyg/glowup-digiservices/price-match.png'}}" alt="Price Match"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Price</span><span class="digi-black-text">Match</span></h1>
                            <a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}events"><img src="{{media url='wysiwyg/glowup-digiservices/digi-events.png'}}" alt="Events"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Events</span></h1>
                            <a href="{{config path='web/secure/base_url'}}events" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>

       <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}blog"><img src="{{media url='wysiwyg/glowup-digiservices/digi-life.png'}}" alt="digiLife"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Life</span></h1>
                            <a href="{{config path='web/secure/base_url'}}blog" class="btn">Join</a>
                        </div>
                        <p>A photography community built to educate people on how to use their camera equipment and master their settings!</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
HTML;

            $blockIdentifierDesktop = 'digi-services-glow-up';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Digi Services Glow Up')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

            $contentMobile = <<<HTML
<style>#html-body [data-pb-style=CYS97XT]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=W9QIQ6X]{min-height:300px}#html-body [data-pb-style=OUJUQYJ]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=XD1FTOG]{min-height:300px;background-color:transparent}#html-body [data-pb-style=DX8PV5X]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=YGP1J20]{min-height:300px;background-color:transparent}#html-body [data-pb-style=T4WSE2J]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=FP8MU9N]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="CYS97XT"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="W9QIQ6X"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="OUJUQYJ"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="XD1FTOG"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digirent"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-rent.png" alt="digiRent"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digirent" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiseconds"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-seconds.png" alt="digiSeconds"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiseconds" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiseconds-trade-up"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/trade-up-program.png" alt="Trade Up Program"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiseconds-trade-up" class="btn"&gt;Trade Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}events"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-events.png" alt="Events"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}events" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="DX8PV5X"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="YGP1J20"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digi-print"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-print.png" alt="digiPrint"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiprint" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}pickup-instore"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/click-and-collect.png" alt="Click &amp; Collect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}pickup-instore" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiclub"&gt;&lt;img src="{{store url=''}}/media/wysiwyg/glowup-digiservices/digi-club.png" alt="digiClub"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiclub" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}blog"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-life.png" alt="digiLife"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}blog" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="T4WSE2J"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="FP8MU9N"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}deals"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-deals.png" alt="digiDeals"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}deals" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}b2b"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digidirect-business.png" alt="digiDirect Business"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}b2b" class="btn"&gt;Enter Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiprotect"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-protect.png" alt="digiProtect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}price-match-guarantee"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/price-match.png" alt="Price Match"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match-guarantee" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

            $blockIdentifierMobile = 'digi-services-glow-up-mobile';
            $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

            if ($blockMobile->getId()) {
                $blockMobile->setContent($contentMobile)
                    ->save();
            } else {
                $blockMobile->setTitle('Digi Services Glow Up Mobile')
                    ->setIdentifier($blockIdentifierMobile)
                    ->setContent($contentMobile)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

        }

        if (version_compare($context->getVersion(), '1.0.11', '<')) {

            $contentDesktop = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><a href="#digiservices"><span class="digi-orange-text">digi</span><span class="digi-black-text">Services.</span></a>Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digirent"><img src="{{media url='wysiwyg/glowup-digiservices/digi-rent.png'}}" alt="digiRent"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Rent</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digirent" class="btn">Learn More</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digi-print"><img src="{{media url='wysiwyg/glowup-digiservices/digi-print.png'}}" alt="digiPrint"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Print</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digi-print" class="btn">Shop Now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}deals"><img src="{{media url='wysiwyg/glowup-digiservices/digi-deals.png'}}" alt="digiDeals"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Deals</span></h1>
                            <a href="{{config path='web/secure/base_url'}}deals" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds"><img src="{{media url='wysiwyg/glowup-digiservices/digi-seconds.png'}}" alt="digiSeconds"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Seconds</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds" class="btn">Shop Now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}b2b"><img src="{{media url='wysiwyg/glowup-digiservices/digidirect-business.png'}}" alt="digiDirect Business"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Direct</span><span class="digi-black-text small">Business</span></h1>
                            <a href="{{config path='web/secure/base_url'}}b2b" class="btn">Enter Now</a>
                        </div>
                        <p>Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-click-collect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}click-and-collect"><img src="{{media url='wysiwyg/glowup-digiservices/click-and-collect.png'}}" alt="Click & Collect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Click &amp; Collect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}click-and-collect" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprotect"><img src="{{media url='wysiwyg/glowup-digiservices/digiprotects.png'}}" alt="digiProtect"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Protect</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiprotect" class="btn">Learn More</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}trade-up"><img src="{{media url='wysiwyg/glowup-digiservices/trade-up-program.png'}}" alt="Trade Up Program"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Trade</span><span class="digi-black-text">Up</span><span class="digi-black-text small">Program</span></h1>
                            <a href="{{config path='web/secure/base_url'}}trade-up" class="btn">Trade Now</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiclub"><img src="{{media url='wysiwyg/glowup-digiservices/digi-club.png'}}" alt="digiClub"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">digi</span><span class="digi-black-text">Club</span><span class="registered">®</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiclub" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

 	<div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}price-match"><img src="{{media url='wysiwyg/glowup-digiservices/price-match.png'}}" alt="Price Match"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-orange-text">Price</span><span class="digi-black-text">Match</span></h1>
                            <a href="{{config path='web/secure/base_url'}}price-match" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}events"><img src="{{media url='wysiwyg/glowup-digiservices/digi-events.png'}}" alt="Events"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1><span class="digi-black-text">Events</span></h1>
                            <a href="{{config path='web/secure/base_url'}}events" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>

       <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <a href="#"><img src="{{media url='wysiwyg/glowup-digiservices/digi-life.png'}}" alt="digiLife"></a>
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

    </div>
</section>
HTML;

            $blockIdentifierDesktop = 'digi-services-glow-up';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Digi Services Glow Up')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

            $contentMobile = <<<HTML
<style>#html-body [data-pb-style=CYS97XT]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=W9QIQ6X]{min-height:300px}#html-body [data-pb-style=OUJUQYJ]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=XD1FTOG]{min-height:300px;background-color:transparent}#html-body [data-pb-style=DX8PV5X]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=YGP1J20]{min-height:300px;background-color:transparent}#html-body [data-pb-style=T4WSE2J]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=FP8MU9N]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="CYS97XT"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="W9QIQ6X"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="OUJUQYJ"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="XD1FTOG"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digirent"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-rent.png" alt="digiRent"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Rent&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digirent" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiseconds"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-seconds.png" alt="digiSeconds"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Seconds&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiseconds" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}trade-up"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/trade-up-program.png" alt="Trade Up Program"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Trade&lt;/span&gt;&lt;span class="digi-black-text"&gt;Up&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}trade-up" class="btn"&gt;Trade Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}events"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-events.png" alt="Events"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}events" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="DX8PV5X"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="YGP1J20"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digi-print"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-print.png" alt="digiPrint"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Print&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiprint" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}click-and-collect"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/click-and-collect.png" alt="Click &amp; Collect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}click-and-collect" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiclub"&gt;&lt;img src="{{store url=''}}/media/wysiwyg/glowup-digiservices/digi-club.png" alt="digiClub"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Club&lt;/span&gt;&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiclub" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="#"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-life.png" alt="digiLife"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Life&lt;/span&gt;&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="T4WSE2J"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="FP8MU9N"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}deals"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-deals.png" alt="digiDeals"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Deals&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}deals" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}b2b"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digidirect-business.png" alt="digiDirect Business"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Direct&lt;/span&gt;&lt;span class="digi-black-text small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}b2b" class="btn"&gt;Enter Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiprotect"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digiprotects.png" alt="digiProtect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;digi&lt;/span&gt;&lt;span class="digi-black-text"&gt;Protect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}price-match"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/price-match.png" alt="Price Match"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-orange-text"&gt;Price&lt;/span&gt;&lt;span class="digi-black-text"&gt;Match&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

            $blockIdentifierMobile = 'digi-services-glow-up-mobile';
            $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

            if ($blockMobile->getId()) {
                $blockMobile->setContent($contentMobile)
                    ->save();
            } else {
                $blockMobile->setTitle('Digi Services Glow Up Mobile')
                    ->setIdentifier($blockIdentifierMobile)
                    ->setContent($contentMobile)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }

        }

        if (version_compare($context->getVersion(), '1.0.12', '<')) {

            $contentDesktop = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><a href="#digiservices"><span class="digi-title" data-highlight="digi">Services.</span></a> Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digirent"><img src="{{media url='wysiwyg/glowup-digiservices/digi-rent.png'}}" alt="digiRent" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Rent</h1>
                            <a href="{{config path='web/secure/base_url'}}digirent" class="btn">Learn More</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digi-print"><img src="{{media url='wysiwyg/glowup-digiservices/digi-print.png'}}" alt="digiPrint" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Print</h1>
                            <a href="{{config path='web/secure/base_url'}}digi-print" class="btn">Shop Now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}deals"><img src="{{media url='wysiwyg/glowup-digiservices/digi-deals.png'}}" alt="digiDeals" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Deals</h1>
                            <a href="{{config path='web/secure/base_url'}}deals" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds"><img src="{{media url='wysiwyg/glowup-digiservices/digi-seconds.png'}}" alt="digiSeconds" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Seconds</h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds" class="btn">Shop Now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}b2b"><img src="{{media url='wysiwyg/glowup-digiservices/digidirect-business.png'}}" alt="digiDirect Business" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Direct<span class="small">Business</span></h1>
                            <a href="{{config path='web/secure/base_url'}}b2b" class="btn">Enter Now</a>
                        </div>
                        <p>Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-click-collect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}click-and-collect"><img src="{{media url='wysiwyg/glowup-digiservices/click-and-collect.png'}}" alt="Click & Collect" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title">Click &amp; Collect</h1>
                            <a href="{{config path='web/secure/base_url'}}click-and-collect" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprotect"><img src="{{media url='wysiwyg/glowup-digiservices/digiprotects.png'}}" alt="digiProtect" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Protect</h1>
                            <a href="{{config path='web/secure/base_url'}}digiprotect" class="btn">Learn More</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}trade-up"><img src="{{media url='wysiwyg/glowup-digiservices/trade-up-program.png'}}" alt="Trade Up Program" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="Trade">Up<span class="small">Program</span></h1>
                            <a href="{{config path='web/secure/base_url'}}trade-up" class="btn">Trade Now</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiclub"><img src="{{media url='wysiwyg/glowup-digiservices/digi-club.png'}}" alt="digiClub" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Club<span class="registered">®</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiclub" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}price-match"><img src="{{media url='wysiwyg/glowup-digiservices/price-match.png'}}" alt="Price Match" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="Price">Match</h1>
                            <a href="{{config path='web/secure/base_url'}}price-match" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}events"><img src="{{media url='wysiwyg/glowup-digiservices/digi-events.png'}}" alt="Events" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title">Events</h1>
                            <a href="{{config path='web/secure/base_url'}}events" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <a href="#"><img src="{{media url='wysiwyg/glowup-digiservices/digi-life.png'}}" alt="digiLife" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Life</h1>
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

            $blockIdentifierDesktop = 'digi-services-glow-up';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Digi Services Glow Up')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }


            $contentMobile = <<<HTML
<style>#html-body [data-pb-style=FT9ENG8]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=DOH7UHX]{min-height:300px}#html-body [data-pb-style=LBO3HHY]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=LYO38HO]{min-height:300px;background-color:transparent}#html-body [data-pb-style=YIYW386]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=W5L7GCN]{min-height:300px;background-color:transparent}#html-body [data-pb-style=UIQ8T6P]{background-position:left top;background-size:cover;background-repeat:no-repeat;min-height:300px}#html-body [data-pb-style=KFF2P59]{min-height:300px;background-color:transparent}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="FT9ENG8"><div class="pagebuilder-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-element="main" data-pb-style="DOH7UHX"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="LBO3HHY"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="LYO38HO"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-title" data-highlight="digi"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-rent"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digirent"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-rent-img.png" alt="digiRent"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Rent&lt;/h1&gt;&lt;a href="{{store url=''}}digirent" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Your ultimate flexible rental solution.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-seconds"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiseconds"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-seconds-img.png" alt="digiSeconds"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Seconds&lt;/h1&gt;&lt;a href="{{store url=''}}digiseconds" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Save money on pre-loved, open-box, and refurbished gear.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-trade"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}trade-up"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/trade-up-program-img.png" alt="Trade Up Program"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="Trade"&gt;Up&lt;span class="small"&gt;Program&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}trade-up" class="btn"&gt;Trade Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-events"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}events"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-events-img.png" alt="Events"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Events&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}events" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Join exclusive photography events, workshops, and in-store sessions across Australia.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="YIYW386"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="W5L7GCN"><div class="pagebuilder-poster-content"><div data-element="content"><p id="XUQG0LM">&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-title" data-highlight="digi"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-print"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digi-print"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-print-img.png" alt="digiPrint"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Print&lt;/h1&gt;&lt;a href="{{store url=''}}digiprint" class="btn"&gt;Shop Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A world of options for printing &amp; preserving your photographs.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-click-collect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}click-and-collect"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/click-and-collect-img.png" alt="Click &amp; Collect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1&gt;&lt;span class="digi-black-text"&gt;Click &amp;amp; Collect&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}click-and-collect" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect offers a Click &amp; Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-club"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiclub"&gt;&lt;img src="{{store url=''}}/media/wysiwyg/glowup-digiservices/digi-club-img.png" alt="digiClub"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Club&lt;span class="registered"&gt;®&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}digiclub" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;And unlock ultimate benefits.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-life"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="#"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-life-img.png" alt="digiLife"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Life&lt;/h1&gt;&lt;a href="#" class="btn"&gt;Join&lt;/a&gt;&lt;/div&gt;&lt;p&gt;A photography community built to educate people on how to use their camera equipment and master their settings!&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" data-pb-style="UIQ8T6P"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" aria-label="" title="" data-element="overlay" data-pb-style="KFF2P59"><div class="pagebuilder-poster-content"><div data-element="content"><p>&lt;section class="digiservices-section-mobile"&gt;&lt;div class="digiservices-header"&gt;&lt;h2&gt;&lt;a href="#digiservices"&gt;&lt;span class="digi-title" data-highlight="digi"&gt;Services.&lt;/span&gt;&lt;/a&gt;&lt;br/&gt;Do even more with digi products and services.&lt;/h2&gt;&lt;/div&gt;&lt;div class="digiservices-grid"&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-market"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}deals"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digi-deals-img.png" alt="digiDeals"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Deals&lt;/h1&gt;&lt;a href="{{store url=''}}deals" class="btn"&gt;Explore&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Endless aisles of products &amp; categories.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;<br>&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-direct"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}b2b"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digidirect-business-img.png" alt="digiDirect Business"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Direct&lt;span class="small"&gt;Business&lt;/span&gt;&lt;/h1&gt;&lt;a href="{{store url=''}}b2b" class="btn"&gt;Enter Now&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-protect"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}digiprotect"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/digiprotects-img.png" alt="digiProtect"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="digi"&gt;Protect&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}digiprotect" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;Will add up to 3 years beyond the manufacturer's warranty.&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="digiservice-wrapper"&gt;&lt;div class="digiservice-card digiservice-card-price"&gt;&lt;div class="digiservice-card-inner"&gt;&lt;a href="{{store url=''}}price-match"&gt;&lt;img src="{{store url=''}}media/wysiwyg/glowup-digiservices/price-match-img.png" alt="Price Match"&gt;&lt;/a&gt;&lt;div class="digicard-body"&gt;&lt;div class="digicard-header"&gt;&lt;h1 class="digi-title" data-highlight="Price"&gt;Match&lt;/h1&gt;&lt;a href="{{config path='web/secure/base_url'}}price-match" class="btn"&gt;Learn More&lt;/a&gt;&lt;/div&gt;&lt;p&gt;digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;</p></div></div></div></div></div></div></div></div></div>
HTML;

            $blockIdentifierMobile = 'digi-services-glow-up-mobile';
            $blockMobile = $this->blockFactory->create()->load($blockIdentifierMobile, 'identifier');

            if ($blockMobile->getId()) {
                $blockMobile->setContent($contentMobile)
                    ->save();
            } else {
                $blockMobile->setTitle('Digi Services Glow Up Mobile')
                    ->setIdentifier($blockIdentifierMobile)
                    ->setContent($contentMobile)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }
        }

        if (version_compare($context->getVersion(), '1.0.13', '<')) {

            $contentDesktop = <<<HTML
<section class="digiservices-section">
    <div class="digiservices-header">
        <h2><a href="#digiservices"><span class="digi-title" data-highlight="digi">Services.</span></a> Do even more with digi products and services.</h2>
    </div>

    <div class="digiservices-grid">
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-rent">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digirent"><img src="{{media url='wysiwyg/glowup-digiservices/digi-rent.png'}}" alt="digiRent" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Rent</h1>
                            <a href="{{config path='web/secure/base_url'}}digirent" class="btn">Learn More</a>
                        </div>
                        <p>Your ultimate flexible rental solution.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-print">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digi-print"><img src="{{media url='wysiwyg/glowup-digiservices/digi-print.png'}}" alt="digiPrint" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Print</h1>
                            <a href="{{config path='web/secure/base_url'}}digi-print" class="btn">Shop Now</a>
                        </div>
                        <p>A world of options for printing & preserving your photographs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-market">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}deals"><img src="{{media url='wysiwyg/glowup-digiservices/digi-deals.png'}}" alt="digiDeals" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Deals</h1>
                            <a href="{{config path='web/secure/base_url'}}deals" class="btn">Explore</a>
                        </div>
                        <p>Endless aisles of products & categories.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-seconds">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiseconds"><img src="{{media url='wysiwyg/glowup-digiservices/digi-seconds.png'}}" alt="digiSeconds" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Seconds</h1>
                            <a href="{{config path='web/secure/base_url'}}digiseconds" class="btn">Shop Now</a>
                        </div>
                        <p>Save money on pre-loved, open-box, and refurbished gear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-direct">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}b2b"><img src="{{media url='wysiwyg/glowup-digiservices/digidirect-business.png'}}" alt="digiDirect Business" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Direct<span class="small">Business</span></h1>
                            <a href="{{config path='web/secure/base_url'}}b2b" class="btn">Enter Now</a>
                        </div>
                        <p>Specially designed to meet each customer's needs as our team goes beyond a one-size-fits-all approach.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-click-collect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}click-and-collect"><img src="{{media url='wysiwyg/glowup-digiservices/click-and-collect.png'}}" alt="Click & Collect" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title">Click &amp; Collect</h1>
                            <a href="{{config path='web/secure/base_url'}}click-and-collect" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect offers a Click & Collect service which allows you to shop and pay for your order online then pick it up at a time and place that may be more convenient to you.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-protect">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiprotect"><img src="{{media url='wysiwyg/glowup-digiservices/digiprotects.png'}}" alt="digiProtect" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Protect</h1>
                            <a href="{{config path='web/secure/base_url'}}digiprotect" class="btn">Learn More</a>
                        </div>
                        <p>Will add up to 3 years beyond the manufacturer's warranty.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-trade">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}trade-up"><img src="{{media url='wysiwyg/glowup-digiservices/trade-up-program.png'}}" alt="Trade Up Program" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="Trade">Up<span class="small">Program</span></h1>
                            <a href="{{config path='web/secure/base_url'}}trade-up" class="btn">Trade Now</a>
                        </div>
                        <p>Are you looking to upgrade your tech equipment and take your creative skills to the next level? Look no further than digiDirect's Trade-Up Program!.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-club">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}digiclub"><img src="{{media url='wysiwyg/glowup-digiservices/digi-club.png'}}" alt="digiClub" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Club<span class="registered">®</span></h1>
                            <a href="{{config path='web/secure/base_url'}}digiclub" class="btn">Join</a>
                        </div>
                        <p>And unlock ultimate benefits.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-price">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}price-match"><img src="{{media url='wysiwyg/glowup-digiservices/price-match.png'}}" alt="Price Match" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="Price">Match</h1>
                            <a href="{{config path='web/secure/base_url'}}price-match" class="btn">Learn More</a>
                        </div>
                        <p>digiDirect will price match Authorised Australian competitors which include both physical stores and online retailers..</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-events">
                <div class="digiservice-card-inner">
                    <a href="{{config path='web/secure/base_url'}}events"><img src="{{media url='wysiwyg/glowup-digiservices/digi-events.png'}}" alt="Events" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title">Events</h1>
                            <a href="{{config path='web/secure/base_url'}}events" class="btn">Explore</a>
                        </div>
                        <p>Join exclusive photography events, workshops, and in-store sessions across Australia.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="digiservice-wrapper">
            <div class="digiservice-card digiservice-card-life">
                <div class="digiservice-card-inner">
                    <a href="#"><img src="{{media url='wysiwyg/glowup-digiservices/digi-life.png'}}" alt="digiLife" loading="lazy"></a>
                    <div class="digicard-body">
                        <div class="digicard-header">
                            <h1 class="digi-title" data-highlight="digi">Life</h1>
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

            $blockIdentifierDesktop = 'digi-services-glow-up';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Digi Services Glow Up')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }
        }

        $setup->endSetup();
    }
}
