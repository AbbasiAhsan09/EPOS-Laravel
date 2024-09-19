<div class="xp-navbar">
    <ul class="menu">
        <li class="menu-item">
            <a href="/">Dashboard</a>
        </li>
        <li class="menu-item"> <a href="/sales">Sales</a>
            <ul class="submenu">
                <li class="submenu-item"><a href="/sales/add">New</a></li>
                <li class="submenu-item"><a href="/sales">List</a></li>
                <li class="submenu-item"><a href="/reports/sales-report">Report</a></li>
                <li class="submenu-item"><a href="/reports/sales-detail-report">Detail Report </a></li>
                <li class="submenu-item"><a href="/reports/sales-summary-report">Summary Report </a></li>
                <li class="submenu-item"><a href="/reports/sales-report?filter_deleted=true">Report (Deleted) </a></li>
            </ul>
        </li>
        <li class="menu-item">Purchase
            <ul class="submenu">
                <li class="submenu-item">New Purchase</li>
                <li class="submenu-item">Zoom Out</li>
                <li class="submenu-item">Full Screen</li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="/parties">Parties</a>
        </li>

        <li class="menu-item">Products
            <ul class="submenu">
                <li class="submenu-item">List</li>
                <li class="submenu-item">Fields</li>
                <li class="submenu-item">Categories</li>
            </ul>
        </li>

        <li class="menu-item">Favorites</li>
        <li class="menu-item">Tools</li>
        <li class="menu-item">Help</li>
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

.menu-item:hover {
    background-color: #d0d0d0;
    border-color: #8b8b8b;
}

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

</style>