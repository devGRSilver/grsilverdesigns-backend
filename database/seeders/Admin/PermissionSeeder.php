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
                ['name' => 'users.export', 'display_name' => 'Export Users'],
            ],

            // ID = 2
            'Staff Management' => [
                ['name' => 'staff.view.any', 'display_name' => 'View Staff'],
                ['name' => 'staff.view', 'display_name' => 'View Staff Details'],
                ['name' => 'staff.create', 'display_name' => 'Create Staff'],
                ['name' => 'staff.update', 'display_name' => 'Update Staff'],
                ['name' => 'staff.delete', 'display_name' => 'Delete Staff'],
                ['name' => 'staff.update.status', 'display_name' => 'Update Staff Status'],
                ['name' => 'staff.reset.password', 'display_name' => 'Reset Staff Password'],
            ],

            // ID = 3
            'Role Management' => [
                ['name' => 'roles.view.any', 'display_name' => 'View Roles'],
                ['name' => 'roles.view', 'display_name' => 'View Role Details'],
                ['name' => 'roles.create', 'display_name' => 'Create Role'],
                ['name' => 'roles.update', 'display_name' => 'Update Role'],
                ['name' => 'roles.delete', 'display_name' => 'Delete Role'],
                ['name' => 'roles.update.status', 'display_name' => 'Update Role Status'],
                ['name' => 'roles.assign.permissions', 'display_name' => 'Assign Permissions'],
            ],

            // ID = 4
            'Permission Management' => [
                ['name' => 'permissions.view.any', 'display_name' => 'View Permissions'],
                ['name' => 'permissions.update.status', 'display_name' => 'Update Permission Status'],
            ],

            // ID = 5
            'Attribute Management' => [
                ['name' => 'attributes.view.any', 'display_name' => 'View Attributes'],
                ['name' => 'attributes.create', 'display_name' => 'Create Attribute'],
                ['name' => 'attributes.update', 'display_name' => 'Update Attribute'],
                ['name' => 'attributes.delete', 'display_name' => 'Delete Attribute'],
                ['name' => 'attributes.update.status', 'display_name' => 'Update Attributes Status'],
                ['name' => 'attributes.values.create', 'display_name' => 'Create Attribute Value'],
                ['name' => 'attributes.values.update', 'display_name' => 'Update Attribute Value'],
                ['name' => 'attributes.values.delete', 'display_name' => 'Delete Attribute Value'],
            ],

            // ID = 6
            'Categories Management' => [
                ['name' => 'categories.view.any', 'display_name' => 'View All Categories'],
                ['name' => 'categories.view', 'display_name' => 'View Category Details'],
                ['name' => 'categories.create', 'display_name' => 'Create Category'],
                ['name' => 'categories.update', 'display_name' => 'Update Category'],
                ['name' => 'categories.delete', 'display_name' => 'Delete Category'],
                ['name' => 'categories.update.status', 'display_name' => 'Update Category Status'],
            ],

            // ID = 7
            'Subcategories Management' => [
                ['name' => 'subcategories.view.any', 'display_name' => 'View All Subcategories'],
                ['name' => 'subcategories.view', 'display_name' => 'View Subcategory Details'],
                ['name' => 'subcategories.create', 'display_name' => 'Create Subcategory'],
                ['name' => 'subcategories.update', 'display_name' => 'Update Subcategory'],
                ['name' => 'subcategories.delete', 'display_name' => 'Delete Subcategory'],
                ['name' => 'subcategories.update.status', 'display_name' => 'Update Subcategory Status'],
            ],

            // ID = 8
            'Product Management' => [
                ['name' => 'products.view.any', 'display_name' => 'View All Products'],
                ['name' => 'products.view', 'display_name' => 'View Product Details'],
                ['name' => 'products.create', 'display_name' => 'Create Product'],
                ['name' => 'products.update', 'display_name' => 'Update Product'],
                ['name' => 'products.delete', 'display_name' => 'Delete Product'],
                ['name' => 'products.update.status', 'display_name' => 'Update Product Status'],
                ['name' => 'products.images.upload', 'display_name' => 'Upload Product Images'],
                ['name' => 'products.images.delete', 'display_name' => 'Delete Product Images'],
                ['name' => 'products.variants.manage', 'display_name' => 'Manage Product Variants'],
                ['name' => 'products.import', 'display_name' => 'Import Products'],
                ['name' => 'products.export', 'display_name' => 'Export Products'],
            ],

            // ID = 9
            'Metal Management' => [
                ['name' => 'metals.view.any', 'display_name' => 'View Metals'],
                ['name' => 'metals.create', 'display_name' => 'Create Metal'],
                ['name' => 'metals.update', 'display_name' => 'Update Metal'],
                ['name' => 'metals.delete', 'display_name' => 'Delete Metal'],
                ['name' => 'metals.update.status', 'display_name' => 'Update Metal Status'],
                ['name' => 'metals.assign', 'display_name' => 'Assign Metals'],
                ['name' => 'metals.category.delete', 'display_name' => 'Delete Metal Category'],
                ['name' => 'metals.subcategory.delete', 'display_name' => 'Delete Metal Subcategory'],
            ],

            // ID = 10
            'Content Management' => [
                ['name' => 'contents.view.any', 'display_name' => 'View All Contents'],
                ['name' => 'contents.view', 'display_name' => 'View Content Details'],
                ['name' => 'contents.create', 'display_name' => 'Create Content'],
                ['name' => 'contents.update', 'display_name' => 'Update Content'],
                ['name' => 'contents.delete', 'display_name' => 'Delete Content'],
                ['name' => 'contents.update.status', 'display_name' => 'Update Content Status'],
            ],

            // ID = 11
            'Blog Management' => [
                ['name' => 'blogs.view.any', 'display_name' => 'View All Blogs'],
                ['name' => 'blogs.view', 'display_name' => 'View Blog Details'],
                ['name' => 'blogs.create', 'display_name' => 'Create Blog'],
                ['name' => 'blogs.update', 'display_name' => 'Update Blog'],
                ['name' => 'blogs.delete', 'display_name' => 'Delete Blog'],
                ['name' => 'blogs.update.status', 'display_name' => 'Update Blog Status'],
            ],

            // ID = 12
            'Review Management' => [
                ['name' => 'reviews.view.any', 'display_name' => 'View All Reviews'],
                ['name' => 'reviews.view', 'display_name' => 'View Review Details'],
                ['name' => 'reviews.update.status', 'display_name' => 'Update Review Status'],
                ['name' => 'reviews.delete', 'display_name' => 'Delete Review'],
                ['name' => 'reviews.export', 'display_name' => 'Export Reviews'],
            ],

            // ID = 13
            'Newsletter Management' => [
                ['name' => 'newsletters.view.any', 'display_name' => 'View All Newsletters'],
                ['name' => 'newsletters.view', 'display_name' => 'View Newsletter Details'],
                ['name' => 'newsletters.create', 'display_name' => 'Create Newsletter'],
                ['name' => 'newsletters.update', 'display_name' => 'Update Newsletter'],
                ['name' => 'newsletters.delete', 'display_name' => 'Delete Newsletter'],
                ['name' => 'newsletters.update.status', 'display_name' => 'Update Newsletter Status'],
                ['name' => 'newsletters.send', 'display_name' => 'Send Newsletter'],
                ['name' => 'newsletters.export', 'display_name' => 'Export Newsletters'],
            ],

            // ID = 14
            'Transaction Management' => [
                ['name' => 'transactions.view.any', 'display_name' => 'View All Transactions'],
                ['name' => 'transactions.view', 'display_name' => 'View Transaction Details'],
                ['name' => 'transactions.update.status', 'display_name' => 'Update Transaction Status'],
                ['name' => 'transactions.refund', 'display_name' => 'Refund Transaction'],
                ['name' => 'transactions.export', 'display_name' => 'Export Transactions'],
            ],

            // ID = 15
            'Order Management' => [
                ['name' => 'orders.view.any', 'display_name' => 'View Orders'],
                ['name' => 'orders.view', 'display_name' => 'View Order Details'],
                ['name' => 'orders.update.status', 'display_name' => 'Update Order Status'],
                ['name' => 'orders.cancel', 'display_name' => 'Cancel Order'],
                ['name' => 'orders.invoice', 'display_name' => 'Generate Invoice'],
                ['name' => 'orders.shipment', 'display_name' => 'Update Shipment'],
                ['name' => 'orders.refund', 'display_name' => 'Process Refund'],
                ['name' => 'orders.export', 'display_name' => 'Export Orders'],
            ],

            // ID = 16
            'Coupon Management' => [
                ['name' => 'coupons.view.any', 'display_name' => 'View All Coupons'],
                ['name' => 'coupons.view', 'display_name' => 'View Coupon Details'],
                ['name' => 'coupons.create', 'display_name' => 'Create Coupon'],
                ['name' => 'coupons.update', 'display_name' => 'Update Coupon'],
                ['name' => 'coupons.delete', 'display_name' => 'Delete Coupon'],
                ['name' => 'coupons.update.status', 'display_name' => 'Update Coupon Status'],
            ],

            // ID = 17
            'Setting Management' => [
                ['name' => 'settings.view', 'display_name' => 'View Settings'],
                ['name' => 'settings.update', 'display_name' => 'Update Settings'],
                ['name' => 'settings.email', 'display_name' => 'Email Settings'],
                ['name' => 'settings.payment', 'display_name' => 'Payment Settings'],
                ['name' => 'settings.shipping', 'display_name' => 'Shipping Settings'],
                ['name' => 'settings.seo', 'display_name' => 'SEO Settings'],
            ],

            // ID = 18
            'Payment Management' => [
                ['name' => 'payments.view.any', 'display_name' => 'View Payments'],
                ['name' => 'payments.view', 'display_name' => 'View Payment Details'],
                ['name' => 'payments.export', 'display_name' => 'Export Payments'],
            ],

            // ID = 19
            'Report Management' => [
                ['name' => 'reports.sales', 'display_name' => 'Sales Reports'],
                ['name' => 'reports.products', 'display_name' => 'Product Reports'],
                ['name' => 'reports.customers', 'display_name' => 'Customer Reports'],
                ['name' => 'reports.orders', 'display_name' => 'Order Reports'],
                ['name' => 'reports.revenue', 'display_name' => 'Revenue Reports'],
                ['name' => 'reports.export', 'display_name' => 'Export Reports'],
            ],

            // ID = 20
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
