<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('cari')->toString();

        $sales = $this->filteredSalesQuery($search)
            ->with('customer')
            ->latest('sale_date')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search'));
    }

    public function exportExcel(Request $request)
    {
        if (! class_exists(\ZipArchive::class)) {
            return back()->withErrors('Export Excel membutuhkan ekstensi PHP Zip. Gunakan Export CSV jika ekstensi Zip belum aktif di server.');
        }

        $search = $request->string('cari')->toString();
        $sales = $this->filteredSalesQuery($search)
            ->with(['customer', 'items'])
            ->latest('sale_date')
            ->latest()
            ->get();

        $directory = storage_path('app/exports');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'laporan-penjualan-' . now()->format('Ymd-His') . '.xlsx';
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        $this->buildXlsxFile($sales, $filePath);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $search = $request->string('cari')->toString();
        $sales = $this->filteredSalesQuery($search)
            ->with(['customer', 'items'])
            ->latest('sale_date')
            ->latest()
            ->get();

        $fileName = 'laporan-penjualan-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            echo "\xEF\xBB\xBF";

            fputcsv($handle, $this->exportHeaders(), ';');

            foreach ($this->exportRows($sales) as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function exportSql(Request $request): StreamedResponse
    {
        $search = $request->string('cari')->toString();
        $sales = $this->filteredSalesQuery($search)
            ->with('items')
            ->oldest('id')
            ->get();

        $fileName = 'backup-penjualan-' . now()->format('Ymd-His') . '.sql';

        return response()->streamDownload(function () use ($sales) {
            echo "-- Backup data transaksi penjualan\n";
            echo "-- Dibuat pada: " . now()->format('d/m/Y H:i') . "\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($sales as $sale) {
                echo "INSERT INTO `sales` (`id`, `invoice_number`, `customer_id`, `user_id`, `sale_date`, `subtotal`, `discount`, `tax`, `grand_total`, `paid_amount`, `change_amount`, `payment_method`, `status`, `notes`, `created_at`, `updated_at`) VALUES (";
                echo implode(', ', [
                    $this->sqlValue($sale->id, true),
                    $this->sqlValue($sale->invoice_number),
                    $this->sqlValue($sale->customer_id, true),
                    $this->sqlValue($sale->user_id, true),
                    $this->sqlValue(optional($sale->sale_date)->format('Y-m-d')),
                    $this->sqlValue($sale->subtotal, true),
                    $this->sqlValue($sale->discount, true),
                    $this->sqlValue($sale->tax, true),
                    $this->sqlValue($sale->grand_total, true),
                    $this->sqlValue($sale->paid_amount, true),
                    $this->sqlValue($sale->change_amount, true),
                    $this->sqlValue($sale->payment_method),
                    $this->sqlValue($sale->status),
                    $this->sqlValue($sale->notes),
                    $this->sqlValue(optional($sale->created_at)->format('Y-m-d H:i:s')),
                    $this->sqlValue(optional($sale->updated_at)->format('Y-m-d H:i:s')),
                ]);
                echo ");\n";

                foreach ($sale->items as $item) {
                    echo "INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `product_sku`, `product_name`, `quantity`, `price`, `line_total`, `created_at`, `updated_at`) VALUES (";
                    echo implode(', ', [
                        $this->sqlValue($item->id, true),
                        $this->sqlValue($item->sale_id, true),
                        $this->sqlValue($item->product_id, true),
                        $this->sqlValue($item->product_sku),
                        $this->sqlValue($item->product_name),
                        $this->sqlValue($item->quantity, true),
                        $this->sqlValue($item->price, true),
                        $this->sqlValue($item->line_total, true),
                        $this->sqlValue(optional($item->created_at)->format('Y-m-d H:i:s')),
                        $this->sqlValue(optional($item->updated_at)->format('Y-m-d H:i:s')),
                    ]);
                    echo ");\n";
                }
            }

            echo "\nSET FOREIGN_KEY_CHECKS=1;\n";
        }, $fileName, [
            'Content-Type' => 'application/sql; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function create()
    {
        return view('sales.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sale_date' => ['required', 'date'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['required', 'exists:products,id'],
            'quantities' => ['required', 'array', 'min:1'],
            'quantities.*' => ['required', 'integer', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'max:40'],
            'notes' => ['nullable', 'max:1000'],
        ]);

        if (count($validated['product_ids']) !== count($validated['quantities'])) {
            return back()->withErrors('Jumlah produk dan kuantitas belum seimbang.')->withInput();
        }

        try {
            $sale = DB::transaction(function () use ($request, $validated) {
                $items = [];
                $subtotal = 0;

                foreach ($validated['product_ids'] as $index => $productId) {
                    $quantity = (int) $validated['quantities'][$index];
                    $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();

                    if (! $product->is_active || $product->stock < $quantity) {
                        throw new \RuntimeException("Stok {$product->name} tidak cukup untuk transaksi ini.");
                    }

                    $lineTotal = $quantity * (float) $product->selling_price;
                    $subtotal += $lineTotal;

                    $items[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'price' => (float) $product->selling_price,
                        'line_total' => $lineTotal,
                    ];
                }

                $discount = (float) ($validated['discount'] ?? 0);
                $tax = (float) ($validated['tax'] ?? 0);
                $grandTotal = max($subtotal - $discount + $tax, 0);
                $paidAmount = (float) $validated['paid_amount'];

                if ($paidAmount < $grandTotal) {
                    throw new \RuntimeException('Nominal bayar masih kurang dari total transaksi.');
                }

                $sale = Sale::create([
                    'invoice_number' => $this->nextInvoiceNumber(),
                    'customer_id' => $validated['customer_id'] ?? null,
                    'user_id' => $request->user()->id,
                    'sale_date' => $validated['sale_date'],
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $paidAmount - $grandTotal,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'Lunas',
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($items as $item) {
                    $sale->items()->create([
                        'product_id' => $item['product']->id,
                        'product_sku' => $item['product']->sku,
                        'product_name' => $item['product']->name,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'line_total' => $item['line_total'],
                    ]);

                    $item['product']->decrement('stock', $item['quantity']);
                }

                return $sale;
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }

        return redirect()->route('penjualan.show', $sale)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->load('items.product');

            foreach ($sale->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $sale->delete();
        });

        return redirect()->route('penjualan.index')->with('success', 'Transaksi dibatalkan dan stok dikembalikan.');
    }

    private function filteredSalesQuery(?string $search = null): Builder
    {
        return Sale::query()
            ->when($search, function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            });
    }

    private function exportHeaders(): array
    {
        return [
            'No',
            'Invoice',
            'Tanggal',
            'Pelanggan',
            'Metode Pembayaran',
            'Jumlah Item',
            'Subtotal',
            'Diskon',
            'Pajak / Biaya',
            'Total',
            'Bayar',
            'Kembalian',
            'Status',
        ];
    }

    private function exportRows($sales): array
    {
        return $sales->values()->map(function (Sale $sale, int $index) {
            return [
                $index + 1,
                $sale->invoice_number,
                optional($sale->sale_date)->format('d/m/Y'),
                $sale->customer->name ?? 'Pelanggan umum',
                $sale->payment_method,
                $sale->items->sum('quantity'),
                (float) $sale->subtotal,
                (float) $sale->discount,
                (float) $sale->tax,
                (float) $sale->grand_total,
                (float) $sale->paid_amount,
                (float) $sale->change_amount,
                $sale->status,
            ];
        })->all();
    }

    private function buildXlsxFile($sales, string $filePath): void
    {
        $headers = $this->exportHeaders();
        $rows = $this->exportRows($sales);
        $zip = new \ZipArchive();

        if ($zip->open($filePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('File export Excel tidak dapat dibuat.');
        }

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypes());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRels());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRels());
        $zip->addFromString('xl/styles.xml', $this->xlsxStyles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxWorksheet($headers, $rows));
        $zip->close();
    }

    private function xlsxWorksheet(array $headers, array $rows): string
    {
        $numericColumns = [1, 6, 7, 8, 9, 10, 11, 12];
        $sheetRows = [];
        $sheetRows[] = $this->xlsxRow($headers, 1, [], true);

        foreach ($rows as $index => $row) {
            $sheetRows[] = $this->xlsxRow($row, $index + 2, $numericColumns);
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18"/>'
            . '<cols><col min="1" max="1" width="8" customWidth="1"/><col min="2" max="5" width="22" customWidth="1"/><col min="6" max="13" width="16" customWidth="1"/></cols>'
            . '<sheetData>' . implode('', $sheetRows) . '</sheetData>'
            . '</worksheet>';
    }

    private function xlsxRow(array $values, int $rowNumber, array $numericColumns = [], bool $isHeader = false): string
    {
        $cells = [];

        foreach ($values as $index => $value) {
            $columnNumber = $index + 1;
            $reference = $this->xlsxColumnName($columnNumber) . $rowNumber;
            $isNumeric = in_array($columnNumber, $numericColumns, true) && is_numeric($value);
            $style = $isHeader ? ' s="1"' : ($isNumeric ? ' s="2"' : '');

            if ($isNumeric) {
                $cells[] = '<c r="' . $reference . '"' . $style . '><v>' . $value . '</v></c>';
            } else {
                $cells[] = '<c r="' . $reference . '" t="inlineStr"' . $style . '><is><t>' . $this->xml($value) . '</t></is></c>';
            }
        }

        return '<row r="' . $rowNumber . '">' . implode('', $cells) . '</row>';
    }

    private function xlsxColumnName(int $columnNumber): string
    {
        $name = '';

        while ($columnNumber > 0) {
            $modulo = ($columnNumber - 1) % 26;
            $name = chr(65 + $modulo) . $name;
            $columnNumber = intdiv($columnNumber - $modulo, 26);
        }

        return $name;
    }

    private function xlsxContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function xlsxRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function xlsxWorkbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Laporan Penjualan" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function xlsxWorkbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function xlsxStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1E3A8A"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFill="1"/><xf numFmtId="4" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/></cellXfs>'
            . '</styleSheet>';
    }

    private function sqlValue($value, bool $numeric = false): string
    {
        if ($value === null || $value === '') {
            return 'NULL';
        }

        if ($numeric && is_numeric($value)) {
            return (string) $value;
        }

        return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], (string) $value) . "'";
    }

    private function xml($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function nextInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $lastInvoice = Sale::where('invoice_number', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->latest('id')
            ->value('invoice_number');

        $nextNumber = $lastInvoice ? ((int) substr($lastInvoice, -4)) + 1 : 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
