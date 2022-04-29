<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\ManageRoles;
use App\RolesPermission;
use App\User;
use DB;

class ManagePermissionController extends Controller
{
    public function __construct(){
        $this->middleware('auth', ['except' => ['permissionDenied']]);
    }

    public function index(){

    }

    public function setPermission($id){
        // dd($id);
        // User role permission start here
        $user = auth()->user();
        if($user->role_id != 1){
            return view('permission-denied');
        }
        // User role permission end here


        $controllers = $this->getControllers($id);

        $role = ManageRoles::where('id', $id)->first();
        return view('manage-permissions.set', compact('role','controllers'));
    }

    public function store(Request $request){
        // dd($request->all());
        $role_id = $request->roles_id;

        $permissions = $request->permission;

        foreach($permissions as $key=>$value) {
            // dd($key,$value);

            RolesPermission::updateOrCreate(
                // fields to match in DB
                ['permission_key' => $key, 'roles_id' => $role_id], 
                // fields to set the values
                [
                    'roles_id'           => $role_id, 
                    'permission_key'     => $key, 
                    'permission_allowed' => $value, 
                ]
            );
        }

        return back()->with('success','Permission entry is successfully saved.');
    }

    public function permissionDenied(){
        return view('permission-denied');
    }

    function getControllers($id){

        $permission_query = RolesPermission::where('roles_id', $id)->get();
        $permissions = [];

        foreach($permission_query as $permission){
            $permissions[$permission->permission_key] = $permission->permission_allowed;
        }

        $controllers = [
            'AdjustmentController' => [
                'controller_name' => 'Adjustment',
                'methods' => [
                    'index' => empty($permissions['AdjustmentController.index']) ? 0 : $permissions['AdjustmentController.index'],
                    'add' => empty($permissions['AdjustmentController.add']) ? 0 : $permissions['AdjustmentController.add'],
                    'edit' => empty($permissions['AdjustmentController.edit']) ? 0 : $permissions['AdjustmentController.edit'],
                    'show' => empty($permissions['AdjustmentController.show']) ? 0 : $permissions['AdjustmentController.show'],
                    'delete' => empty($permissions['AdjustmentController.delete']) ? 0 : $permissions['AdjustmentController.delete'],
                ]
            ],
        
            'BillingInvoiceController' => [
                'controller_name' => 'Billing',
                'methods' => [
                    'index' => empty($permissions['BillingInvoiceController.index']) ? 0 : $permissions['BillingInvoiceController.index'],
                    'add' => empty($permissions['BillingInvoiceController.add']) ? 0 : $permissions['BillingInvoiceController.add'],
                    'edit' => empty($permissions['BillingInvoiceController.edit']) ? 0 : $permissions['BillingInvoiceController.edit'],
                    'show' => empty($permissions['BillingInvoiceController.show']) ? 0 : $permissions['BillingInvoiceController.show'],
                    'delete' => empty($permissions['BillingInvoiceController.delete']) ? 0 : $permissions['BillingInvoiceController.delete'],
                    'view_details' => empty($permissions['BillingInvoiceController.view_details']) ? 0 : $permissions['BillingInvoiceController.view_details'],
                ]
            ],

            'CashAdvanceController' => [
                'controller_name' => 'Cash Advance',
                'methods' => [
                    'index' => empty($permissions['CashAdvanceController.index']) ? 0 : $permissions['CashAdvanceController.index'],
                    'add' => empty($permissions['CashAdvanceController.add']) ? 0 : $permissions['CashAdvanceController.add'],
                    'edit' => empty($permissions['CashAdvanceController.edit']) ? 0 : $permissions['CashAdvanceController.edit'],
                    'show' => empty($permissions['CashAdvanceController.show']) ? 0 : $permissions['CashAdvanceController.show'],
                    'delete' => empty($permissions['CashAdvanceController.delete']) ? 0 : $permissions['CashAdvanceController.delete'],
                    'view_details' => empty($permissions['CashAdvanceController.view_details']) ? 0 : $permissions['CashAdvanceController.view_details'],
                ]
            ],

            'CashInvoiceController' => [
                'controller_name' => 'Cash Invoice',
                'methods' => [
                    'index' => empty($permissions['CashInvoiceController.index']) ? 0 : $permissions['CashInvoiceController.index'],
                    'add' => empty($permissions['CashInvoiceController.add']) ? 0 : $permissions['CashInvoiceController.add'],
                    'edit' => empty($permissions['CashInvoiceController.edit']) ? 0 : $permissions['CashInvoiceController.edit'],
                    'show' => empty($permissions['CashInvoiceController.show']) ? 0 : $permissions['CashInvoiceController.show'],
                    'delete' => empty($permissions['CashInvoiceController.delete']) ? 0 : $permissions['CashInvoiceController.delete'],
                ]
            ],

            'CashPaymentVoucherController' => [
                'controller_name' => 'Cash Payment Voucher',
                'methods' => [
                    'index' => empty($permissions['CashPaymentVoucherController.index']) ? 0 : $permissions['CashPaymentVoucherController.index'],
                    'add' => empty($permissions['CashPaymentVoucherController.add']) ? 0 : $permissions['CashPaymentVoucherController.add'],
                    'edit' => empty($permissions['CashPaymentVoucherController.edit']) ? 0 : $permissions['CashPaymentVoucherController.edit'],
                    'show' => empty($permissions['CashPaymentVoucherController.show']) ? 0 : $permissions['CashPaymentVoucherController.show'],
                    'delete' => empty($permissions['CashPaymentVoucherController.delete']) ? 0 : $permissions['CashPaymentVoucherController.delete'],
                    'view_details' => empty($permissions['CashPaymentVoucherController.view_details']) ? 0 : $permissions['CashPaymentVoucherController.view_details'],
                ]
            ],

            'ChartAccountController' => [
                'controller_name' => 'Chart Account',
                'methods' => [
                    'index' => empty($permissions['ChartAccountController.index']) ? 0 : $permissions['ChartAccountController.index'],
                    'add' => empty($permissions['ChartAccountController.add']) ? 0 : $permissions['ChartAccountController.add'],
                    'edit' => empty($permissions['ChartAccountController.edit']) ? 0 : $permissions['ChartAccountController.edit'],
                    'show' => empty($permissions['ChartAccountController.show']) ? 0 : $permissions['ChartAccountController.show'],
                    'delete' => empty($permissions['ChartAccountController.delete']) ? 0 : $permissions['ChartAccountController.delete'],
                ]
            ],

            'ChartAccountTypeController' => [
                'controller_name' => 'Chart Account Type',
                'methods' => [
                    'index' => empty($permissions['ChartAccountTypeController.index']) ? 0 : $permissions['ChartAccountTypeController.index'],
                    'add' => empty($permissions['ChartAccountTypeController.add']) ? 0 : $permissions['ChartAccountTypeController.add'],
                    'edit' => empty($permissions['ChartAccountTypeController.edit']) ? 0 : $permissions['ChartAccountTypeController.edit'],
                    'show' => empty($permissions['ChartAccountTypeController.show']) ? 0 : $permissions['ChartAccountTypeController.show'],
                    'delete' => empty($permissions['ChartAccountTypeController.delete']) ? 0 : $permissions['ChartAccountTypeController.delete'],
                ]
            ],

            'CollectionReceiptController' => [
                'controller_name' => 'Collection Receipt',
                'methods' => [
                    'index' => empty($permissions['CollectionReceiptController.index']) ? 0 : $permissions['CollectionReceiptController.index'],
                    'add' => empty($permissions['CollectionReceiptController.add']) ? 0 : $permissions['CollectionReceiptController.add'],
                    'edit' => empty($permissions['CollectionReceiptController.edit']) ? 0 : $permissions['CollectionReceiptController.edit'],
                    'show' => empty($permissions['CollectionReceiptController.show']) ? 0 : $permissions['CollectionReceiptController.show'],
                    'delete' => empty($permissions['CollectionReceiptController.delete']) ? 0 : $permissions['CollectionReceiptController.delete'],
                ]
            ],

            'CompanyController' => [
                'controller_name' => 'Company Information',
                'methods' => [
                    'index' => empty($permissions['CompanyController.index']) ? 0 : $permissions['CompanyController.index'],
                    'add' => empty($permissions['CompanyController.add']) ? 0 : $permissions['CompanyController.add'],
                    'edit' => empty($permissions['CompanyController.edit']) ? 0 : $permissions['CompanyController.edit'],
                    'show' => empty($permissions['CompanyController.show']) ? 0 : $permissions['CompanyController.show'],
                    'delete' => empty($permissions['CompanyController.delete']) ? 0 : $permissions['CompanyController.delete'],
                ]
            ],

            'CreditInvoiceController' => [
                'controller_name' => 'Credit Invoice',
                'methods' => [
                    'index' => empty($permissions['CreditInvoiceController.index']) ? 0 : $permissions['CreditInvoiceController.index'],
                    'add' => empty($permissions['CreditInvoiceController.add']) ? 0 : $permissions['CreditInvoiceController.add'],
                    'edit' => empty($permissions['CreditInvoiceController.edit']) ? 0 : $permissions['CreditInvoiceController.edit'],
                    'show' => empty($permissions['CreditInvoiceController.show']) ? 0 : $permissions['CreditInvoiceController.show'],
                    'delete' => empty($permissions['CreditInvoiceController.delete']) ? 0 : $permissions['CreditInvoiceController.delete'],
                    'view_details' => empty($permissions['CreditInvoiceController.view_details']) ? 0 : $permissions['CreditInvoiceController.view_details'],
                ]
            ],

            'CustomerCRUDController' => [
                'controller_name' => 'Customer Information',
                'methods' => [
                    'index' => empty($permissions['CustomerCRUDController.index']) ? 0 : $permissions['CustomerCRUDController.index'],
                    'add' => empty($permissions['CustomerCRUDController.add']) ? 0 : $permissions['CustomerCRUDController.add'],
                    'edit' => empty($permissions['CustomerCRUDController.edit']) ? 0 : $permissions['CustomerCRUDController.edit'],
                    'show' => empty($permissions['CustomerCRUDController.show']) ? 0 : $permissions['CustomerCRUDController.show'],
                    'delete' => empty($permissions['CustomerCRUDController.delete']) ? 0 : $permissions['CustomerCRUDController.delete'],
                ]
            ],

            'DiscountController' => [
                'controller_name' => 'Discount',
                'methods' => [
                    'index' => empty($permissions['DiscountController.index']) ? 0 : $permissions['DiscountController.index'],
                    'add' => empty($permissions['DiscountController.add']) ? 0 : $permissions['DiscountController.add'],
                    'edit' => empty($permissions['DiscountController.edit']) ? 0 : $permissions['DiscountController.edit'],
                    'show' => empty($permissions['DiscountController.show']) ? 0 : $permissions['DiscountController.show'],
                    'delete' => empty($permissions['DiscountController.delete']) ? 0 : $permissions['DiscountController.delete'],
                ]
            ],

            'DTRController' => [
                'controller_name' => 'Daily Time Record',
                'methods' => [
                    'index' => empty($permissions['DTRController.index']) ? 0 : $permissions['DTRController.index'],
                    'add' => empty($permissions['DTRController.add']) ? 0 : $permissions['DTRController.add'],
                    'edit' => empty($permissions['DTRController.edit']) ? 0 : $permissions['DTRController.edit'],
                    'delete' => empty($permissions['DTRController.delete']) ? 0 : $permissions['DTRController.delete'],
                    'view_details' => empty($permissions['DTRController.view_details']) ? 0 : $permissions['DTRController.view_details'],
                    'qr_code' => empty($permissions['DTRController.qr_code']) ? 0 : $permissions['DTRController.qr_code'],
                ]
            ],

            'EmployeeCRUDController' => [
                'controller_name' => 'Employee Information',
                'methods' => [
                    'index' => empty($permissions['EmployeeCRUDController.index']) ? 0 : $permissions['EmployeeCRUDController.index'],
                    'add' => empty($permissions['EmployeeCRUDController.add']) ? 0 : $permissions['EmployeeCRUDController.add'],
                    'edit' => empty($permissions['EmployeeCRUDController.edit']) ? 0 : $permissions['EmployeeCRUDController.edit'],
                    'show' => empty($permissions['EmployeeCRUDController.show']) ? 0 : $permissions['EmployeeCRUDController.show'],
                    'delete' => empty($permissions['EmployeeCRUDController.delete']) ? 0 : $permissions['EmployeeCRUDController.delete'],
                ]
            ],

            'InventoryController' => [
                'controller_name' => 'Inventory',
                'methods' => [
                    'index' => empty($permissions['InventoryController.index']) ? 0 : $permissions['InventoryController.index'],
                    'view_details' => empty($permissions['InventoryController.view_details']) ? 0 : $permissions['InventoryController.view_details'],
                ]
            ],

            'JournalController' => [
                'controller_name' => 'Journal',
                'methods' => [
                    'index' => empty($permissions['JournalController.index']) ? 0 : $permissions['JournalController.index'],
                ]
            ],

            'LedgerController' => [
                'controller_name' => 'Ledger',
                'methods' => [
                    'index' => empty($permissions['LedgerController.index']) ? 0 : $permissions['LedgerController.index'],
                ]
            ],
            
            'OfficialReceiptController' => [
                'controller_name' => 'Official Receipt',
                'methods' => [
                    'index' => empty($permissions['OfficialReceiptController.index']) ? 0 : $permissions['OfficialReceiptController.index'],
                    'add' => empty($permissions['OfficialReceiptController.add']) ? 0 : $permissions['OfficialReceiptController.add'],
                    'edit' => empty($permissions['OfficialReceiptController.edit']) ? 0 : $permissions['OfficialReceiptController.edit'],
                    'show' => empty($permissions['OfficialReceiptController.show']) ? 0 : $permissions['OfficialReceiptController.show'],
                    'delete' => empty($permissions['OfficialReceiptController.delete']) ? 0 : $permissions['OfficialReceiptController.delete'],
                    'view_details' => empty($permissions['OfficialReceiptController.view_details']) ? 0 : $permissions['OfficialReceiptController.view_details'],
                ]
            ],

            'OpenInvoiceController' => [
                'controller_name' => 'Open Invoice',
                'methods' => [
                    'index' => empty($permissions['OpenInvoiceController.index']) ? 0 : $permissions['OpenInvoiceController.index'],
                    'add' => empty($permissions['OpenInvoiceController.add']) ? 0 : $permissions['OpenInvoiceController.add'],
                    'edit' => empty($permissions['OpenInvoiceController.edit']) ? 0 : $permissions['OpenInvoiceController.edit'],
                    'show' => empty($permissions['OpenInvoiceController.show']) ? 0 : $permissions['OpenInvoiceController.show'],
                    'delete' => empty($permissions['OpenInvoiceController.delete']) ? 0 : $permissions['OpenInvoiceController.delete'],
                    'view_details' => empty($permissions['OpenInvoiceController.view_details']) ? 0 : $permissions['OpenInvoiceController.view_details'],
                ]
            ],

            'OptionController' => [
                'controller_name' => 'Option',
                'methods' => [
                    'index' => empty($permissions['OptionController.index']) ? 0 : $permissions['OptionController.index'],
                    'add' => empty($permissions['OptionController.add']) ? 0 : $permissions['OptionController.add'],
                    'edit' => empty($permissions['OptionController.edit']) ? 0 : $permissions['OptionController.edit'],
                ]
            ],

            'PayrollController' => [
                'controller_name' => 'Payroll',
                'methods' => [
                    'index' => empty($permissions['PayrollController.index']) ? 0 : $permissions['PayrollController.index'],
                    'add' => empty($permissions['PayrollController.add']) ? 0 : $permissions['PayrollController.add'],
                    'edit' => empty($permissions['PayrollController.edit']) ? 0 : $permissions['PayrollController.edit'],
                    'delete' => empty($permissions['PayrollController.delete']) ? 0 : $permissions['PayrollController.delete'],
                    'view_details' => empty($permissions['PayrollController.view_details']) ? 0 : $permissions['PayrollController.view_details'],
                ]
            ],

            'POSController' => [
                'controller_name' => 'Point of Sale (POS)',
                'methods' => [
                    'index' => empty($permissions['POSController.index']) ? 0 : $permissions['POSController.index'],
                    'add' => empty($permissions['POSController.add']) ? 0 : $permissions['POSController.add'],
                    'edit' => empty($permissions['POSController.edit']) ? 0 : $permissions['POSController.edit'],
                    'delete' => empty($permissions['POSController.delete']) ? 0 : $permissions['POSController.delete'],
                    'view_details' => empty($permissions['POSController.view_details']) ? 0 : $permissions['POSController.view_details'],
                    'report' => empty($permissions['POSController.report']) ? 0 : $permissions['POSController.report'],
                ]
            ],

            'POSExpensesController' => [
                'controller_name' => 'POS Expenses',
                'methods' => [
                    'index' => empty($permissions['POSExpensesController.index']) ? 0 : $permissions['POSExpensesController.index'],
                    'add' => empty($permissions['POSExpensesController.add']) ? 0 : $permissions['POSExpensesController.add'],
                    'edit' => empty($permissions['POSExpensesController.edit']) ? 0 : $permissions['POSExpensesController.edit'],
                    'delete' => empty($permissions['POSExpensesController.delete']) ? 0 : $permissions['POSExpensesController.delete'],
                ]
            ],

            'ProductController' => [
                'controller_name' => 'Products',
                'methods' => [
                    'index' => empty($permissions['ProductController.index']) ? 0 : $permissions['ProductController.index'],
                    'add' => empty($permissions['ProductController.add']) ? 0 : $permissions['ProductController.add'],
                    'edit' => empty($permissions['ProductController.edit']) ? 0 : $permissions['ProductController.edit'],
                    'show' => empty($permissions['ProductController.show']) ? 0 : $permissions['ProductController.show'],
                    'delete' => empty($permissions['ProductController.delete']) ? 0 : $permissions['ProductController.delete'],
                    'view_details' => empty($permissions['ProductController.view_details']) ? 0 : $permissions['ProductController.view_details'],
                ]
            ],

            'ReportController' => [
                'controller_name' => 'Reports',
                'methods' => [
                    'index' => empty($permissions['ReportController.index']) ? 0 : $permissions['ReportController.index'],
                    'report' => empty($permissions['ReportController.report']) ? 0 : $permissions['ReportController.report'],
                    'export' => empty($permissions['ReportController.export']) ? 0 : $permissions['ReportController.export'],
                ]
            ],

            'ServiceCRUDController' => [
                'controller_name' => 'Services',
                'methods' => [
                    'index' => empty($permissions['ServiceCRUDController.index']) ? 0 : $permissions['ServiceCRUDController.index'],
                    'add' => empty($permissions['ServiceCRUDController.add']) ? 0 : $permissions['ServiceCRUDController.add'],
                    'edit' => empty($permissions['ServiceCRUDController.edit']) ? 0 : $permissions['ServiceCRUDController.edit'],
                    'show' => empty($permissions['ServiceCRUDController.show']) ? 0 : $permissions['ServiceCRUDController.show'],
                    'delete' => empty($permissions['ServiceCRUDController.delete']) ? 0 : $permissions['ServiceCRUDController.delete'],
                ]
            ],

            'SubAccountTypeController' => [
                'controller_name' => 'Sub Account Type',
                'methods' => [
                    'index' => empty($permissions['SubAccountTypeController.index']) ? 0 : $permissions['SubAccountTypeController.index'],
                    'add' => empty($permissions['SubAccountTypeController.add']) ? 0 : $permissions['SubAccountTypeController.add'],
                    'edit' => empty($permissions['SubAccountTypeController.edit']) ? 0 : $permissions['SubAccountTypeController.edit'],
                    'show' => empty($permissions['SubAccountTypeController.show']) ? 0 : $permissions['SubAccountTypeController.show'],
                    'delete' => empty($permissions['SubAccountTypeController.delete']) ? 0 : $permissions['SubAccountTypeController.delete'],
                ]
            ],

            'SupplierInvoiceController' => [
                'controller_name' => 'Supplier Invoice',
                'methods' => [
                    'index' => empty($permissions['SupplierInvoiceController.index']) ? 0 : $permissions['SupplierInvoiceController.index'],
                    'add' => empty($permissions['SupplierInvoiceController.add']) ? 0 : $permissions['SupplierInvoiceController.add'],
                    'edit' => empty($permissions['SupplierInvoiceController.edit']) ? 0 : $permissions['SupplierInvoiceController.edit'],
                    'show' => empty($permissions['SupplierInvoiceController.show']) ? 0 : $permissions['SupplierInvoiceController.show'],
                    'delete' => empty($permissions['SupplierInvoiceController.delete']) ? 0 : $permissions['SupplierInvoiceController.delete'],
                    'view_details' => empty($permissions['SupplierInvoiceController.view_details']) ? 0 : $permissions['SupplierInvoiceController.view_details'],
                ]
            ],

            'TaxController' => [
                'controller_name' => 'Taxes',
                'methods' => [
                    'index' => empty($permissions['TaxController.index']) ? 0 : $permissions['TaxController.index'],
                    'add' => empty($permissions['TaxController.add']) ? 0 : $permissions['TaxController.add'],
                    'edit' => empty($permissions['TaxController.edit']) ? 0 : $permissions['TaxController.edit'],
                    'show' => empty($permissions['TaxController.show']) ? 0 : $permissions['TaxController.show'],
                    'delete' => empty($permissions['TaxController.delete']) ? 0 : $permissions['TaxController.delete'],
                ]
            ],

            'VendorController' => [
                'controller_name' => 'Vendors',
                'methods' => [
                    'index' => empty($permissions['VendorController.index']) ? 0 : $permissions['VendorController.index'],
                    'add' => empty($permissions['VendorController.add']) ? 0 : $permissions['VendorController.add'],
                    'edit' => empty($permissions['VendorController.edit']) ? 0 : $permissions['VendorController.edit'],
                    'show' => empty($permissions['VendorController.show']) ? 0 : $permissions['VendorController.show'],
                    'delete' => empty($permissions['VendorController.delete']) ? 0 : $permissions['VendorController.delete'],
                ]
            ],

            // 'WaterRefillingController' => [
            //     'controller_name' => 'Water Refilling Monitoring',
            //     'methods' => [
            //         'index' => empty($permissions['WaterRefillingController.index']) ? 0 : $permissions['WaterRefillingController.index'],
            //         'add' => empty($permissions['WaterRefillingController.add']) ? 0 : $permissions['WaterRefillingController.add'],
            //         'edit' => empty($permissions['WaterRefillingController.edit']) ? 0 : $permissions['WaterRefillingController.edit'],
            //         'show' => empty($permissions['WaterRefillingController.show']) ? 0 : $permissions['WaterRefillingController.show'],
            //         'delete' => empty($permissions['WaterRefillingController.delete']) ? 0 : $permissions['WaterRefillingController.delete'],
            //         'report' => empty($permissions['WaterRefillingController.report']) ? 0 : $permissions['WaterRefillingController.report'],
            //         'reset' => empty($permissions['WaterRefillingController.reset']) ? 0 : $permissions['WaterRefillingController.reset'],
            //     ]
            // ],

            // 'WRMChangeSAPfilterAlkalineController' => [
            //     'controller_name' => 'WRM Change SAP Filter Alkaline',
            //     'methods' => [
            //         'add' => empty($permissions['WRMChangeSAPfilterAlkalineController.add']) ? 0 : $permissions['WRMChangeSAPfilterAlkalineController.add'],
            //     ]
            // ],

            // 'WRMChangeSAPfilterController' => [
            //     'controller_name' => 'WRM Change SAP Filter',
            //     'methods' => [
            //         'add' => empty($permissions['WRMChangeSAPfilterController.add']) ? 0 : $permissions['WRMChangeSAPfilterController.add'],
            //     ]
            // ],

            // 'WRMChangeSAPfilterMineralController' => [
            //     'controller_name' => 'WRM Change SAP Filter Mineral',
            //     'methods' => [
            //         'add' => empty($permissions['WRMChangeSAPfilterMineralController.add']) ? 0 : $permissions['WRMChangeSAPfilterMineralController.add'],
            //     ]
            // ],

            // 'WRMDamageBottlesController' => [
            //     'controller_name' => 'WRM Damage Bottles',
            //     'methods' => [
            //         'index' => empty($permissions['WRMDamageBottlesController.index']) ? 0 : $permissions['WRMDamageBottlesController.index'],
            //         'add' => empty($permissions['WRMDamageBottlesController.add']) ? 0 : $permissions['WRMDamageBottlesController.add'],
            //         'edit' => empty($permissions['WRMDamageBottlesController.edit']) ? 0 : $permissions['WRMDamageBottlesController.edit'],
            //         'show' => empty($permissions['WRMDamageBottlesController.show']) ? 0 : $permissions['WRMDamageBottlesController.show'],
            //         'delete' => empty($permissions['WRMDamageBottlesController.delete']) ? 0 : $permissions['WRMDamageBottlesController.delete'],
            //         'report' => empty($permissions['WRMDamageBottlesController.report']) ? 0 : $permissions['WRMDamageBottlesController.report'],
            //     ]
            // ],

            // 'WRMExpensesController' => [
            //     'controller_name' => 'WRM Expenses',
            //     'methods' => [
            //         'add' => empty($permissions['WRMExpensesController.add']) ? 0 : $permissions['WRMExpensesController.add'],
            //         'edit' => empty($permissions['WRMExpensesController.edit']) ? 0 : $permissions['WRMExpensesController.edit'],
            //         'show' => empty($permissions['WRMExpensesController.show']) ? 0 : $permissions['WRMExpensesController.show'],
            //         'delete' => empty($permissions['WRMExpensesController.delete']) ? 0 : $permissions['WRMExpensesController.delete'],
            //         'view_details' => empty($permissions['WRMExpensesController.view_details']) ? 0 : $permissions['WRMExpensesController.view_details'],
            //     ]
            // ],

            // 'WRMissuedBottlesController' => [
            //     'controller_name' => 'WRM Issued Bottles',
            //     'methods' => [
            //         'index' => empty($permissions['WRMissuedBottlesController.index']) ? 0 : $permissions['WRMissuedBottlesController.index'],
            //         'view_details' => empty($permissions['WRMissuedBottlesController.view_details']) ? 0 : $permissions['WRMissuedBottlesController.view_details'],
            //     ]
            // ],

            // 'WRMOriginalBottlesController' => [
            //     'controller_name' => 'WRM Original Bottles',
            //     'methods' => [
            //         'add' => empty($permissions['WRMOriginalBottlesController.add']) ? 0 : $permissions['WRMOriginalBottlesController.add'],
            //         'edit' => empty($permissions['WRMOriginalBottlesController.edit']) ? 0 : $permissions['WRMOriginalBottlesController.edit'],
            //         'delete' => empty($permissions['WRMOriginalBottlesController.delete']) ? 0 : $permissions['WRMOriginalBottlesController.delete'],
            //         'view_details' => empty($permissions['WRMOriginalBottlesController.view_details']) ? 0 : $permissions['WRMOriginalBottlesController.view_details'],
            //     ]
            // ],

            // 'WRMRegenerateSettingsController' => [
            //     'controller_name' => 'WRM Regenerate Settings',
            //     'methods' => [
            //         'add' => empty($permissions['WRMRegenerateSettingsController.add']) ? 0 : $permissions['WRMRegenerateSettingsController.add'],
            //     ]
            // ],
            
            'ConsMonSystemAccountReceivableController' => [
                'controller_name' => 'CMS Account Receivable',
                'methods' => [
                    'index' => empty($permissions['ConsMonSystemAccountReceivableController.index']) ? 0 : $permissions['ConsMonSystemAccountReceivableController.index'],
                    'add' => empty($permissions['ConsMonSystemAccountReceivableController.add']) ? 0 : $permissions['ConsMonSystemAccountReceivableController.add'],
                    'edit' => empty($permissions['ConsMonSystemAccountReceivableController.edit']) ? 0 : $permissions['ConsMonSystemAccountReceivableController.edit'],
                    'delete' => empty($permissions['ConsMonSystemAccountReceivableController.delete']) ? 0 : $permissions['ConsMonSystemAccountReceivableController.delete'],
                ]
            ],

            'ConsMonSystemBillingController' => [
                'controller_name' => 'CMS Billing',
                'methods' => [
                    'index' => empty($permissions['ConsMonSystemBillingController.index']) ? 0 : $permissions['ConsMonSystemBillingController.index'],
                    'add' => empty($permissions['ConsMonSystemBillingController.add']) ? 0 : $permissions['ConsMonSystemBillingController.add'],
                    'edit' => empty($permissions['ConsMonSystemBillingController.edit']) ? 0 : $permissions['ConsMonSystemBillingController.edit'],
                    'delete' => empty($permissions['ConsMonSystemBillingController.delete']) ? 0 : $permissions['ConsMonSystemBillingController.delete'],
                ]
            ],

            'ConsMonSystemController' => [
                'controller_name' => 'CMS Project & Additional Work',
                'methods' => [
                    'index' => empty($permissions['ConsMonSystemController.index']) ? 0 : $permissions['ConsMonSystemController.index'],
                    'add' => empty($permissions['ConsMonSystemController.add']) ? 0 : $permissions['ConsMonSystemController.add'],
                    'edit' => empty($permissions['ConsMonSystemController.edit']) ? 0 : $permissions['ConsMonSystemController.edit'],
                    'delete' => empty($permissions['ConsMonSystemController.delete']) ? 0 : $permissions['ConsMonSystemController.delete'],
                ]
            ],

            'ConsMonSystemExpensesController' => [
                'controller_name' => 'CMS Expenses',
                'methods' => [
                    'index' => empty($permissions['ConsMonSystemExpensesController.index']) ? 0 : $permissions['ConsMonSystemExpensesController.index'],
                    'add' => empty($permissions['ConsMonSystemExpensesController.add']) ? 0 : $permissions['ConsMonSystemExpensesController.add'],
                    'edit' => empty($permissions['ConsMonSystemExpensesController.edit']) ? 0 : $permissions['ConsMonSystemExpensesController.edit'],
                    'delete' => empty($permissions['ConsMonSystemExpensesController.delete']) ? 0 : $permissions['ConsMonSystemExpensesController.delete'],
                    'view_details' => empty($permissions['ConsMonSystemExpensesController.view_details']) ? 0 : $permissions['ConsMonSystemExpensesController.view_details'],
                ]
            ],

            'ConsMonSystemReportController' => [
                'controller_name' => 'CMS Report',
                'methods' => [
                    'index' => empty($permissions['ConsMonSystemReportController.index']) ? 0 : $permissions['ConsMonSystemReportController.index'],
                    'view_details' => empty($permissions['ConsMonSystemReportController.view_details']) ? 0 : $permissions['ConsMonSystemReportController.view_details'],
                ]
            ],

        ];

        return $controllers;
    }
}