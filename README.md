# Magento Admin Grids Module

Work in Progress.
This module uses Hyvä Admin module (https://github.com/hyva-themes/magento2-hyva-admin) developed by Vinai Kopp (https://github.com/Vinai).

This module replaces:
* Products grid (Catalog » Products) - In progress
* Customers grid (Customers » Customers) - To do

## Installation
### Composer
```
    composer require catgento/magento2-admin-grids
```
### ZIP file
Download the module and unzip it under the folder app/code/Catgento/AdminGrids.

#Motivation and goals

The aim is to replace some of the Magento 2 Admin Grids (Product and Customers initially) and use instead Hyvä Admin Grids.

These are some reasons for replacing Magento 2 Admin Grids:
* Native Grids are slow to load (with Hyvä Grids, grids load 3-4x faster)
* Native Filtering is complex and requires so many clicks. When you have many people working every day with products, filters become
essential for their work. Hyvä Admin Grids places the filters back to the top of the columns
* Customization and maintainability: because working with Hyvä (tailwind and Alpine.js) is easier and more fun than working with Magento UI Components
