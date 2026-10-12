<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * Cross-database helper.
 * Returns driver-appropriate SQL fragments for PostgreSQL and MySQL.
 */
class DbHelper
{
    /** Check if the current connection is MySQL/MariaDB */
    public static function isMySQL(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    /** Check if the current connection is SQLite */
    public static function isSQLite(): bool
    {
        return DB::getDriverName() === 'sqlite';
    }

    /**
     * Month abbreviation label.
     * PG : TO_CHAR(col, 'Mon')
     * MySQL: DATE_FORMAT(col, '%b')
     * SQLite: strftime('%m', col)
     */
    public static function monthLabel(string $col): string
    {
        if (self::isMySQL()) {
            return "DATE_FORMAT({$col}, '%b')";
        }
        if (self::isSQLite()) {
            return "strftime('%m', {$col})";
        }
        return "TO_CHAR({$col}, 'Mon')";
    }

    /**
     * Year-Month sort key (YYYY-MM).
     * PG : TO_CHAR(col, 'YYYY-MM')
     * MySQL: DATE_FORMAT(col, '%Y-%m')
     * SQLite: strftime('%Y-%m', col)
     */
    public static function yearMonth(string $col): string
    {
        if (self::isMySQL()) {
            return "DATE_FORMAT({$col}, '%Y-%m')";
        }
        if (self::isSQLite()) {
            return "strftime('%Y-%m', {$col})";
        }
        return "TO_CHAR({$col}, 'YYYY-MM')";
    }

    /**
     * Cast a VARCHAR column to an integer for JOIN comparisons.
     * PG : col::bigint
     * MySQL: CAST(col AS UNSIGNED)
     * SQLite: CAST(col AS INTEGER)
     */
    public static function castInt(string $col): string
    {
        if (self::isMySQL()) {
            return "CAST({$col} AS UNSIGNED)";
        }
        if (self::isSQLite()) {
            return "CAST({$col} AS INTEGER)";
        }
        return "{$col}::bigint";
    }

    /**
     * Numeric sort for roll_no: numeric rows first, then alphabetic.
     * PG : (CASE WHEN roll_no ~ '^[0-9]+$' THEN roll_no::bigint ELSE 999999999 END) ASC, roll_no ASC
     * MySQL: (CASE WHEN roll_no REGEXP '^[0-9]+$' THEN CAST(roll_no AS UNSIGNED) ELSE 999999999 END) ASC, roll_no ASC
     */
    public static function rollNoOrder(string $col = 'roll_no'): string
    {
        if (self::isMySQL()) {
            return "(CASE WHEN {$col} REGEXP '^[0-9]+\$' THEN CAST({$col} AS UNSIGNED) ELSE 999999999 END) ASC, {$col} ASC";
        }
        return "(CASE WHEN {$col} ~ '^[0-9]+\$' THEN {$col}::bigint ELSE 999999999 END) ASC, {$col} ASC";
    }

    /**
     * Case-insensitive LIKE.
     * PG : ILIKE
     * MySQL: LIKE  (MySQL LIKE is already case-insensitive on utf8mb4_unicode_ci)
     */
    public static function ilike(): string
    {
        return self::isMySQL() ? 'LIKE' : 'ILIKE';
    }

    /**
     * Cast column to text (for LIKE on JSON-ish columns).
     * PG : col::text
     * MySQL: col  (already text/varchar)
     */
    public static function asText(string $col): string
    {
        return self::isMySQL() ? $col : "{$col}::text";
    }
}
