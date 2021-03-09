#Custom Shipping Method Extension
Custom Shipping Method by Sparsh allows the store owner to create their own custom shipping options as well as set custom shipping price for any specific product.

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Sparsh/CustomShippingMethod
4. Drop/move the unzipped files to directory '[Magento_Root]/app/code/Sparsh/CustomShippingMethod'

#Enable Extension:
- php bin/magento module:enable Sparsh_CustomShippingMethod
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Sparsh_CustomShippingMethod
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush