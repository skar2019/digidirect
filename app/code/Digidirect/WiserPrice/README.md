# Digidirect Wiser Price Import Module

## Overview
This Magento 2.4 module allows administrators to upload a CSV file containing SKU and wiser_price data to bulk update the `wiser_price` custom attribute for products.

## Features
- Admin interface under Digidirect > Wiser Price Import menu
- CSV file upload with validation
- Bulk update of wiser_price custom product attribute
- Detailed success/error reporting
- Logging of all operations

## Installation

### Step 1: Copy Module Files
Copy the `Digidirect_WiserPrice` folder to your Magento installation:

```bash
cp -r Digidirect_WiserPrice /path/to/magento/app/code/
```

### Step 2: Enable the Module
Run the following commands from your Magento root directory:

```bash
php bin/magento module:enable Digidirect_WiserPrice
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
php bin/magento cache:flush
```

### Step 3: Set Permissions (if needed)
```bash
chmod -R 755 app/code/Digidirect_WiserPrice
chown -R www-data:www-data app/code/Digidirect_WiserPrice
```

### Step 4: Verify Installation
1. Log into Magento Admin
2. Navigate to **Digidirect > Wiser Price Import**
3. You should see the import interface

## Prerequisites

### Important: wiser_price Attribute Must Exist
This module requires that the `wiser_price` product attribute already exists in your Magento installation. If it doesn't exist, you need to create it first.

#### To create the wiser_price attribute manually:

1. Go to **Stores > Attributes > Product**
2. Click **Add New Attribute**
3. Configure:
   - Attribute Code: `wiser_price`
   - Catalog Input Type: Price
   - Default Label: Wiser Price
   - Add to Attribute Set: Default (or your relevant attribute set)
4. Save the attribute

#### Or create via SQL (ensure you're using the correct attribute set ID):

```sql
-- This is just an example. You may need to adjust attribute_id, entity_type_id, etc.
-- It's safer to create through admin interface or proper setup script
```

## CSV File Format

### Required Columns
The CSV file must contain exactly these two columns:
- `sku` - The product SKU
- `wiser_price` - The price value to set

### Example CSV Format

```csv
sku,wiser_price
PROD001,99.99
PROD002,149.50
PROD003,199.00
SAMPLE-SKU-123,299.99
```

### Important Notes
- First row must be the header row with column names
- Column names are case-insensitive (SKU, sku, Sku all work)
- Empty rows or rows with missing SKU will be skipped
- Products that don't exist will generate errors but won't stop the import

## Usage

1. **Navigate to the Import Page**
   - Log into Magento Admin
   - Go to **Digidirect > Wiser Price Import**

2. **Prepare Your CSV File**
   - Create a CSV file with `sku` and `wiser_price` columns
   - Ensure the file follows the format above

3. **Upload and Import**
   - Click **Choose File** and select your CSV
   - Click **Import** button
   - Wait for processing to complete

4. **Review Results**
   - Success message shows number of products updated
   - Warning message shows number of rows skipped
   - Error messages show products that couldn't be updated
   - Check `var/log/system.log` for detailed error information

## Error Handling

The module provides comprehensive error handling:

- **File Validation**: Ensures only CSV files are uploaded
- **Column Validation**: Checks for required columns
- **Product Validation**: Verifies products exist before updating
- **Transaction Safety**: Each product update is isolated
- **Detailed Logging**: All errors logged to system.log

### Common Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| "Please upload a valid CSV file" | No file uploaded or upload failed | Check file size and upload settings |
| "CSV file must contain sku and wiser_price columns" | Missing required columns | Add missing columns to CSV |
| "Product with SKU not found" | SKU doesn't exist in catalog | Verify SKU or remove from CSV |
| "wiser_price attribute does not exist" | Custom attribute not created | Create the wiser_price attribute first |

## Permissions

To access the import functionality, users need the following permission:
- **Resource**: Digidirect > Wiser Price Import

Admin users can grant this permission through:
**System > User Roles > [Select Role] > Role Resources**

## Technical Details

### Module Structure
```
Digidirect_WiserPrice/
├── Block/
│   └── Adminhtml/
│       └── Import/
│           └── Index.php
├── Controller/
│   └── Adminhtml/
│       └── Import/
│           ├── Index.php
│           └── Save.php
├── etc/
│   ├── acl.xml
│   ├── module.xml
│   └── adminhtml/
│       ├── menu.xml
│       └── routes.xml
├── view/
│   └── adminhtml/
│       ├── layout/
│       │   └── wiserprice_import_index.xml
│       └── templates/
│           └── import/
│               └── index.phtml
└── registration.php
```

### Dependencies
- Magento_Backend
- Magento_Catalog

### Logging
All operations are logged to:
- `var/log/system.log`

## Troubleshooting

### Module Not Showing in Admin Menu
```bash
php bin/magento cache:clean
php bin/magento cache:flush
```

### Permission Denied Error
Ensure the web server has write permissions:
```bash
chmod -R 755 app/code/Digidirect_WiserPrice
```

### Products Not Updating
1. Verify the `wiser_price` attribute exists
2. Check that SKUs in CSV match exactly (case-sensitive)
3. Review `var/log/system.log` for detailed errors

## Support

For issues or questions:
1. Check `var/log/system.log` for detailed error messages
2. Verify the `wiser_price` attribute exists and is properly configured
3. Ensure CSV format matches the required structure

## Version History

### Version 1.0.0
- Initial release
- CSV upload functionality
- Bulk product attribute updates
- Admin interface under Digidirect menu
- Comprehensive error handling and logging

## License
Copyright © Digidirect. All rights reserved.
