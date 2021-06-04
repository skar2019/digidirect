## Out of Stock Notification Settings

File frontend/templates/product/view.phtml

Option | Type | Default | Description
------ | ---- | ------- | -----------
availableProducts | array | [] | List of products having a status "In Stock"
outofstockHideContainer | string | 'outofstock-button' | The block selector that will be displayed instead of the add to cart button
outofstockformContainer | string | '.product-options-bottom' | The block selector that you want to hide for a product with the status "Out of Stock"
actionContainer | string | 'outofstock-form' | Form's selector 
modal | boolean | true | Use or not use modal window
popupButton | string | 'outofstock-popup-show' | Selector button for calling a pop-up
visibleClass | string | '-visible-block' | Class to show the hidden block
modalOptions | object | {...} | Magento_Ui modal options
notificationForBackorder | boolean | false | Show Notification For Backorder Product
isBackorderProduct | boolean | false, | Is Backorder Product
backorderButtonLabel | string | $t('Back-order'), | Back Order Button Label
backorderButtonClassname | string | '-back-order', | Back Order Button HTML Class Name
addToCartFormSelector | string | '#product_addtocart_form', | addToCart Form Selector
addToCartButtonSelector | string | '.action.tocart' | addToCart Button Selector


File frontend/templates/product/view/type/options/configurable.phtml

Option | Type | Default | Description
------ | ---- | ------- | -----------
spConfig | object | {} | Data on all products
availableProducts | array | [] | List of products having a status "In Stock"
superSelector | string | '.super-attribute-select' | The selector of the elements responsible for the selection of the product
outofstockInput | string | 'outofstock-input' | The selector of the form of the module
attributeContainer | string | '.swatch-attribute' | The selector for container of attribute
swatchElements | string | 'swatch-options' | The selector of the unit containing the swatches
swatchEventElements | string | 'swatch-option' | The selector of swatches buttons
hiddenClass | string | 'no-display' | Class to hide the block with button Add to Cart
visibleClass | string | '-visible-block' | Class to show the hidden block
notAvailableClass | string | '-not-available' | Class for not available attributes
selectedClass | string | '.selected' | The selector selected attributes 
availabilityDateInfo | array | [] | Data for the availability date for simple products
soldOutMessageInfo | array | [] | List products for show sold out message 
defaultMessage | string | 'This item has sold out' | Default sold out message 
targetElement | string | '[data-role="outofstock-button"]' | Selector for element, before wich insert data