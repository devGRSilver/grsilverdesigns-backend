<?php

namespace Database\Seeders\Admin;

use App\Constants\Constant;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =============================================
         * RESET TABLES (ID will start from 1)
         * =============================================
         */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        PermissionGroup::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /**
         * =============================================
         * PERMISSION GROUPS (ORDER = ID ORDER)
         * =============================================
         */
        $permissionGroups = [
            'Dashboard Management' => [
                ['name' => 'dashboard.view', 'display_name' => 'Dashboard View'],
            ],

            // ID = 1
            'User Management' => [
                ['name' => 'users.view.any', 'display_name' => 'View Users'],
                ['name' => 'users.view', 'display_name' => 'View User Details'],
                ['name' => 'users.create', 'display_name' => 'Create User'],
                ['name' => 'users.update', 'display_name' => 'Update User'],
                ['name' => 'users.delete', 'display_name' => 'Delete User'],
                ['name' => 'users.update.status', 'display_name' => 'Update User Status'],
                ['name' => 'users.reset.password', 'display_name' => 'Reset User Password'],
            ],



            // ID = 3
            'Attribute Management' => [
                ['name' => 'attributes.view.any', 'display_name' => 'View Attributes'],
                ['name' => 'attributes.create', 'display_name' => 'Create Attribute'],
                ['name' => 'attributes.delete', 'display_name' => 'Delete Attribute'],
                ['name' => 'attributes.update.status', 'display_name' => 'Update Attributes Status'],
                ['name' => 'attributes.values.create', 'display_name' => 'Create Attribute Value'],
                ['name' => 'attributes.values.delete', 'display_name' => 'Delete Attribute Value'],
            ],

            // ID = 4
            'Categories Management' => [
                ['name' => 'categories.view.any', 'display_name' => 'View All Categories'],
                ['name' => 'categories.view', 'display_name' => 'View Category Details'],
                ['name' => 'categories.create', 'display_name' => 'Create Category'],
                ['name' => 'categories.update', 'display_name' => 'Update Category'],
                ['name' => 'categories.delete', 'display_name' => 'Delete Category'],
                ['name' => 'categories.update.status', 'display_name' => 'Update Category Status'],
            ],

            // ID = 5
            'Subcategories Management' => [
                ['name' => 'subcategories.view.any', 'display_name' => 'View All Subcategories'],
                ['name' => 'subcategories.view', 'display_name' => 'View Subcategory Details'],
                ['name' => 'subcategories.create', 'display_name' => 'Create Subcategory'],
                ['name' => 'subcategories.update', 'display_name' => 'Update Subcategory'],
                ['name' => 'subcategories.delete', 'display_name' => 'Delete Subcategory'],
                ['name' => 'subcategories.update.status', 'display_name' => 'Update Subcategory Status'],
            ],

            // ID = 6
            'Product Management' => [
                ['name' => 'products.view.any', 'display_name' => 'View All Products'],
                ['name' => 'products.view', 'display_name' => 'View Product Details'],
                ['name' => 'products.create', 'display_name' => 'Create Product'],
                ['name' => 'products.update', 'display_name' => 'Update Product'],
                ['name' => 'products.delete', 'display_name' => 'Delete Product'],
                ['name' => 'products.update.status', 'display_name' => 'Update Product Status'],
                ['name' => 'products.images.delete', 'display_name' => 'Delete Product Images'],
                ['name' => 'products.import', 'display_name' => 'Import Products'],
                ['name' => 'products.export', 'display_name' => 'Export Products'],
            ],

            // ID = 7
            'Metal Management' => [
                ['name' => 'metals.view.any', 'display_name' => 'View Metals'],
                ['name' => 'metals.update', 'display_name' => 'Update Metals'],
                ['name' => 'metals.assign', 'display_name' => 'Assign Metals'],
                ['name' => 'metals.category.delete', 'display_name' => 'Delete Metal Category'],
                ['name' => 'metals.subcategory.delete', 'display_name' => 'Delete Metal Subcategory'],
            ],

            // ID = 8
            'Content Management' => [
                ['name' => 'contents.view.any', 'display_name' => 'View All Contents'],
                ['name' => 'contents.view', 'display_name' => 'View Content Details'],
                ['name' => 'contents.update', 'display_name' => 'Update Content'],
                ['name' => 'contents.update.status', 'display_name' => 'Update Content Status'],
            ],

            // ID = 9
            'Blog Management' => [
                ['name' => 'blogs.view.any', 'display_name' => 'View All Blogs'],
                ['name' => 'blogs.view', 'display_name' => 'View Blog Details'],
                ['name' => 'blogs.create', 'display_name' => 'Create Blog'],
                ['name' => 'blogs.update', 'display_name' => 'Update Blog'],
                ['name' => 'blogs.delete', 'display_name' => 'Delete Blog'],
                ['name' => 'blogs.update.status', 'display_name' => 'Update Blog Status'],
            ],

            // ID = 10
            'Review Management' => [
                ['name' => 'reviews.view.any', 'display_name' => 'View All Reviews'],
                ['name' => 'reviews.view', 'display_name' => 'View Review Details'],
                ['name' => 'reviews.update.status', 'display_name' => 'Update Review Status'],
                ['name' => 'reviews.delete', 'display_name' => 'Delete Review'],
            ],

            // ID = 11
            'Newsletter Management' => [
                ['name' => 'newsletters.view.any', 'display_name' => 'View All Newsletters'],
                ['name' => 'newsletters.update.status', 'display_name' => 'Update Newsletter Status'],
            ],

            // ID = 12
            'Transaction Management' => [
                ['name' => 'transactions.view.any', 'display_name' => 'View All Transactions'],
                ['name' => 'transactions.view', 'display_name' => 'View Transaction Details'],
            ],

            // ID = 13
            'Order Management' => [
                ['name' => 'orders.view.any', 'display_name' => 'View Orders'],
                ['name' => 'orders.view', 'display_name' => 'View Order Details'],
                ['name' => 'orders.update.status', 'display_name' => 'Update Order Status'],
                ['name' => 'orders.cancel', 'display_name' => 'Cancel Order'],
                ['name' => 'orders.invoice', 'display_name' => 'Generate Invoice'],
            ],

            // ID = 15
            'Setting Management' => [
                ['name' => 'settings.view', 'display_name' => 'View Settings'],
                ['name' => 'settings.update', 'display_name' => 'Update Settings'],
            ],


            // ID = 17
            'Payment Management' => [
                ['name' => 'payments.view.any', 'display_name' => 'View Payments'],
                ['name' => 'payments.view', 'display_name' => 'View Payment Details'],
                ['name' => 'payments.refund', 'display_name' => 'Refund Payment'],
            ],




            // ID = 18
            'Global Search Management' => [
                ['name' => 'global.search', 'display_name' => 'Global Search'],
            ],
        ];

        /**
         * =============================================
         * SAVE DATA (ID ORDER PRESERVED)
         * =============================================
         */
        foreach ($permissionGroups as $groupName => $permissions) {

            // Save group first (ID auto-increment)
            $group = PermissionGroup::create([
                'name' => Str::slug($groupName),
                'display_name' => $groupName,
                'status' => Constant::ACTIVE,
            ]);

            // Save permissions under group
            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'guard_name' => 'admin',
                    'permission_group_id' => $group->id,
                ]);
            }
        }
    }
}
