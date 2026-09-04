<?php
session_start();
require_once 'includes/db.php';
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get all analyses
$result = mysqli_query($conn,
    "SELECT * FROM skin_analysis 
     WHERE user_id='$userId' 
     ORDER BY detected_at DESC");

$analyses = [];
while($row = mysqli_fetch_assoc($result)) {
    $analyses[] = $row;
}

$totalAnalyses = count($analyses);

// Get skin type distribution
$distResult = mysqli_query($conn,
    "SELECT skin_type, COUNT(*) as total 
     FROM skin_analysis 
     WHERE user_id='$userId' 
     GROUP BY skin_type 
     ORDER BY total DESC");

$skinDist = [];
while($row = mysqli_fetch_assoc($distResult)) {
    $skinDist[] = $row;
}

$mostFrequent = !empty($skinDist) ? $skinDist[0] : null;
$latest = !empty($analyses) ? $analyses[0] : null;

// Get recommendations
$recommendations = [];
if($mostFrequent) {
    $recResult = mysqli_query($conn,
        "SELECT * FROM recommendations 
         WHERE skin_type='{$mostFrequent['skin_type']}'");
    while($row = mysqli_fetch_assoc($recResult)) {
        $recommendations[] = $row;
    }
}

// Build HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
    .header { background: #e07b8a; color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
    .header h1 { margin: 0; font-size: 24px; }
    .header p { margin: 5px 0 0; font-size: 13px; opacity: 0.9; }
    .section { margin-bottom: 20px; }
    .section h2 { color: #e07b8a; font-size: 16px; border-bottom: 2px solid #f0e0e5; padding-bottom: 5px; }
    .summary-grid { display: flex; gap: 10px; margin-bottom: 15px; }
    .summary-item { background: #fdf6f0; border-radius: 8px; padding: 12px; text-align: center; flex: 1; }
    .summary-item h3 { color: #e07b8a; margin: 0 0 4px; font-size: 18px; }
    .summary-item p { color: #777; margin: 0; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th { background: #fdf0f3; color: #e07b8a; padding: 8px 12px; text-align: left; }
    td { padding: 8px 12px; border-bottom: 1px solid #f5e8eb; color: #555; }
    .rec-row { background: #fdf6f0; border-radius: 8px; padding: 10px; margin-bottom: 8px; }
    .rec-step { color: #e07b8a; font-weight: bold; font-size: 12px; }
    .footer { text-align: center; color: #aaa; font-size: 11px; margin-top: 30px; border-top: 1px solid #f0e0e5; padding-top: 10px; }
</style>
</head>
<body>

<div class="header">
    <h1>SkinSense - Skin Analysis Report</h1>
    <p>Name: ' . $userName . ' &nbsp;|&nbsp; Date: ' . date('d M Y') . ' &nbsp;|&nbsp; Total Analyses: ' . $totalAnalyses . '</p>
</div>

<div class="section">
    <h2>Summary</h2>
    <div class="summary-grid">
        <div class="summary-item">
            <h3>' . ($latest ? ucfirst($latest['skin_type']) : '-') . '</h3>
            <p>Latest Skin Type</p>
        </div>
        <div class="summary-item">
            <h3>' . ($mostFrequent ? ucfirst($mostFrequent['skin_type']) : '-') . '</h3>
            <p>Most Frequent</p>
        </div>
        <div class="summary-item">
            <h3>' . $totalAnalyses . '</h3>
            <p>Total Scans</p>
        </div>
    </div>
</div>

<div class="section">
    <h2>Skin Type Distribution</h2>
    <table>
        <thead>
            <tr>
                <th>Skin Type</th>
                <th>Times Detected</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>';

foreach($skinDist as $dist) {
    $percentage = round(($dist['total'] / $totalAnalyses) * 100);
    $html .= '
            <tr>
                <td>' . ucfirst($dist['skin_type']) . ' Skin</td>
                <td>' . $dist['total'] . ' times</td>
                <td>' . $percentage . '%</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Analysis History</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date & Time</th>
                <th>Skin Type</th>
            </tr>
        </thead>
        <tbody>';

foreach($analyses as $index => $analysis) {
    $html .= '
            <tr>
                <td>' . ($index + 1) . '</td>
                <td>' . date('d M Y, h:i A', strtotime($analysis['detected_at'])) . '</td>
                <td>' . ucfirst($analysis['skin_type']) . ' Skin</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Recommended Skincare Routine</h2>
    <p style="color:#777; font-size:13px; margin-bottom:10px;">
        Based on your most frequent skin type: <strong>' . ($mostFrequent ? ucfirst($mostFrequent['skin_type']) : '-') . ' Skin</strong>
    </p>';

$step = 1;
foreach($recommendations as $rec) {
    $html .= '
    <div class="rec-row">
        <div class="rec-step">Step ' . $step++ . '</div>
        <strong>' . $rec['step'] . '</strong>
        <p style="margin:3px 0 0; color:#777; font-size:12px;">' . $rec['tip'] . '</p>
    </div>';
}

$html .= '
</div>

<div class="footer">
    Generated by SkinSense AI System • ' . date('d M Y') . '
</div>

</body>
</html>';

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', false);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Download PDF
$dompdf->stream('SkinSense_Report_' . date('d-M-Y') . '.pdf', 
    ['Attachment' => true]);
exit();
?>