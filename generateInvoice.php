<?php
require('fpdf/fpdf.php');

// Turn off error reporting for the user-facing page to avoid output before PDF generation
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // Disable notices and warnings
ini_set('display_errors', 0); // Don't show errors

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
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch all order items for the given order ID
$sql = "
    SELECT  
        orders.order_id,
        orders.date,
        orders.batch_code,
        orders.quantity,
        orders.type,
        orders.client_id,
        orders.supplier_id,
        orders.discount,
        orders.freight,
        orders.cgst,
        orders.sgst,
        orders.igst,
        orders.billing_amount,
        CONCAT_WS(' ', client.comp_first_name, client.comp_middle_name, client.comp_last_name) AS company_name,
        client.gst_no,
        product.general_name,
        product.chemical_size,
        product.pp,
        product.sp
    FROM 
        orders
    LEFT JOIN client ON orders.client_id = client.id
    LEFT JOIN product ON orders.batch_code = product.batch_code
    WHERE orders.order_id = $order_id
";

$result = $conn->query($sql);
if ($result->num_rows === 0) {
    die("No order found with ID $order_id");
}

$order = $result->fetch_assoc();

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
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(135, 10, 'Company Name: ' . (isset($order['company_name']) ? $order['company_name'] : 'N/A'), 0, 0);
$pdf->Cell(135, 10, 'GST No: ' . (isset($order['gst_no']) ? $order['gst_no'] : 'N/A'), 0, 1);
$pdf->Cell(135, 10, 'Order Date: ' . (isset($order['date']) ? $order_date->format('d/m/Y') : 'N/A'), 0, 1);
$pdf->Ln(10);

// Table: Order Details (Header)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, 'Item No', 1, 0, 'C');
$pdf->Cell(50, 10, 'Item Name', 1, 0, 'C');
$pdf->Cell(30, 10, 'Batch Code', 1, 0, 'C');
$pdf->Cell(25, 10, 'Price/Unit', 1, 0, 'C');
$pdf->Cell(20, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(20, 10, 'Discount', 1, 0, 'C');
$pdf->Cell(25, 10, 'Subtotal', 1, 0, 'C');
$pdf->Cell(25, 10, 'Tax', 1, 0, 'C');
$pdf->Cell(40, 10, 'Subtotal After Tax', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

$freight_charge = $order['freight'];
// var_dump($freight_charge);
// Loop through all items
do {

    // Ensure the values exist before performing calculations
    $quantity = isset($order['quantity']) ? $order['quantity'] : 0;
    $price = isset($order['sp']) ? $order['sp'] : 0;
    $discount = isset($order['discount']) ? $order['discount'] : 0;
    $cgst = isset($order['cgst']) ? $order['cgst'] : 0;
    $sgst = isset($order['sgst']) ? $order['sgst'] : 0;
    $igst = isset($order['igst']) ? $order['igst'] : 0;

    $item_total = $quantity * $price;
    $discount_amount = ($item_total * $discount) / 100;
    $subtotal_item = $item_total - $discount_amount;

    $tax_cgst = $cgst;
    $tax_sgst = $sgst;
    $tax_igst = $igst;

    $subtotal_after_tax = $subtotal_item + $tax_cgst + $tax_sgst + $tax_igst;

    $total_tax_cgst += $tax_cgst;
    $total_tax_sgst += $tax_sgst;
    $total_tax_igst += $tax_igst;
    $subtotal += $subtotal_item;


    $pdf->Cell(20, 10, $item_number++, 1, 0, 'C');
    $pdf->Cell(50, 10, isset($order['general_name']) ? $order['general_name'] : 'N/A', 1, 0, 'C');
    $pdf->Cell(30, 10, isset($order['batch_code']) ? $order['batch_code'] : 'N/A', 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($price, 2), 1, 0, 'C');
    $pdf->Cell(20, 10, $quantity, 1, 0, 'C');
    $pdf->Cell(20, 10, number_format($discount, 2) . '%', 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($subtotal_item, 2), 1, 0, 'C');
    $pdf->Cell(25, 10, number_format($tax_cgst + $tax_sgst + $tax_igst, 2), 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($subtotal_after_tax, 2), 1, 1, 'C');
} while ($order = $result->fetch_assoc());

// Add Freight Charges
$pdf->Ln(10);
$pdf->Cell(270, 10, 'Freight Charges: INR ' . number_format($freight_charge, 2), 1, 1, 'R');

// Calculate Grand Total
$grand_total = $subtotal + $total_tax_cgst + $total_tax_sgst + $total_tax_igst + $freight_charge;
$pdf->Cell(270, 10, 'Grand Total: INR ' . number_format($grand_total, 2), 1, 1, 'R');



//Convert Grand Total to Words (Simple Custom Function)
function convertNumberToWords($num)
{
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return ucfirst($f->format($num));
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 12);
$pdf->MultiCell(200, 10, 'Amount in Words: ' . convertNumberToWords($grand_total) . ' only', 0, 'L');

// Output PDF
$pdf->Output('I', 'Invoice_' . $order['order_id'] . '.pdf');
