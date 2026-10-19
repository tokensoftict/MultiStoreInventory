<?php

namespace App\Services;

/**
 * Pure helpers for the optional Carton -> Packet -> Piece system.
 *
 * Units are plain arrays (so the logic is DB-free and unit-testable):
 *   ['id', 'name', 'level', 'parent_unit_id', 'units_per_parent']
 * parent_unit_id points to the next LARGER unit; units_per_parent says how many
 * of THIS unit fit in one parent (e.g. Packet.units_per_parent = 20 means
 * 1 Carton = 20 Packets). The Piece (level 3) is the base unit (multiplier 1).
 */
class InventoryQuantityService
{
    const SYSTEM_LEGACY = 'legacy';
    const SYSTEM_MULTI_LEVEL = 'multi_level';

    /** True only when the store is explicitly configured for the multi-level system. */
    public static function usesMultiLevel($store): bool
    {
        if (is_numeric($store)) {
            $store = \App\Models\Warehousestore::find($store);
        }
        if (!$store) {
            return false;
        }
        if (is_object($store) && !isset($store->quantity_system) && isset($store->id)) {
            $fresh = \App\Models\Warehousestore::find($store->id);
            if ($fresh) {
                if (session()->has('activeStore')) {
                    session()->put('activeStore', $fresh->toArray());
                }
                return ($fresh->quantity_system ?? self::SYSTEM_LEGACY) === self::SYSTEM_MULTI_LEVEL;
            }
        }
        return ($store->quantity_system ?? self::SYSTEM_LEGACY) === self::SYSTEM_MULTI_LEVEL;
    }

    /** Normalise Eloquent models / objects to arrays. */
    public static function normalize($units): array
    {
        $out = [];
        foreach ($units as $unit) {
            $u = is_array($unit) ? $unit : $unit->toArray();
            $out[] = [
                'id' => $u['id'] ?? null,
                'name' => $u['name'] ?? '',
                'level' => (int)($u['level'] ?? 0),
                'parent_unit_id' => $u['parent_unit_id'] ?? null,
                'units_per_parent' => isset($u['units_per_parent']) ? (float)$u['units_per_parent'] : null,
            ];
        }
        return $out;
    }

    /** How many base pieces make up ONE of the given unit. */
    public static function multiplier(array $units, $unitId): float
    {
        return self::multiplierInner($units, $unitId, []);
    }

    private static function multiplierInner(array $units, $unitId, array $visited): float
    {
        if (in_array($unitId, $visited, true)) {
            throw new \InvalidArgumentException('Circular unit hierarchy detected.');
        }
        $visited[] = $unitId;

        foreach ($units as $child) {
            if ($child['parent_unit_id'] !== null && (string)$child['parent_unit_id'] === (string)$unitId) {
                $perParent = (float)$child['units_per_parent'];
                if ($perParent <= 0) {
                    throw new \InvalidArgumentException('Unit conversion must be greater than zero.');
                }
                return $perParent * self::multiplierInner($units, $child['id'], $visited);
            }
        }
        return 1.0; // leaf = base piece
    }

    /** Convert a quantity of a given unit into base pieces. */
    public static function toBase(array $units, $unitId, $quantity): float
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative.');
        }
        return round((float)$quantity * self::multiplier($units, $unitId), 4);
    }

    /** Convert mixed quantities, e.g. [unitId => qty, ...], into base pieces. */
    public static function toBaseMixed(array $units, array $quantities): float
    {
        $total = 0.0;
        foreach ($quantities as $unitId => $qty) {
            $total += self::toBase($units, $unitId, (float)$qty);
        }
        return round($total, 4);
    }

    /**
     * Break a base quantity into units, largest first.
     * Returns [['id','name','level','quantity'], ...]  e.g. 450 => 2C, 5P, 0Pc.
     */
    public static function breakdown(array $units, $baseQuantity): array
    {
        $sorted = $units;
        usort($sorted, function ($a, $b) {
            return $a['level'] <=> $b['level'];
        });

        $remaining = (float)$baseQuantity;
        $result = [];
        $last = count($sorted) - 1;
        foreach ($sorted as $i => $unit) {
            $mult = self::multiplier($units, $unit['id']);
            if ($i === $last) {
                $qty = round($remaining, 4);
            } else {
                $qty = floor(round($remaining / $mult, 6));
                $remaining = round($remaining - $qty * $mult, 4);
            }
            $result[] = [
                'id' => $unit['id'],
                'name' => $unit['name'],
                'level' => $unit['level'],
                'quantity' => $qty,
            ];
        }
        return $result;
    }

    /** Human readable e.g. "2 Cartons, 5 Packets, 0 Pieces" (names as configured). */
    public static function breakdownLabel(array $units, $baseQuantity): string
    {
        $parts = [];
        foreach (self::breakdown($units, $baseQuantity) as $row) {
            $q = $row['quantity'];
            $parts[] = (floor($q) == $q ? (int)$q : $q) . ' ' . $row['name'];
        }
        return implode(', ', $parts);
    }

    /**
     * Validate a unit configuration. Returns a list of error strings (empty = valid).
     * Rules: names required, prices >= 0, conversions > 0, no self parent / cycles,
     * a single chain from a root to the Piece, levels strictly increasing downwards.
     */
    public static function validateUnits(array $units): array
    {
        $errors = [];
        if (count($units) < 1) {
            return ['At least one unit (the base unit) is required.'];
        }

        $ids = array_column($units, 'id');
        $roots = [];
        $childrenOf = [];

        foreach ($units as $u) {
            if (trim((string)$u['name']) === '') {
                $errors[] = 'Every unit must have a name.';
            }
            if ($u['parent_unit_id'] === null || $u['parent_unit_id'] === '') {
                $roots[] = $u;
                continue;
            }
            if ((string)$u['parent_unit_id'] === (string)$u['id']) {
                $errors[] = "Unit '{$u['name']}' cannot be its own parent.";
                continue;
            }
            if (!in_array($u['parent_unit_id'], $ids)) {
                $errors[] = "Unit '{$u['name']}' has an unknown parent.";
                continue;
            }
            if ($u['units_per_parent'] === null || $u['units_per_parent'] <= 0) {
                $errors[] = "Conversion for '{$u['name']}' must be greater than zero.";
            }
            $childrenOf[$u['parent_unit_id']][] = $u;
        }

        if (count($roots) !== 1) {
            $errors[] = 'Units must form exactly one chain with a single largest unit.';
        }
        foreach ($childrenOf as $parentId => $kids) {
            if (count($kids) > 1) {
                $errors[] = 'A unit cannot have more than one smaller unit.';
            }
        }
        if ($errors) {
            return array_values(array_unique($errors));
        }

        // Walk root -> leaf: detects cycles and checks level ordering.
        $current = $roots[0];
        $seen = [];
        $leaf = $current;
        while (true) {
            if (in_array($current['id'], $seen, true)) {
                return ['Circular unit hierarchy detected.'];
            }
            $seen[] = $current['id'];
            $leaf = $current;
            $next = $childrenOf[$current['id']][0] ?? null;
            if (!$next) {
                break;
            }
            if ($next['level'] <= $current['level']) {
                $errors[] = "Unit '{$next['name']}' must be smaller than '{$current['name']}'.";
            }
            $current = $next;
        }
        if (count($seen) !== count($units)) {
            $errors[] = 'All units must be linked into one chain (a unit is disconnected or circular).';
        }
        if ($leaf['level'] !== 3) {
            $errors[] = 'The smallest unit must be the Piece (base unit).';
        }

        return array_values(array_unique($errors));
    }
}
