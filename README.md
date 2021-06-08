# Magento 1.9 Assign simple products to grouped
A Magento 1.9's module to associate grouped products with their children products.

## Module informations
`Package/Namespace`: "Matheus"  

`Modulename`: "AssignToGrouped"

`codepool`: "community"  

## How to install
Add the folder `Matheus` inside `/app/code/community/` and add the file `Matheus_AssignToGrouped.xml` inside `/app/etc/modules/`

## How to use
After installation a new submenu named `Assign To Grouped` will be created at the menu `Catalog` in your admin panel. Click in it to enter the module's page. 



Now, you just need to upload your file and press in the `Start` button to associate simple products with their respective grouped products.



## Input file pattern
The input file must be in CSV format and in the following order:
|sku_grouped|skus_children|
| --- | --- |
|product-sku|product-child-1|


To associate multiple products to a grouped product you just need to separate the SKUs in the column `skus_children` with a `;`, like shown below:
|sku_grouped|skus_children|
| --- | --- |
|product-2-sku|product-child-1;product-child-2;product-child-3|
