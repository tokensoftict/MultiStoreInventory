<?php

namespace App\Services;

use App\Models\QuantityUnit;
use Illuminate\Support\Str;

/**
 * Single source of truth for the stock Excel template used by multi-level (Carton/Packet/Piece) stores.
 * The exporter writes these headings and the importer reads the very same (slugged) keys,
 * so the template and the import can never drift apart.
 */
class MultiLevelStockSheet
{
    public const COST = 'cost';
    public const SELLING = 'selling';
    public const QTY = 'qty';
    public const PER_PARENT = 'per_parent';

    /**
     * Active unit levels, largest first, each with its headings and import keys.
     * [['level'=>1,'name'=>'Cartons','is_base'=>false,'columns'=>['cost'=>['heading'=>..,'key'=>..], ...]], ...]
     */
    public static function levels(): array
    {
        $levels = [];
        foreach (QuantityUnit::activeUnits()->sortBy('level')->values() as $idx => $unit) {
            $label = strtoupper(trim($unit->name));
            $columns = [
                self::COST => self::column("$label COST PRICE"),
                self::SELLING => self::column("$label SELLING PRICE"),
                self::QTY => self::column("$label QUANTITY"),
            ];
            if ($idx > 0) {
                $columns[self::PER_PARENT] = self::column("$label PER PARENT UNIT");
            }
            $levels[] = [
                'level' => (int)$unit->level,
                'name' => $unit->name,
                'is_base' => (bool)$unit->is_base,
                'columns' => $columns,
            ];
        }
        return $levels;
    }

    public static function column(string $heading): array
    {
        return ['heading' => $heading, 'key' => Str::slug($heading, '_')];
    }

    /** Flat list of headings for all unit columns, in sheet order. */
    public static function unitHeadings(): array
    {
        $headings = [];
        foreach (self::levels() as $level) {
            foreach ($level['columns'] as $col) {
                $headings[] = $col['heading'];
            }
        }
        return $headings;
    }

    public static function headings(bool $template): array
    {
        $head = $template ? [] : ['ID'];
        $head = array_merge($head, ['NAME', 'PRODUCT TYPE', 'CATEGORY', 'MANUFACTURER'], self::unitHeadings());
        if ($template) {
            $head[] = 'SUPPLIER NAME';
            $head[] = 'SUPPLIER PHONE';
        }
        return $head;
    }

    /** Human readable guide placed on the "Instructions" sheet. */
    public static function instructions(): array
    {
        $levels = self::levels();
        $rows = [
            ['HOW TO FILL THIS TEMPLATE'],
            [''],
            ['NAME is required. PRODUCT TYPE: SINGLE (default) or one of the values on the "Product Types" sheet.'],
            ['CATEGORY / MANUFACTURER: use an existing name or ID, or a new name to have it created.'],
        ];
        $names = array_map(function ($l) {
            return $l['name'];
        }, $levels);
        $rows[] = ['Units in this store (largest to smallest): ' . implode(' > ', $names)];
        $rows[] = [''];
        foreach ($levels as $idx => $level) {
            if ($level['is_base']) {
                $rows[] = ["{$level['name']} is the base unit and is always used by every product. Enter its prices and quantity (loose pieces)."];
            } else {
                $rows[] = ["{$level['name']} is optional. Leave all its columns blank if the product is not packaged in {$level['name']}."];
            }
            if (isset($level['columns'][self::PER_PARENT])) {
                $rows[] = ["   '{$level['columns'][self::PER_PARENT]['heading']}' = how many {$level['name']} are inside ONE of the next larger unit used by the product (required when the product uses more than one unit)."];
            }
        }
        $rows[] = [''];
        $rows[] = ['Prices are per ONE of that unit. QUANTITY columns are opening stock and may be entered in any unit, e.g. 2 Cartons + 3 Packets + 4 Pieces.'];
        $rows[] = ['SUPPLIER NAME / PHONE are used for the opening stock batch.'];
        return $rows;
    }
}
