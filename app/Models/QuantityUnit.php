<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * QuantityUnit represents packaging and base levels in the multi-level quantity system.
 * Level 1 = Top packaging (Cartons / Boxes)
 * Level 2 = Middle packaging (Packets / Bundles)
 * Level 3 = Base unit (Pieces / Units) - is_base = true, cannot be disabled.
 *
 * @property int $id
 * @property int $level
 * @property string $name
 * @property string|null $description
 * @property bool $is_base
 * @property bool $status
 */
class QuantityUnit extends Model
{
    protected $table = 'quantity_units';

    protected $casts = [
        'level' => 'int',
        'is_base' => 'bool',
        'status' => 'bool',
    ];

    protected $fillable = [
        'level',
        'name',
        'description',
        'is_base',
        'status',
    ];

    public static $validate = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
    ];

    public static $fields = [
        'name',
        'description',
    ];

    /**
     * Get all active quantity units ordered by level.
     * Fallback to default [Cartons, Packets, Pieces] if table doesn't exist or is empty.
     */
    public static function activeUnits(): Collection
    {
        try {
            $units = static::where('status', 1)->orderBy('level')->get();
            if ($units->isNotEmpty()) {
                return $units;
            }
        } catch (\Throwable $e) {
            // table might not exist in some contexts
        }

        return collect([
            (object)['id' => 1, 'level' => 1, 'name' => 'Cartons', 'description' => '', 'is_base' => false, 'status' => 1],
            (object)['id' => 2, 'level' => 2, 'name' => 'Packets', 'description' => '', 'is_base' => false, 'status' => 1],
            (object)['id' => 3, 'level' => 3, 'name' => 'Pieces', 'description' => '', 'is_base' => true, 'status' => 1],
        ]);
    }

    /**
     * Check if only pieces (base unit) is enabled and other packaging units are disabled.
     */
    public static function isOnlyPiecesEnabled(): bool
    {
        return static::activeUnits()->count() <= 1;
    }

    /**
     * Get all units (including inactive) ordered by level.
     */
    public static function allUnits(): Collection
    {
        try {
            $units = static::orderBy('level')->get();
            if ($units->isNotEmpty()) {
                return $units;
            }
        } catch (\Throwable $e) {
        }

        return static::activeUnits();
    }

    /**
     * Get the base unit (is_base = true, or level = 3).
     */
    public static function getBaseUnit()
    {
        try {
            $base = static::where('is_base', true)->first();
            if ($base) {
                return $base;
            }
            $level3 = static::where('level', 3)->first();
            if ($level3) {
                return $level3;
            }
        } catch (\Throwable $e) {
        }

        return (object)['id' => 3, 'level' => 3, 'name' => 'Pieces', 'description' => '', 'is_base' => true, 'status' => 1];
    }

    /**
     * Map of level => name for active units.
     */
    public static function activeLevelNames(): array
    {
        return static::activeUnits()->pluck('name', 'level')->toArray();
    }

    /**
     * Name for a specific level.
     */
    public static function nameForLevel(int $level, string $default = ''): string
    {
        $unit = static::allUnits()->firstWhere('level', $level);
        return $unit ? $unit->name : $default;
    }
}
