<?php

namespace Digidirect\HelloBar\Setup;

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

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $contentDesktop = <<<HTML
<div class="top-banner tcl-banner version-1-0-1">
    <section class="tcl-banner__carousel-container">
      <div class="tcl-banner__carousel_wrapper">
        <div class="tcl-banner__carousel">
          <div class="tcl-banner__slides-container" style="height: 36px;">


            <!-- Slide 1 -->
            <div class="tcl-banner__slide">
              <div class="tcl-banner__container">
                <div class="tcl-banner__heading"><a href="{{store url=''}}christmas-gift-guide">Click here</a> to discover our Christmas Gift Guide</div>
              </div>
            </div>

            <!-- Slide 2 -->
            <div class="tcl-banner__slide">
              <div class="tcl-banner__container">
                <div class="tcl-banner__heading">Don’t know what to choose? <a target="_blank" href="https://digidirectgiftcards.viisolutions.com.au/">Shop</a> Gift Cards now</div>
              </div>
            </div>

            <!-- Slide 3 -->
            <div class="tcl-banner__slide">
              <div class="tcl-banner__container">
                <div class="tcl-banner__heading">Need a passport photo? We’ve got you covered! <a href="{{store url=''}}passport-photos">Click here</a></div>
              </div>
            </div>

          </div>

          <!-- Navigation Dots -->
          <nav class="tcl-banner__tabList">
            <div class="tds-tab-list tds-tab-list--animated tds-tab-list--dots" role="tablist">
              <div class="tds--animated-backdrop"></div>
              <button aria-selected="false" class="tds-tab" id="1" role="tab" type="button"></button>
              <button aria-selected="false" class="tds-tab" id="2" role="tab" type="button"></button>
              <button aria-selected="false" class="tds-tab" id="3" role="tab" type="button"></button>
            </div>
          </nav>

        </div>
      </div>
    </section>
  </div>
HTML;

            $blockIdentifierDesktop = 'hello-bar';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Hello Bar')
                    ->setIdentifier($blockIdentifierDesktop)
                    ->setContent($contentDesktop)
                    ->setIsActive(true)
                    ->setStores([0])
                    ->save();
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {

            $contentDesktop = <<<HTML
<div class="top-banner tcl-banner version-1-0-2">
    <section class="tcl-banner__carousel-container">
      <div class="tcl-banner__carousel_wrapper">
        <div class="tcl-banner__carousel">
          <div class="tcl-banner__slides-container" style="height: 36px;">


            <!-- Slide 1 -->
            <div class="tcl-banner__slide">
              <div class="tcl-banner__container">
                <div class="tcl-banner__heading"><a href="{{store url=''}}christmas-gift-guide">Click here</a> to discover our Christmas Gift Guide</div>
              </div>
            </div>

            <!-- Slide 2 -->
            <div class="tcl-banner__slide">
              <div class="tcl-banner__container">
                <div class="tcl-banner__heading">Don’t know what to choose? <a target="_blank" href="https://digidirectgiftcards.viisolutions.com.au/">Shop</a> Gift Cards now</div>
              </div>
            </div>

            <!-- Slide 3 -->
            <div class="tcl-banner__slide">
              <div class="tcl-banner__container">
                <div class="tcl-banner__heading">Need a passport photo? We’ve got you covered! <a href="{{store url=''}}passport-photos">Click here</a></div>
              </div>
            </div>

          </div>

          <!-- Navigation Dots -->
          <nav class="tcl-banner__tabList">
            <div class="tds-tab-list tds-tab-list--animated tds-tab-list--dots" role="tablist">
              <div class="tds--animated-backdrop"></div>
              <button aria-selected="false" class="tds-tab" id="1" role="tab" type="button"></button>
              <button aria-selected="false" class="tds-tab" id="2" role="tab" type="button"></button>
              <button aria-selected="false" class="tds-tab" id="3" role="tab" type="button"></button>
            </div>
          </nav>

        </div>
      </div>
    </section>
  </div>
HTML;

            $blockIdentifierDesktop = 'hello-bar';
            $blockDesktop = $this->blockFactory->create()->load($blockIdentifierDesktop, 'identifier');

            if ($blockDesktop->getId()) {
                $blockDesktop->setContent($contentDesktop)
                    ->save();
            } else {
                $blockDesktop->setTitle('Hello Bar')
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
