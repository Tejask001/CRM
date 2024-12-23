<?php
require('fpdf/fpdf.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "amba_associats";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the order ID from the URL
$order_id = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch all order items for the given order ID
$sql = "
    SELECT  
        orders.order_id,
        orders.date,
        order_items.batch_code,
        order_items.quantity,
        orders.type,
        orders.client_id,
        orders.supplier_id,
        orders.payment_method,
        order_items.discount,
        order_items.freight,
        order_items.cgst,
        order_items.sgst,
        order_items.igst,
        order_items.billing_amount,
        CONCAT_WS(' ', client.comp_first_name, client.comp_middle_name, client.comp_last_name) AS company_name,
        client.comp_address,
        client.gst_no,
        client.manager_phone,
        product.general_name,
        product.chemical_size,
        product.pp,
        product.sp
    FROM 
        orders
    LEFT JOIN order_items ON orders.order_id = order_items.order_id
    LEFT JOIN client ON orders.client_id = client.id
    LEFT JOIN product ON order_items.batch_code = product.batch_code
    WHERE orders.order_id = '$order_id'
";


$result = $conn->query($sql);
if ($result->num_rows === 0) {
    die("No order found with ID $order_id");
}

// Fetch the first result
$order = $result->fetch_assoc();
if (!$order) {
    die("Order data is unavailable.");
}

// Initialize variables
$grand_total = 0;
$subtotal = 0;
$total_tax_cgst = 0;
$total_tax_sgst = 0;
$total_tax_igst = 0;
$item_number = 1;

// Initialize FPDF with Landscape orientation
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape mode
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Invoice Header
$pdf->Cell(270, 10, 'Invoice', 1, 1, 'C'); // Adjust the width to fit the page in landscape
$pdf->Ln(10);

// Client Details
$order_date = new DateTime($order['date']);
// $pdf->SetFont('Arial', 'B', 12);
// $pdf->Cell(135, 10, 'Customer Details ', 0, 1);
// $pdf->Cell(135, 10, 'Company Name: ' . ($order['company_name'] ?? 'N/A'), 0, 1);
// $pdf->Cell(135, 10, 'GST No: ' . ($order['gst_no'] ?? 'N/A'), 0, 1);
// $pdf->Cell(135, 10, 'Company Address: ' . ($order['comp_address'] ?? 'N/A'), 0, 1);
// $pdf->Cell(135, 10, 'Manager Phone: +91-' . ($order['manager_phone'] ?? 'N/A'), 0, 1);
// $pdf->Cell(135, 10, 'Order Date: ' . ($order_date ? $order_date->format('d/m/Y') : 'N/A'), 0, 1);
// $pdf->Ln(10);

// Client and Company Details (Two Columns)
$pdf->SetFont('Arial', 'B', 12);

// Left Column: Customer Details
$pdf->Cell(135, 10, 'Customer Details', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);

// Right Column: Company Details
$pdf->Cell(135, 10, 'Dealer Details', 0, 1, 'R');

// Left Column Content: Customer Details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(135, 10, 'Company Name: ' . ($order['company_name'] ?? 'N/A'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);

// Right Column Content: Company Details
$pdf->Cell(135, 10, 'Company Name: Amba Associats', 0, 1, 'R');

$pdf->Cell(135, 10, 'GST No: ' . ($order['gst_no'] ?? 'N/A'), 0, 0, 'L');
$pdf->Cell(135, 10, 'GST No: 12345678909876', 0, 1, 'R');

$pdf->Cell(135, 10, 'Company Address: ' . ($order['comp_address'] ?? 'N/A'), 0, 0, 'L');
$pdf->Cell(135, 10, 'Address: Faridabad', 0, 1, 'R');

$pdf->Cell(135, 10, 'Manager Phone: +91-' . ($order['manager_phone'] ?? 'N/A'), 0, 0, 'L');
$pdf->Cell(135, 10, 'Phone: +91-9876543210', 0, 1, 'R');

$pdf->Cell(135, 10, 'Order Date: ' . ($order_date ? $order_date->format('d/m/Y') : 'N/A'), 0, 0, 'L');
$pdf->Cell(135, 10, '', 0, 1, 'R'); // Empty right cell to align layout

$pdf->Cell(135, 10, 'Payment Method: ' . ($order['payment_method'] ?? 'N/A'), 0, 0, 'L');


$pdf->Ln(15); // Add spacing after details


// Table: Order Details (Header)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, 'Item No', 1, 0, 'C');
$pdf->Cell(50, 10, 'Item Name', 1, 0, 'C');
// $pdf->Cell(30, 10, 'Batch Code', 1, 0, 'C');
$pdf->Cell(25, 10, 'Price/Unit', 1, 0, 'C');
$pdf->Cell(20, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(20, 10, 'Discount', 1, 0, 'C');
$pdf->Cell(25, 10, 'Freight', 1, 0, 'C');
// $pdf->Cell(25, 10, 'Subtotal', 1, 0, 'C');
$pdf->Cell(25, 10, 'CGST', 1, 0, 'C');
$pdf->Cell(25, 10, 'SGST', 1, 0, 'C');
$pdf->Cell(25, 10, 'IGST', 1, 0, 'C');
$pdf->Cell(40, 10, 'Subtotal After Tax', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

$freight_charge = $order['freight'] ?? 0;

// Loop through all items
do {
    $quantity = $order['quantity'] ?? 0;
    $price = $order['sp'] ?? 0;
    $discount = $order['discount'] ?? 0;
    $cgst = $order['cgst'] ?? 0;
    $sgst = $order['sgst'] ?? 0;
    $igst = $order['igst'] ?? 0;

    $item_total = $quantity * $price;
    $discount_amount = ($item_total * $discount) / 100;
    $subtotal_item = $item_total - $discount_amount + $freight;

    $subtotal_after_tax = $subtotal_item + $cgst + $sgst + $igst;

    $total_tax_cgst += $cgst;
    $total_tax_sgst += $sgst;
    $total_tax_igst += $igst;
    $subtotal += $subtotal_item;

    $pdf->Cell(20, 10, $item_number++, 1, 0, 'C');
    $pdf->Cell(50, 10, $order['general_name'] ?? 'N/A', 1, 0, 'C');
    // $pdf->Cell(30, 10, $order['batch_code'] ?? 'N/A', 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($price, 2), 1, 0, 'C');
    $pdf->Cell(20, 10, $quantity, 1, 0, 'C');
    $pdf->Cell(20, 10, number_format($discount, 2) . '%', 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($freight_charge, 2), 1, 0, 'C');
    // $pdf->Cell(25, 10, number_format($subtotal_item, 2), 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($cgst, 2), 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($sgst, 2), 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($igst, 2), 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($subtotal_after_tax, 2), 1, 1, 'C');
} while ($order = $result->fetch_assoc());

// Calculate Grand Total
$grand_total = $subtotal + $total_tax_cgst + $total_tax_sgst + $total_tax_igst;
$pdf->Cell(275, 10, 'Grand Total: INR ' . number_format($grand_total, 2), 1, 1, 'R');

// Convert Grand Total to Words
function convertNumberToWords($num)
{
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return ucfirst($f->format($num));
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 12);
$pdf->MultiCell(200, 10, 'Amount in Words: ' . convertNumberToWords($grand_total) . ' only', 0, 'L');

// Output PDF
ob_clean(); // Clear output buffer to avoid output issues
$pdf->Output('I', 'Invoice_' . ($order_id ?? 'unknown') . '.pdf');
