<?php //echo Request::segment(1); die; ?>
<?php //echo Route::current()->getActionName(); die; ?>
<?php //echo Route::getCurrentRoute()->getActionName(); die; ?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
            @if(isset(Auth::user()->avatar) && Auth::user()->avatar) 
                <img src="{{ asset('/uploads/images/' . Auth::user()->avatar ) }}" class="img-circle" alt="User Avatar" /> 
            @else
                <img src="{{ asset('/uploads/images/no-avatar.png') }}" class="img-circle" alt="User Avatar" /> 
            @endif
            </div>
            <div class="pull-left info">
                <p>@if(isset(Auth::user()->name)){{ Auth::user()->name }}@else Name @endif</p>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="{{ add_active_class(Request::segment(1), 'home') }}">
                <a href="{{ url('/') }}"><span>Home</span></a>
            </li>

            <?php
            // User role permission start here
            $user = auth()->user();
            if($user->role_id == 1){?>
            <li class="treeview {{ add_active_class(Request::segment(1), ['manage-roles', 'user']) }}">
                <a href="#"><span>User & Security</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="{{ add_active_class(Request::segment(1), 'user') }}">
                        <a href="{{ url('/user') }}"><span>Manage Users</span></a>
                    </li>
                    <li class="{{ add_active_class(Request::segment(1), 'manage-roles') }}">
                        <a href="{{ url('/manage-roles') }}"><span>Manage Roles</span></a>
                    </li>
                </ul>
            </li>
            <?php }?>
            

            <?php
                // User role permission
                if(!auth()->user()->canAccess('CompanyController.index')){
                }else{ ?>
                <li class="{{ add_active_class(Request::segment(1), 'company') }}">
                    <a href="{{ url('/company') }}"><span>Company</span></a>
                </li>
            <?php }?>
            
            {{-- <li class="{{ add_active_class(Request::segment(1), 'user') }}">
                <a href="{{ url('/user') }}"><span>Users</span></a>
            </li> --}}
            <?php /* Parent Menu -> Vendors */ ?>
            <li class="treeview {{ add_active_class(Request::segment(1), ['vendors', 'supplier-invoice', 'cash-payment-voucher']) }}">
                <a href="javascript:;"><span>Vendor & etc.</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php
                    // User role permission
                    if(!auth()->user()->canAccess('VendorController.index')){
                    }else{ ?>
                    <li class="{{ add_active_class(Request::segment(1), 'vendors') }}">
                        <a href="{{ url('/vendors') }}"><span>Vendors</span></a>
                    </li>
                    <?php }?>

                    <?php
                    // User role permission
                    if(!auth()->user()->canAccess('SupplierInvoiceController.index')){
                    }else{ ?>
                        <li class="{{ add_active_class(Request::segment(1), 'supplier-invoice') }}">
                            <a href="{{ url('/supplier-invoice') }}"><span>Enter Bills</span></a>
                        </li>
                    <?php }?>
                    
                    <?php
                    // User role permission
                    if(!auth()->user()->canAccess('CashPaymentVoucherController.index')){
                    }else{ ?>
                        <li class="{{ add_active_class(Request::segment(1), 'cash-payment-voucher') }}">
                            <a href="{{ url('/cash-payment-voucher') }}"><span>Cash Payment Voucher</span></a>
                        </li>
                    <?php }?>
                </ul>
            </li>
            <?php /* Parent Menu -> Customers */ ?>
            <li class="treeview {{ add_active_class(Request::segment(1), ['customer', 'product-list', 'product', 'cash-invoice', 'credit-invoice', 'collection-receipt', 'open-invoice', 'service-list', 'billing-invoice', 'official-receipt']) }}">
                <a href="javascript:;"><span>Customer & etc.</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php
                    // User role permission
                    if(!auth()->user()->canAccess('CustomerCRUDController.index')){
                    }else{ ?>
                    <li class="{{ add_active_class(Request::segment(1), 'customer') }}">
                        <a href="{{ url('/customer') }}"><span>Customers</span></a>
                    </li>
                    <?php }?>

                    <li class="{{ add_active_class(Request::segment(1), ['product', 'cash-invoice', 'credit-invoice', 'collection-receipt', 'open-invoice']) }}">
                        <a href="javascript:;"><span>Trading</span></a>
                        <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('ProductController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'product') }}"><a href="{{ url('/product') }}"><span>Product List</span></a></li>
                            <?php }?>

                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('CashInvoiceController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'cash-invoice') }}"><a href="{{ url('/cash-invoice') }}"><span>Cash Invoice</span></a></li>
                            <?php }?>

                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('CreditInvoiceController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'credit-invoice') }}"><a href="{{ url('/credit-invoice') }}"><span>Credit Invoice</span></a></li>
                            <?php }?>

                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('CollectionReceiptController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'collection-receipt') }}"><a href="{{ url('/collection-receipt') }}"><span>Collection Receipt</span></a></li>
                            <?php }?>

                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('OpenInvoiceController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'open-invoice') }}"><a href="{{ url('/open-invoice') }}"><span>Open Invoice</span></a></li>
                            <?php }?>
                        </ul>
                    </li>
                    <li class="{{ add_active_class(Request::segment(1), ['service-list', 'billing-invoice', 'official-receipt']) }}">
                        <a href="javascript:;"><span>Services</span></a>
                        <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('ServiceCRUDController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'service-list') }}"><a href="{{ url('/service-list') }}"><span>List of Services</span></a></li>
                            <?php }?>

                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('BillingInvoiceController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'billing-invoice') }}"><a href="{{ url('/billing-invoice') }}"><span>Billing Invoice</span></a></li>
                            <?php }?>

                            <?php
                            // User role permission
                            if(!auth()->user()->canAccess('OfficialReceiptController.index')){
                            }else{ ?>
                                <li class="{{ add_active_class(Request::segment(1), 'official-receipt') }}"><a href="{{ url('/official-receipt') }}"><span>Official Receipt</span></a></li>
                            <?php }?>
                        </ul>
                    </li>
                </ul>
            </li>
            <?php /* Parent Menu -> Employee */ ?>
            <li class="treeview {{ add_active_class(Request::segment(1), ['employee', 'list-employees', 'payroll', 'cash-advance', 'daily-time-record', 'edit-password']) }}">
                <a href="javascript:;"><span>Employee & etc.</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php
                    // User role permission
                    if(!auth()->user()->canAccess('EmployeeCRUDController.index')){
                    }else{ ?>
                    <li class="{{ add_active_class(Request::segment(1), 'list-employees') }}"><a href="{{ url('/list-employees') }}"><span>List of employees</span></a></li>
                    <?php }?>
                    
                    <?php
                    // User role permission
                    if(!auth()->user()->canAccess('CashAdvanceController.index')){
                    }else{ ?>
                        <li class="{{ add_active_class(Request::segment(1), ['payroll','cash-advance']) }}">
                            <a href="{{ url('/payroll') }}"><span>Payroll</span></a>
                            <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li class="{{ add_active_class(Request::segment(1), 'cash-advance') }}"><a href="{{ url('/cash-advance') }}"><span>Cash Advance</span></a></li>
                            </ul>
                        </li>
                    <?php }?>

                    <?php
                    // User role permission
                    if(!auth()->user()->canAccess('DTRController.index')){
                    }else{ ?>
                        <li class="{{ add_active_class(Request::segment(1), ['daily-time-record','edit-password','create-password','dtr-account-list','absent']) }}">
                            <a href="{{ url('/daily-time-record') }}"><span>Daily Time Record</span></a>
                            <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li class="{{ add_active_class($route_uri, 'daily-time-record/dtr-account-list') }}"><a href="{{ url('daily-time-record/dtr-account-list') }}"><span>DTR Accounts</span></a></li>
                                <li class="{{ add_active_class($route_uri, 'daily-time-record/create-password') }}"><a href="{{ url('daily-time-record/create-password') }}"><span>Create time in access</span></a></li>
                                <li class="{{ add_active_class($route_uri, 'daily-time-record/edit-password') }}"><a href="{{ url('daily-time-record/edit-password') }}"><span>Edit time in access</span></a></li>
                                <li class="{{ add_active_class($route_uri, 'time-in-time-out') }}"><a href="{{ url('time-in-time-out') }}" target="_blank"><span>Time In/Out</span></a></li>
                                <li class="{{ add_active_class($route_uri, 'daily-time-record/absent') }}"><a href="{{ url('daily-time-record/absent') }}"><span>Absent Entry</span></a></li>
                                <li class="{{ add_active_class($route_uri, 'daily-time-record/qr-code') }}"><a href="{{ url('daily-time-record/qr-code') }}"><span>Generate QR Code</span></a></li>
                            </ul>
                        </li>
                    <?php }?>
                </ul>
            </li>
            <?php /* Parent Menu -> Chart of Acct. */ ?>
            <li class="treeview {{ add_active_class(Request::segment(1), ['chart-account', 'chart-account-type', 'sub-account-type']) }}">
                <?php
                // User role permission
                if(!auth()->user()->canAccess('ChartAccountController.index')){
                }else{ ?>
                    <a href="{{ url('/chart-account') }}"><span>Chart of Acct.</span></a>
                    <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                        <li class="{{ add_active_class(Request::segment(1), 'chart-account-type') }}"><a href="{{ url('/chart-account-type') }}"><span>Account Type</span></a></li>
                        <li class="{{ add_active_class(Request::segment(1), 'sub-account-type') }}"><a href="{{ url('/sub-account-type') }}"><span>Sub Account Type</span></a></li>
                    </ul>
                <?php }?>
            </li>

            <?php
            // User role permission
            if(!auth()->user()->canAccess('TaxController.index')){
            }else{ ?>
                <li class="{{ add_active_class(Request::segment(1), 'tax') }}"><a href="{{ url('/tax') }}"><span>Tax Settings</span></a></li>
            <?php }?>
            
            <?php
            // User role permission
            if(!auth()->user()->canAccess('DiscountController.index')){
            }else{ ?>
                <li class="{{ add_active_class(Request::segment(1), 'discount') }}"><a href="{{ url('/discount') }}"><span>Discount Settings</span></a></li>
            <?php }?>

            <?php /* Parent Menu -> Journal */ ?>
            <li class="treeview {{ add_active_class(Request::segment(1), ['journal', 'adjusting', 'sub-account-type']) }}">

                <?php
                // User role permission
                if(!auth()->user()->canAccess('JournalController.index')){
                }else{ ?>
                    <a href="{{ url('/journal') }}"><span>Journal</span></a>
                    <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                        <li class="{{ add_active_class(Request::segment(1), 'adjusting') }}"><a href="{{ url('/adjusting') }}"><span>Adjusting</span></a></li>
                    </ul>
                <?php }?> 
            </li>

            <?php
            // User role permission
            if(!auth()->user()->canAccess('LedgerController.index')){
            }else{ ?>
                <li class="{{ add_active_class(Request::segment(1), 'ledger') }}"><a href="{{ url('/ledger') }}"><span>Ledger</span></a></li>
            <?php }?>

            <?php
            // User role permission
            if(!auth()->user()->canAccess('ReportController.index')){
            }else{ ?>
                <li class="{{ add_active_class(Request::segment(1), 'report') }}"><a href="{{ url('/report') }}"><span>Reports</span></a></li>
            <?php }?>

            <?php /* Parent Menu -> Advanced */ ?>
            <li class="treeview {{ add_active_class(Request::segment(1), ['water-refilling-monitoring','construction-monitoring-system','reports','inventory','point-of-sale','construction-monitoring-expenses','construction-monitoring-report','construction-monitoring-billing','construction-monitoring-ar']) }}">
                <a href="#"><span>Advanced</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="{{ add_active_class(Request::segment(1), ['water-refilling-monitoring','reports','inventory']) }}">
                        <?php
                        // User role permission
                        if(!auth()->user()->canAccess('WaterRefillingController.index')){
                        }else{ ?>
                            <a href="{{ url('/water-refilling-monitoring') }}"><span>Water Refilling Monitoring</span></a>
                            <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li class="{{ add_active_class($route_uri, 'water-refilling-monitoring/create') }}"><a href="{{ url('water-refilling-monitoring/create') }}"><span>Add new Entry</span></a></li>
                                <li class="{{ add_active_class($route_uri, 'water-refilling-monitoring/reports') }}"><a href="{{ url('water-refilling-monitoring/reports') }}"><span>Reports</span></a></li>
                                <li class="{{ add_active_class(Request::segment(1), 'inventory') }}"><a href="{{ url('/inventory') }}"><span>Inventory</span></a></li>
                            </ul>
                        <?php }?>
                    </li>
                </ul>
                <ul class="treeview-menu">
                    <li class="{{ add_active_class(Request::segment(1), ['point-of-sale','reports','sales-reports','inventory']) }}">
                        <?php
                        // User role permission
                        if(!auth()->user()->canAccess('POSController.index')){
                        }else{ ?>
                            <a href="{{ url('/point-of-sale') }}"><span>Point Of Sale</span></a>
                            <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li class="{{ add_active_class($route_uri, 'point-of-sale/create') }}"><a href="{{ url('point-of-sale/create') }}"><span>Sales Entry</span></a></li>
                                <li class="treeview {{ add_active_class(Request::segment(1), ['point-of-sale','reports','sales-reports']) }}">
                                    <a href="javascript:;"><span>POS Reports</span></a>
                                    <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li class="{{ add_active_class($route_uri, 'point-of-sale/reports') }}"><a href="{{ url('point-of-sale/reports') }}"><span>Summary Reports</span></a></li>
                                        <li class="{{ add_active_class($route_uri, 'point-of-sale/sales-reports') }}"><a href="{{ url('point-of-sale/sales-reports') }}"><span>Sales Reports</span></a></li>
                                    </ul>
                                </li>
                                <li class="{{ add_active_class($route_uri, 'point-of-sale/inventory') }}"><a href="{{ url('point-of-sale/inventory') }}"><span>Stocks Inventory</span></a></li>
                            </ul>
                        <?php }?> 
                    </li>
                </ul>
            </li>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>