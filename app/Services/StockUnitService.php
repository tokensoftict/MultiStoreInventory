<?php

namespace App\Services;

use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\Warehousestore;
use Illuminate\Support\Facades\DB;

/**
 * Database-aware glue for the optional Carton -> Packet -> Piece system.
 * Every public method is a no-op / returns "legacy" behaviour unless the store is multi-level.
 * In multi-level stores the base Piece quantity lives in the store's yard column.
 */
class StockUnitService
{
    private static $storeCache = [];
    private static $unitCache = [];

    /** Is the currently active store (or given store id) in multi-level mode? Always false for legacy. */
    public static function isMulti($storeId = null): bool
    {
        if ($storeId === null) {
            $active = function_exists('getActiveStore') ? getActiveStore() : null;
            $storeId = $active->id ?? null;
        }
        if (!$storeId) {
            return false;
        }
        if (!array_key_exists($storeId, self::$storeCache)) {
            self::$storeCache[$storeId] = Warehousestore::find($storeId);
        }
        return InventoryQuantityService::usesMultiLevel(self::$storeCache[$storeId]);
    }

    /** Store whose packed column equals the given one (purchase form posts packed_column). */
    public static function storeByColumn($column)
    {
        return Warehousestore::where('packed_column', $column)->orWhere('yard_column', $column)->first();
    }

    public static function baseColumn($store)
    {
        return $store->yard_column;
    }

    public static function clearCache(): void
    {
        self::$storeCache = [];
        self::$unitCache = [];
    }

    /** Normalised active units for a product, keyed by nothing (list ordered by level). */
    public static function units($stockId): array
    {
        if (!isset(self::$unitCache[$stockId])) {
            $activeLevels = \App\Models\QuantityUnit::activeUnits()->pluck('level')->toArray();
            self::$unitCache[$stockId] = InventoryQuantityService::normalize(
                ProductUnit::where('stock_id', $stockId)
                    ->where('status', 1)
                    ->whereIn('level', $activeLevels)
                    ->orderBy('level')
                    ->get()
            );
        }
        return self::$unitCache[$stockId];
    }

    /** Units with prices and multipliers, for the front end. */
    public static function unitsForUi($stockId, $baseQuantity = null): array
    {
        $activeLevels = \App\Models\QuantityUnit::activeUnits()->pluck('level')->toArray();
        $rows = ProductUnit::where('stock_id', $stockId)
            ->where('status', 1)
            ->whereIn('level', $activeLevels)
            ->orderBy('level')
            ->get();
        $units = InventoryQuantityService::normalize($rows);
        $out = [];
        foreach ($rows as $row) {
            $mult = InventoryQuantityService::multiplier($units, $row->id);
            $out[] = [
                'id' => $row->id,
                'name' => $row->name,
                'level' => $row->level,
                'multiplier' => $mult,
                'cost_price' => (float)$row->cost_price,
                'selling_price' => (float)$row->selling_price,
                'available' => $baseQuantity === null ? null : floor(round($baseQuantity / $mult, 6)),
            ];
        }
        return $out;
    }

    public static function hasUnits($stockId): bool
    {
        return count(self::units($stockId)) > 0;
    }

    public static function breakdownLabel($stockId, $baseQuantity): string
    {
        $units = self::units($stockId);
        if (!$units) {
            return (string)(float)$baseQuantity;
        }
        return InventoryQuantityService::breakdownLabel($units, $baseQuantity);
    }

    /** Per-unit worth of a base quantity: [['name','quantity','cost','selling'], ...] largest unit first. */
    public static function valuationBreakdown($stockId, $baseQuantity): array
    {
        $units = self::units($stockId);
        if (!$units) {
            return [];
        }
        $prices = ProductUnit::where('stock_id', $stockId)->where('status', 1)->get()->keyBy('id');
        $rows = [];
        foreach (InventoryQuantityService::breakdown($units, $baseQuantity) as $row) {
            $rows[] = [
                'name' => $row['name'],
                'level' => $row['level'],
                'quantity' => $row['quantity'],
                'cost' => $row['quantity'] * (float)$prices[$row['id']]->cost_price,
                'selling' => $row['quantity'] * (float)$prices[$row['id']]->selling_price,
            ];
        }
        return $rows;
    }

    /** Stock worth for a base quantity using each unit's own prices: [cost, selling]. */
    public static function valuation($stockId, $baseQuantity): array
    {
        $units = self::units($stockId);
        if (!$units) {
            return [0.0, 0.0];
        }
        $prices = ProductUnit::where('stock_id', $stockId)->where('status', 1)->get()->keyBy('id');
        $sorted = $units;
        usort($sorted, function ($a, $b) {
            return $a['level'] <=> $b['level'];
        });

        $remaining = (float)$baseQuantity;
        $last = count($sorted) - 1;
        $cost = 0.0;
        $sell = 0.0;
        foreach ($sorted as $i => $unit) {
            $mult = InventoryQuantityService::multiplier($units, $unit['id']);
            if ($i === $last) {
                $qty = round($remaining, 4);
            } else {
                $qty = floor(round($remaining / $mult, 6));
                $remaining = round($remaining - $qty * $mult, 4);
            }
            $unitModel = $prices[$unit['id']] ?? null;
            if ($unitModel) {
                $cost += $qty * (float)$unitModel->cost_price;
                $sell += $qty * (float)$unitModel->selling_price;
            }
        }
        return [$cost, $sell];
    }

    /**
     * Quick-adjust: set a multi-level product's stock to the total of the given per-level
     * quantities ([level => qty]). All stock is held as base pieces in the store's yard column.
     * Returns a list of error strings (empty on success).
     */
    public static function adjustQuantity(Stock $stock, array $levelQuantities, $expiryDate = null): array
    {
        $units = ProductUnit::where('stock_id', $stock->id)->where('status', 1)->get()->keyBy('level');
        if ($units->isEmpty()) {
            return ['This product has no units defined. Edit the product and set its units first.'];
        }

        $received = [];
        foreach ($levelQuantities as $level => $qty) {
            if ($qty === null || $qty === '') {
                continue;
            }
            if (!is_numeric($qty) || $qty < 0 || !isset($units[$level])) {
                return ['Quantities must be zero or more, for the product\'s defined units only.'];
            }
            $received[$units[$level]->id] = (float)$qty;
        }
        $newBase = InventoryQuantityService::toBaseMixed(InventoryQuantityService::normalize($units->values()), $received);

        $batches = $stock->stockBatches()->orderBy('expiry_date', 'DESC')->get();
        if ($batches->isEmpty()) {
            return ['Stock Initial Purchase Order not found, Please create Purchase order for product to adjust'];
        }

        $store = getActiveStore();
        $column = $store->yard_column;
        $old = (float)$batches->sum($column);

        DB::transaction(function () use ($stock, $batches, $column, $newBase, $old, $store, $expiryDate) {
            foreach ($batches as $batch) {
                $batch->$column = 0;
                $batch->save();
            }
            $recent = $batches->first();
            $recent->$column = $newBase;
            if ($expiryDate) {
                $recent->expiry_date = $expiryDate;
            }
            $recent->save();

            \App\Models\StockQuantityAdjustment::create([
                'stock_id' => $stock->id,
                'user_id' => auth()->id(),
                'date_adjusted' => date('Y-m-d'),
                'from' => $old,
                'to' => $newBase,
                'type' => 'NORMAL',
                'warehousestore_id' => $store->id,
                'changed_column' => $column,
            ]);
        });

        return [];
    }

    /**
     * Replace a product's units from form input and validate them.
     * $input: [level => ['name','units_per_parent','cost_price','selling_price']] for levels 1..3.
     * A level is considered "used" when its name is filled in; Piece (3) is mandatory.
     * Returns list of error strings (empty on success).
     */
    public static function saveUnits(Stock $stock, array $input): array
    {
        $activeUnits = \App\Models\QuantityUnit::activeUnits();
        $activeLevels = $activeUnits->pluck('level')->toArray();
        $baseUnit = \App\Models\QuantityUnit::getBaseUnit();
        $baseLevel = (int)($baseUnit->level ?? 3);

        $levels = [];
        foreach ($activeLevels as $level) {
            $row = $input[$level] ?? null;
            if ($row && trim((string)($row['name'] ?? '')) !== '') {
                $levels[$level] = $row;
            }
        }
        if (!isset($levels[$baseLevel])) {
            return ['The ' . ($baseUnit->name ?? 'base unit') . ' is required.'];
        }
        $hasActivePackaging = count($activeLevels) > 1;
        if ($hasActivePackaging && count($levels) < 2) {
            return ['Define at least one packaging unit larger than the ' . ($baseUnit->name ?? 'base unit') . '.'];
        }

        $errors = [];
        $chain = array_keys($levels); // ascending levels, e.g. [1,2,3] or [2,3] or [1,3]
        $units = [];
        foreach ($chain as $idx => $level) {
            $row = $levels[$level];
            foreach (['cost_price', 'selling_price'] as $f) {
                if (isset($row[$f]) && $row[$f] !== '' && (!is_numeric($row[$f]) || $row[$f] < 0)) {
                    $errors[] = "{$row['name']}: $f cannot be negative.";
                }
            }
            $perParent = null;
            if ($idx > 0) {
                $perParent = $row['units_per_parent'] ?? null;
                if (!is_numeric($perParent) || $perParent <= 0) {
                    $errors[] = "Conversion for {$row['name']} must be greater than zero.";
                }
            }
            $units[] = [
                'id' => $level, // temporary ids for validation
                'name' => $row['name'],
                'level' => $level,
                'parent_unit_id' => $idx > 0 ? $chain[$idx - 1] : null,
                'units_per_parent' => $perParent !== null ? (float)$perParent : null,
            ];
        }
        $errors = array_merge($errors, InventoryQuantityService::validateUnits($units));
        if ($errors) {
            return array_values(array_unique($errors));
        }

        DB::transaction(function () use ($stock, $levels, $chain, $baseLevel) {
            $existing = ProductUnit::where('stock_id', $stock->id)->get()->keyBy('level');
            $savedIds = [];
            foreach ($chain as $idx => $level) {
                $row = $levels[$level];
                $unit = $existing[$level] ?? new ProductUnit(['stock_id' => $stock->id, 'level' => $level]);
                $unit->name = trim($row['name']);
                $unit->parent_unit_id = null; // set after ids exist
                $unit->units_per_parent = $idx > 0 ? (float)$row['units_per_parent'] : null;
                $unit->cost_price = (float)($row['cost_price'] ?? 0);
                $unit->selling_price = (float)($row['selling_price'] ?? 0);
                $unit->status = 1;
                $unit->save();
                $savedIds[$level] = $unit->id;
            }
            foreach ($chain as $idx => $level) {
                ProductUnit::where('id', $savedIds[$level])->update([
                    'parent_unit_id' => $idx > 0 ? $savedIds[$chain[$idx - 1]] : null,
                ]);
            }
            // retire levels that are no longer used
            ProductUnit::where('stock_id', $stock->id)->whereNotIn('id', array_values($savedIds))->update(['status' => 0]);

            // Keep legacy aggregate price fields meaningful for existing reports/valuation:
            // selling/cost = largest unit, yard_* = Piece.
            $top = $levels[$chain[0]];
            $piece = $levels[$baseLevel];
            $stock->selling_price = (float)($top['selling_price'] ?? 0);
            $stock->cost_price = (float)($top['cost_price'] ?? 0);
            $stock->yard_selling_price = (float)($piece['selling_price'] ?? 0);
            $stock->yard_cost_price = (float)($piece['cost_price'] ?? 0);
            $stock->save();
        });

        unset(self::$unitCache[$stock->id]);
        return [];
    }

    /**
     * Normalise raw invoice product rows for a multi-level store.
     * Rows with a unit_id are converted: qty -> base pieces, price/cost -> per-piece, original kept in unit_* keys.
     * Rows without unit_id are untouched (legacy behaviour).
     * Returns [products, errors(stock_id => message)].
     */
    public static function normalizeInvoiceProducts(array $products, $storeId = null): array
    {
        $errors = [];
        $activeStore = Warehousestore::find($storeId ?? getActiveStore()->id);
        $isMulti = $activeStore ? InventoryQuantityService::usesMultiLevel($activeStore) : false;

        $out = [];
        foreach ($products as $product) {
            $stock = Stock::find($product['id'] ?? null);
            if (!$stock) {
                $out[] = $product;
                continue;
            }

            if ($isMulti) {
                $units = self::units($product['id']);
                $unit = null;
                if (!empty($product['unit_id'])) {
                    foreach ($units as $u) {
                        if ((string)$u['id'] === (string)$product['unit_id']) {
                            $unit = $u;
                        }
                    }
                } elseif (count($units) > 0) {
                    // Multi-level store, but row has no unit_id (e.g. updating an invoice from before multi-level):
                    if (!empty($product['invoice_item_id']) && $product['invoice_item_id'] !== 'new') {
                        $oldItem = \App\Models\InvoiceItem::find($product['invoice_item_id']);
                        if ($oldItem && !empty($oldItem->unit_id)) {
                            foreach ($units as $u) {
                                if ((string)$u['id'] === (string)$oldItem->unit_id) {
                                    $unit = $u;
                                }
                            }
                        }
                    }
                    if (!$unit) {
                        if (($product['type'] ?? '') === $activeStore->packed_column) {
                            $unit = $units[0];
                        } else {
                            $unit = end($units);
                        }
                    }
                }

                if ($unit) {
                    $qty = (float)($product['qty'] ?? 0);
                    if ($qty <= 0) {
                        $errors[$product['id']] = 'Quantity must be greater than zero.';
                        $out[] = $product;
                        continue;
                    }
                    $mult = InventoryQuantityService::multiplier($units, $unit['id']);
                    $dbUnit = ProductUnit::find($unit['id']);
                    $unitPrice = (float)($product['price'] ?? ($dbUnit ? $dbUnit->selling_price : 0));
                    $unitCost = $dbUnit ? (float)$dbUnit->cost_price : 0;

                    // Fallback if dbUnit cost_price is 0
                    if ($unitCost <= 0) {
                        if ($dbUnit && $dbUnit->level == 1 && (float)$stock->cost_price > 0) {
                            $unitCost = (float)$stock->cost_price;
                        } elseif ((float)$stock->yard_cost_price > 0) {
                            $unitCost = (float)$stock->yard_cost_price * $mult;
                        } elseif ((float)$stock->cost_price > 0) {
                            $topMult = InventoryQuantityService::multiplier($units, $units[0]['id'] ?? $unit['id']);
                            $unitCost = $topMult > 0 ? ((float)$stock->cost_price / $topMult) * $mult : (float)$stock->cost_price;
                        }
                    }

                    $product['unit_id'] = $unit['id'];
                    $product['unit_name'] = $unit['name'];
                    $product['unit_qty'] = $qty;
                    $product['unit_price'] = $unitPrice;
                    $product['unit_cost'] = $unitCost;
                    $product['unit_multiplier'] = $mult;
                    $product['qty'] = round($qty * $mult, 4);
                    $product['price'] = $mult > 0 ? ($unitPrice / $mult) : $unitPrice;
                    $product['cost_price'] = $mult > 0 ? ($unitCost / $mult) : $unitCost;
                    $product['type'] = $activeStore->yard_column;
                    $out[] = $product;
                    continue;
                }
            }

            // Non-unit line (legacy store or stock with no multi-level units):
            // Cost price is always resolved from the stock model, never trusted from client
            $isYard = ($product['type'] ?? '') === ($activeStore ? $activeStore->yard_column : 'yard_quantity');
            if ($isYard) {
                $cost = (float)$stock->yard_cost_price > 0 ? (float)$stock->yard_cost_price : (float)$stock->cost_price;
            } else {
                $cost = (float)$stock->cost_price > 0 ? (float)$stock->cost_price : (float)$stock->yard_cost_price;
            }
            $product['cost_price'] = $cost;
            $out[] = $product;
        }
        return [$out, $errors];
    }

    /** Row identity: product + column + (unit when multi-level). */
    public static function rowKey(array $product): string
    {
        return $product['id'] . '-' . $product['type'] . (!empty($product['unit_id']) ? '-u' . $product['unit_id'] : '');
    }

    /**
     * Take $qty base pieces out of an ordered FIFO allocation after skipping $skip pieces.
     * Used so several unit lines of one product never double-use the same batch stock.
     */
    public static function sliceBatches(array $all, float $skip, float $qty): array
    {
        $result = [];
        $remainingSkip = $skip;
        $need = $qty;
        foreach ($all as $id => $batch) {
            $avail = (float)$batch['qty'];
            if ($remainingSkip >= $avail) {
                $remainingSkip -= $avail;
                continue;
            }
            $avail -= $remainingSkip;
            $remainingSkip = 0;
            $take = min($avail, $need);
            if ($take > 0) {
                $b = $batch;
                $b['qty'] = $take;
                $result[$id] = $b;
                $need -= $take;
            }
            if (round($need, 4) <= 0) {
                break;
            }
        }
        return $result;
    }
}
