<!-- Sidebar -->
<div id="sidebar" class="col-2 d-flex flex-column bg-dark text-white vh-100 p-0">
    <div class="text-center py-3">
        <img src="https://via.placeholder.com/80" alt="Logo" class="img-fluid rounded-circle mb-2">
        <h4>Amba Associats</h4>
    </div>
    <ul class="list-group list-group-flush">
        <a href="supplier.php">
            <li class="list-group-item <?php echo basename($_SERVER['PHP_SELF']) == 'supplier.php' ? 'active' : ''; ?>">
                Suppliers
            </li>
        </a>
        <a href="client.php">
            <li class="list-group-item <?php echo basename($_SERVER['PHP_SELF']) == 'client.php' ? 'active' : ''; ?>">
                Clients
            </li>
        </a>
        <a href="product.php">
            <li class="list-group-item <?php echo basename($_SERVER['PHP_SELF']) == 'product.php' ? 'active' : ''; ?>">
                Products
            </li>
        </a>
        <a href="orders.php">
            <li class="list-group-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                Orders
            </li>
        </a>
    </ul>
</div>