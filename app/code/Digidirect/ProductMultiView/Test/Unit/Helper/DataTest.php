<?php
namespace Digidirect\ProductMultiView\Test\Unit\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * Data helper
     * @var \Digidirect\ProductMultiView\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Scope config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Image Builder
     * @var \Digidirect\ProductMultiView\Block\Product\ImageBuilder
     */
    protected $_imageBuilder;

    /**
     * Product
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Set up required common objects
     * @return void
     */
    public function setUp()
    {
        $this->_scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_imageBuilder = $this->getMockBuilder('Digidirect\ProductMultiView\Block\Product\ImageBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getSmallImage', 'getMediaGalleryImages', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_dataHelper = $this->_objectManager->getObject('Digidirect\ProductMultiView\Helper\Data', [
            '_imageBuilder' => $this->_imageBuilder,
        ]);

        $requestProperty = new \ReflectionProperty('Digidirect\ProductMultiView\Helper\Data', 'scopeConfig');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($this->_dataHelper, $this->_scopeConfig);
    }

    /**
     * Test case module disabled
     * @return void
     */
    public function testModuleDisabled()
    {
        $this->_scopeConfig->expects($this->any())
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_ENABLED, 'website')
            ->willReturn(false);

        $this->_imageBuilder->expects($this->never())
            ->method('create');

        $this->assertFalse($this->_dataHelper->getProductHoverImage($this->_product));
    }

    /**
     * Test case product has alternative image
     * @return void
     */
    public function testProductHasAlternativeImage()
    {
        $this->_scopeConfig->expects($this->any())
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_ENABLED, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getData')
            ->with(\Digidirect\ProductMultiView\Helper\Data::ALTERNATIVE_IMAGE_KEY)
            ->willReturn('image.jpeg');

        $this->assertEquals('image.jpeg', $this->_dataHelper->getProductHoverImage($this->_product));
    }

    /**
     * Test case display random hover disabled
     * @return void
     */
    public function testDisplayRandomHoverDisabled()
    {
        $this->_scopeConfig->expects($this->at(0))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_ENABLED, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getData')
            ->with(\Digidirect\ProductMultiView\Helper\Data::ALTERNATIVE_IMAGE_KEY)
            ->willReturn(null);

        $this->_scopeConfig->expects($this->at(1))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_DISPLAY_RANDOM_HOVER, 'website')
            ->willReturn(false);

        $this->_imageBuilder->expects($this->never())
            ->method('create');

        $this->assertFalse($this->_dataHelper->getProductHoverImage($this->_product));
    }

    /**
     * Test case product has only one product image
     * @return void
     */
    public function testProductDoesNotHaveImages()
    {
        $this->_scopeConfig->expects($this->at(0))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_ENABLED, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getData')
            ->with(\Digidirect\ProductMultiView\Helper\Data::ALTERNATIVE_IMAGE_KEY)
            ->willReturn(null);

        $this->_scopeConfig->expects($this->at(1))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_DISPLAY_RANDOM_HOVER, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getMediaGalleryImages')
            ->willReturn([]);

        $this->_imageBuilder->expects($this->never())
            ->method('create');

        $this->assertFalse($this->_dataHelper->getProductHoverImage($this->_product));
    }

    /**
     * Test case product has only one product image
     * @return void
     */
    public function testProductHasOnlyOneImage()
    {
        $this->_scopeConfig->expects($this->at(0))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_ENABLED, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getData')
            ->with(\Digidirect\ProductMultiView\Helper\Data::ALTERNATIVE_IMAGE_KEY)
            ->willReturn(null);

        $this->_scopeConfig->expects($this->at(1))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_DISPLAY_RANDOM_HOVER, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getMediaGalleryImages')
            ->willReturn([new \Magento\Framework\DataObject(['file' => 'image.jpeg'])]);

        $this->_product->expects($this->any())
            ->method('getSmallImage')
            ->willReturn('image.jpeg');

        $this->_imageBuilder->expects($this->never())
            ->method('create');

        $this->assertFalse($this->_dataHelper->getProductHoverImage($this->_product));
    }

    /**
     * Test case display random image
     * @return void
     */
    public function testDisplayRandomImage()
    {
        $this->_scopeConfig->expects($this->at(0))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_ENABLED, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getData')
            ->with(\Digidirect\ProductMultiView\Helper\Data::ALTERNATIVE_IMAGE_KEY)
            ->willReturn(null);

        $this->_scopeConfig->expects($this->at(1))
            ->method('isSetFlag')
            ->with(\Digidirect\ProductMultiView\Helper\Data::CONFIG_DISPLAY_RANDOM_HOVER, 'website')
            ->willReturn(true);

        $this->_product->expects($this->any())
            ->method('getMediaGalleryImages')
            ->willReturn([
                new \Magento\Framework\DataObject(['file' => 'image.jpeg']),
                new \Magento\Framework\DataObject(['file' => 'image2.jpeg']),
                new \Magento\Framework\DataObject(['file' => 'image3.jpeg']),
            ]);

        $this->_product->expects($this->any())
            ->method('getSmallImage')
            ->willReturn('image.jpeg');

        $this->assertEquals('image2.jpeg', $this->_dataHelper->getProductHoverImage($this->_product));
    }
}
