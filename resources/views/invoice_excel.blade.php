<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

// Membuat objek Spreadsheet baru
$spreadsheet = new Spreadsheet();

// Mendapatkan aktif sheet
$sheet = $spreadsheet->getActiveSheet();
// Judul Invoice
$sheet->setCellValue('A1', 'Invoice ' . config('app.name', 'Laravel'));
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 17],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

// Header Informasi Invoice
$sheet->setCellValue('A3', 'Informasi Invoice');
$sheet->mergeCells('A3:C3');
$sheet->getStyle('A3')->applyFromArray([
    'font' => ['bold' => true],
]);

// Detail Informasi Invoice
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

// Styling Header Informasi Invoice
$sheet->getStyle('A3:C7')->applyFromArray([
    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
]);

// Header Detail Barang
$sheet->setCellValue('A9', 'Detail Barang');
$sheet->getStyle('A9')->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
]);

// Header Kolom Detail Barang
$sheet->setCellValue('A10', 'Nama Barang');
$sheet->mergeCells('A10:C10');
$sheet->setCellValue('D10', 'Qty');
$sheet->setCellValue('E10', 'Harga');
$sheet->setCellValue('F10', 'Subtotal');
//$sheet->mergeCells('D10:F10');
$sheet->getStyle('A10:F10')->applyFromArray(['font' => ['bold' => true],
'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

// Data Detail Barang
$row = 11;
foreach ($nota['detailNota'] as $detail) {
    $sheet->setCellValue('A' . $row, $detail['nama_barang']);
    $sheet->mergeCells('A' . $row . ':C' . $row);
    $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]); // Pusatkan sel A sampai C pada baris yang sedang diproses
    $sheet->setCellValue('D' . $row, $detail['qty']);
    $sheet->setCellValue('E' . $row, 'Rp ' . number_format($detail['harga'], 0, ',', '.'));
    $sheet->setCellValue('F' . $row, 'Rp ' . number_format($detail['subtotal'], 0, ',', '.'));
    $sheet->getStyle('D' . $row . ':F' . $row)->applyFromArray([
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]); // Pusatkan sel D sampai F pada baris yang sedang diproses
    $row++;
}
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
// Styling Detail Barang
$sheet->getStyle('A10:F10')->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

// Total Item
$sheet->setCellValue('D' . $row, 'Total Item');
$sheet->getStyle('D14:D17')->applyFromArray([
    'font' => ['bold' => true],
]);
// Total Item
$sheet->setCellValue('D' . $row, 'Total Item');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, count($nota['detailNota']));
$sheet->getStyle('F' . $row)->applyFromArray([
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
]); // Menggeser teks ke kiri
$row++;

// Total Belanja
$sheet->setCellValue('D' . $row, 'Total Belanja');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, 'Rp ' . number_format($nota['total_belanja'], 0, ',', '.'));
$row++;

// Bayar
$sheet->setCellValue('D' . $row, 'Bayar');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, 'Rp ' . number_format($nota['bayar'], 0, ',', '.'));
$row++;

// Kembalian
$sheet->setCellValue('D' . $row, 'Kembalian');
$sheet->setCellValue('E' . $row, ':');
$sheet->setCellValue('F' . $row, 'Rp ' . number_format($nota['kembalian'], 0, ',', '.'));
$row++;

// Catatan
$sheet->setCellValue('A' . $row, 'Catatan: Barang yang sudah dibeli tidak bisa dikembalikan');
$sheet->mergeCells('A' . $row . ':D' . $row);
$sheet->getStyle('A' . $row)->applyFromArray([
    'font' => ['size' => 12],
]);

// Terima Kasih
$row++;
$sheet->setCellValue('A' . $row, 'Terima Kasih');
$sheet->mergeCells('A' . $row . ':F' . $row);
$sheet->getStyle('A' . $row)->applyFromArray([
    'font' => ['size' => 16],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

// Menyimpan file ke dalam direktori penyimpanan
$fileName = 'invoice.xlsx';
$writer = new Xlsx($spreadsheet);
$fileContents = $writer->writeToString();
Storage::put('public/' . $fileName, $fileContents);

// Mengambil URL untuk file yang disimpan
$filePath = Storage::url('public/' . $fileName);

// Membaca kembali file Excel yang sudah disimpan dan mengonversinya menjadi HTML
$spreadsheet = IOFactory::load($fileName);
$writer = IOFactory::createWriter($spreadsheet, 'Html');
$htmlContent = $writer->save('php://output');

// Menampilkan HTML di peramban
echo $htmlContent;
?>
<script> window.print(); </script>