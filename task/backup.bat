if exist "E:/Inventory-backup" rmdir /Q /S "E:/Inventory-backup"

php C:/xampp/htdocs/inventory/artisan backup:run --only-db