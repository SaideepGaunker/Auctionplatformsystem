<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require 'db.php';

// Query your table
$result = $con->query("SELECT t.name as team_name, p.name as player_name, s.soldPrice, s.time FROM sold_player s JOIN players p ON s.playerId = p.id JOIN teams t ON s.teamId = t.id ORDER BY t.name");
if (!$result) {
    die("Query failed: " . $con->error);
}

// Create spreadsheet and add data
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add headers
$fields = $result->fetch_fields();
$col = 'A';
foreach ($fields as $field) {
    $sheet->setCellValue($col . '1', $field->name);
    $col++;
}

// Add rows
$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $col = 'A';
    foreach ($row as $value) {
        $sheet->setCellValue($col . $rowNumber, $value);
        $col++;
    }
    $rowNumber++;
}

// Add unsold players
$unsold_result = $con->query("SELECT p.name FROM players p LEFT JOIN sold_player s ON p.id = s.playerId WHERE s.playerId IS NULL");
if (!$unsold_result) {
    die("Query failed for unsold players: " . $con->error);
}

while ($row = $unsold_result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, 'unsold');
    $sheet->setCellValue('B' . $rowNumber, $row['name']);
    $sheet->setCellValue('C' . $rowNumber, '-');
    $sheet->setCellValue('D' . $rowNumber, '-');
    $rowNumber++;
}

// Output file
$filename = 'backup_' . date('Ymd_His') . '.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

// Send file to browser for download
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$filename\"");
readfile($filename);
unlink($filename); // clean up
exit;
?>
