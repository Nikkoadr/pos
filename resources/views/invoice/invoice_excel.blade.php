<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Invoice ' . config('app.name', 'Laravel'));
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 17],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

$sheet->setCellValue('A3', 'Informasi Invoice');
$sheet->mergeCells('A3:C3');
$sheet->getStyle('A3')->applyFromArray([
    'font' => ['bold' => true],
]);

$sheet->setCellValue('A4', 'Id Invoice');
$sheet->setCellValue('B4', ':');
$sheet->setCellValue('C4', $nota['id']);
$sheet->getStyle('C4')->applyFromArray([
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
]);

$sheet->setCellValue('A5', 'Jenis Transaksi');
$sheet->setCellValue('B5', ':');
$sheet->setCellValue('C5', $nota['jenis_transaksi']);

$sheet->setCellValue('A6', 'Kasir');
$sheet->setCellValue('B6', ':');
$sheet->setCellValue('C6', $nota['kasir']);

$sheet->setCellValue('A7', 'Tanggal Transaksi');
$sheet->setCellValue('B7', ':');
$sheet->setCellValue('C7', \Carbon\Carbon::parse($nota['tanggal_transaksi'])->locale('id_ID')->isoFormat('D MMMM YYYY, HH:mm'));

$sheet->getStyle('A3:C7')->applyFromArray([
    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
]);

$sheet->setCellValue('A9', 'Detail Barang');
$sheet->getStyle('A9')->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
]);

$sheet->setCellValue('A10', 'Nama Barang');
$sheet->mergeCells('A10:C10');
$sheet->setCellValue('D10', 'Qty');
$sheet->setCellValue('E10', 'Harga');
$sheet->setCellValue('F10', 'Subtotal');
$sheet->getStyle('A10:F10')->applyFromArray(['font' => ['bold' => true],
'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

$row = 11;
foreach ($nota['detailNota'] as $detail) {
    $sheet->setCellValue('A' . $row, $detail['nama_barang']);
    $sheet->mergeCells('A' . $row . ':C' . $row);
    $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
    $sheet->setCellValue('D' . $row, $detail['qty']);
    $sheet->setCellValue('E' . $row, 'Rp ' . number_format($detail['harga'], 0, ',', '.'));
    $sheet->setCellValue('F' . $row, 'Rp ' . number_format($detail['subtotal'], 0, ',', '.'));
    $sheet->getStyle('D' . $row . ':F' . $row)->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);
    $row++;
}
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
$sheet->getStyle('A10:F10')->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

$sheet->setCellValue('D' . $row, 'Total Item');
$sheet->getStyle('D14:D17')->applyFromArray([
    'font' => ['bold' => true],
]);
$sheet->setCellValue('D' . $row, 'Total Item');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, count($nota['detailNota']));
$sheet->getStyle('F' . $row)->applyFromArray([
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
]);
$row++;

$sheet->setCellValue('D' . $row, 'Total Belanja');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, 'Rp ' . number_format($nota['total_belanja'], 0, ',', '.'));
$row++;

$sheet->setCellValue('D' . $row, 'Bayar');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, 'Rp ' . number_format($nota['bayar'], 0, ',', '.'));
$row++;

$sheet->setCellValue('D' . $row, 'Kembalian');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, 'Rp ' . number_format($nota['kembalian'], 0, ',', '.'));
$row++;

$sheet->setCellValue('A' . $row, 'Catatan: Barang yang sudah dibeli tidak bisa dikembalikan');
$sheet->mergeCells('A' . $row . ':D' . $row);
$sheet->getStyle('A' . $row)->applyFromArray([
    'font' => ['size' => 12],
]);

$row++;
$sheet->setCellValue('A' . $row, 'Terima Kasih');
$sheet->mergeCells('A' . $row . ':F' . $row);
$sheet->getStyle('A' . $row)->applyFromArray([
    'font' => ['size' => 16],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

$fileName = 'invoice.xlsx';
$writer = new Xlsx($spreadsheet);
$filePath = 'public/' . $fileName;
$writer->save(storage_path('app/' . $filePath));

$spreadsheet = IOFactory::load(storage_path('app/' . $filePath));
$writer = IOFactory::createWriter($spreadsheet, 'Html');
$htmlContent = $writer->save('php://output');

echo $htmlContent;
?>
<script> window.print(); </script>