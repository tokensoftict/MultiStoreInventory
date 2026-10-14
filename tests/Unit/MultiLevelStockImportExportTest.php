<?php

namespace Tests\Unit;

use App\Exports\MultiLevelStockExport;
use App\Imports\ImportNewMultiLevelStock;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\Warehousestore;
use App\Services\InventoryQuantityService;
use App\Services\MultiLevelStockSheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MultiLevelStockImportExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session()->put('activeStore', Warehousestore::find(1)->toArray());
    }

    public function test_template_headings_include_every_active_unit()
    {
        request()->merge(['template' => true]);
        $headings = (new MultiLevelStockExport())->headings();

        $this->assertNotContains('ID', $headings);
        $this->assertContains('NAME', $headings);
        $this->assertContains('SUPPLIER NAME', $headings);
        foreach (MultiLevelStockSheet::levels() as $level) {
            $label = strtoupper($level['name']);
            $this->assertContains("$label COST PRICE", $headings);
            $this->assertContains("$label SELLING PRICE", $headings);
            $this->assertContains("$label QUANTITY", $headings);
        }
        $this->assertEmpty((new MultiLevelStockExport())->collection());
    }

    public function test_import_creates_units_prices_and_opening_stock()
    {
        DB::beginTransaction();
        try {
            $row = $this->row('Import Test Product', [
                1 => ['cost' => 600, 'selling' => 800, 'qty' => 2],
                2 => ['cost' => 60, 'selling' => 80, 'qty' => 3, 'per' => 10],
                3 => ['cost' => 6, 'selling' => 8, 'qty' => 4, 'per' => 10],
            ]);
            (new ImportNewMultiLevelStock())->model($row);

            $stock = Stock::where('name', 'Import Test Product')->firstOrFail();
            $units = ProductUnit::where('stock_id', $stock->id)->where('status', 1)->orderBy('level')->get();
            $this->assertCount(3, $units);
            $this->assertEquals(800, $units[0]->selling_price);
            $this->assertEquals(10, $units[2]->units_per_parent);

            // 2 cartons (200) + 3 packets (30) + 4 pieces
            $store = getActiveStore();
            $this->assertEquals(234, (float)$stock->stockbatches()->sum($store->yard_column));
        } finally {
            DB::rollBack();
        }
    }

    public function test_import_rejects_packaging_without_conversion()
    {
        DB::beginTransaction();
        try {
            $row = $this->row('Bad Product', [
                1 => ['cost' => 600, 'selling' => 800],
                3 => ['cost' => 6, 'selling' => 8],
            ]);
            $this->expectException(ValidationException::class);
            (new ImportNewMultiLevelStock())->model($row);
        } finally {
            DB::rollBack();
        }
    }

    /** Build a heading-keyed row like Maatwebsite hands to the importer. */
    private function row(string $name, array $values): array
    {
        $row = ['name' => $name, 'product_type' => 'SINGLE'];
        foreach (MultiLevelStockSheet::levels() as $level) {
            $v = $values[$level['level']] ?? [];
            $cols = $level['columns'];
            $row[$cols['cost']['key']] = $v['cost'] ?? null;
            $row[$cols['selling']['key']] = $v['selling'] ?? null;
            $row[$cols['qty']['key']] = $v['qty'] ?? null;
            if (isset($cols['per_parent'])) {
                $row[$cols['per_parent']['key']] = $v['per'] ?? null;
            }
        }
        return $row;
    }
}
