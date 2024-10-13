<div class="xp-navbar">
    <ul class="menu">
        <li class="menu-item">
            <a href="/">Dashboard</a>
        </li>
        <li class="menu-item"> <a href="/sales">Sales</a>
            <ul class="submenu">
                <li class="submenu-item"><a href="/sales/add">Create</a>
                    <ul class="childmenu">
                        <li class="childmenu-item"><a href="/sales/add">Sale Invoice</a></li>
                        <li class="childmenu-item"><a href="/sales/return">Sale Return</a></li>
                    </ul>
                </li>
                <li class="submenu-item"><a href="/sales">List</a>
                    <ul class="childmenu">
                        <li class="childmenu-item"><a href="/sales/">Sale Invoices</a></li>
                        <li class="childmenu-item"><a href="/sales/returns">Sale Returns</a></li>
                    </ul>
                </li>
                <li class="submenu-item"><a href="/reports/sales-report">Report</a>
                    <ul class="childmenu">
                        <li class="childmenu-item"><a href="/reports/sales-report">Sale Report</a></li>
                        <li class="childmenu-item"><a href="/sales/returns">Sale Returns</a></li>
                        <li class="childmenu-item"><a href="/reports/sales-detail-report">Detail Report </a></li>
                        <li class="childmenu-item"><a href="/reports/sales-report?filter_deleted=true">Report (Deleted) </a></li>
                    </ul>
                </li>
                <li class="submenu-item"></li>
                {{-- <li class="submenu-item"><a href="/reports/sales-summary-report">Summary Report </a></li> --}}
            </ul>
        </li>
        <li class="menu-item"><a href="/purchase">Purchase</a> 
            <ul class="submenu">
                <li class="submenu-item">Create
                    <ul class="childmenu">
                        <li class="childmenu-item"><a href="/purchase/invoice/0/create">Purchase Invoice</a></li>
                        <li class="childmenu-item"><a href="/purchase/return">Purchase Return </a></li>
                    </ul>
                </li>
                <li class="submenu-item"><a href="/purchase/invoice">List</a>
                    <ul class="childmenu">
                        <li class="childmenu-item"><a href="/purchase/invoice/">Purchase Invoices</a></li>
                        <li class="childmenu-item"><a href="/purchase/returns">Purchase Returns</a></li>
                    </ul>
                </li>
                <li class="submenu-item"><a href="/reports/purchase-report">Report</a>
                    <ul class="childmenu">
                        <li class="childmenu-item"><a href="/reports/purchase-report">Purchase Report</a></li>
                        <li class="childmenu-item"><a href="/purchase/returns">Purchase Returns</a></li>
                        <li class="childmenu-item"><a href="/reports/purchase-detail-report">Purchase Detail Report</a></li>
                    </ul>
                </li>
            </ul>
        </li>

        <li class="menu-item">Accounting
            <ul class="submenu">
                <li class="submenu-item"><a href="/account">Accounts</a></li>
                <li class="submenu-item"><a href="/account/journal">New Transactions</a></li>
                <li class="submenu-item"><a href="/account/transactions">Transaction</a></li>
                <li class="submenu-item"><a href="/account/report/trial-balance">Financial Report</a></li>
                <li class="submenu-item"><a href="/account/report/general-ledger">Ledger Report</a></li>
            </ul>
        </li>

        <li class="menu-item">Reporting
            <ul class="submenu">
                <li class="submenu-item"><a href="/reports/inventory-report">Inventory Balance Report</a></li>
                <li class="submenu-item"><a href="/reports/sales-report">Sale Report</a></li>
                <li class="submenu-item"><a href="/sales/returns">Sale Return Report</a></li>
                <li class="submenu-item"><a href="/reports/sales-detail-report">Sale Detail Report</a></li>
                <li class="submenu-item"><a href="/reports/purchase-report">Purchase Report</a></li>
                <li class="submenu-item"><a href="/reports/purchase-detail-report">Purchase Detail Report</a></li>
                <li class="submenu-item"><a href="/account/report/trial-balance">Financial Report</a></li>
                <li class="submenu-item"><a href="/account/report/general-ledger">Ledger Report</a></li>
            </ul>
        </li>


        <li class="menu-item">
            <a href="/parties">Parties</a>
        </li>

        <li class="menu-item"> <a href="/products">Products</a>
            <ul class="submenu">
                <li class="submenu-item"><a href="/products">Products</a></li>
                <li class="submenu-item"><a href="/fields">Categories</a></li>
                <li class="submenu-item"><a href="/product-category">Sub Categories</a></li>
                <li class="submenu-item"><a href="/uom">UOM</a></li>
            </ul>
        </li>

        <li class="menu-item"><a href="/system/configurations">Settings</a></li>
        <li class="menu-item bg-logout"><a href="{{route('auth.logout')}}">Logout</a></li>
        <li class="menu-item">
            <a class="" target="_blank" href="https://wa.me/03200681969?text=Hello,%20%0AName:%20{{Auth::check() ? Auth::user()->name : ''}}%0AStore:%20{{Auth::check() && isset(Auth::user()->store)? Auth::user()->store->store_name : ''}}%0AUser%20Role:%20{{Auth::check() && isset(Auth::user()->userroles->role_name) ? Auth::user()->userroles->role_name : ''}}%0AI%20need%20help%20regarding...."  type="button">Need Help?</a>
        </li>
    </ul>
</div>


<style>

.xp-navbar {
    background-color: #9ec200 ;
    /* padding: 5px; */
    border-bottom: 1px solid #b4b4b4;
    /* box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.1); */
    position: sticky;
    top: 0;
    left: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;

    /* box-shadow: 2px  2px 10px gray; */
    margin-bottom: 20px
}

.menu {
    list-style: none;
    display: flex;
    margin-bottom: 0
}

.menu-item {
    position: relative; /* Important for submenu positioning */
    padding: 5px 15px;
    /* margin-right: 15px; */
    background-color: white;
    border: 1px solid transparent;
    cursor: pointer;
    border-right: 1px solid gray;
    border-left: 1px solid gray;
    transition: background-color 0.3s, border-color 0.3s;
    color: #9ec200;
    /* border-radius: 5px; */
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
}


.menu-item > a {
    color: #9ec200
}

/* .menu-item:hover {
    background-color: #d0d0d0;
    border-color: #8b8b8b;
} */

.menu-item:active {
    background-color: #c0c0c0;
    border-color: #7a7a7a;
}

.submenu {
    display: none; /* Hide submenu by default */
    position: absolute;
    top: 100%; /* Position submenu below the parent */
    left: 0;
    background-color: #e4e4e4;
    border: 1px solid #8b8b8b;
    padding: 0;
    list-style: none;
    z-index: 1000;
    transition: .2s
}

.submenu-item {
    padding: 5px 15px;
    white-space: nowrap; /* Prevent breaking */
    cursor: pointer;
    transition: background-color 0.3s;
}

.submenu-item:hover {
    background-color: #d0d0d0;
}

.menu-item:hover .submenu {
    display: block; /* Show submenu on hover */
}

.menu-item:hover, .menu-active{
    background: #9ec200;
    color: white;
    box-shadow:  0px 0px 20px #8b8b8b
}

.menu-item:hover > a{
    color: white
}


.bg-logout{
    background: red;
    color: white
}

.bg-logout >a {
    color: white
}
.bg-logout:hover{
    background: rgb(216, 44, 44)
}


/* Refactoring */


ul.childmenu{
    list-style: none;
    display: none;
    position: absolute;
    top: 20%;
    left: 100%;
    padding: 0;
    min-width: 150px;
    background: #cecece;
    border: solid 1px #9ec200;
    transition: 0.5s;

}

.submenu-item{
    color: #344767
}

.childmenu-item{
    background: #cecece;
    padding:5px 10px;
}
.childmenu-item:hover{
    background: #ececec;
}

.submenu-item{
    position: relative;
}

.submenu-item:hover > .childmenu{
    display: inline;
    /* height: fit-content; */
}


</style>