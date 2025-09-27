<?php

namespace App\Imports;

use App\Models\Test;
use App\Models\TestCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TestsImport implements ToModel, WithHeadingRow
{
    // If your headers start on a different row, you can uncomment:
    // public function headingRow(): int { return 1; }

    public function model(array $row)
    {
        // laravel-excel normalizes headings to snake_case lowercase
        // Accept: test_category_id | category_id | category (name)
        $rawCategory = $row['test_category_id'] ?? $row['category_id'] ?? $row['category'] ?? null;

        $catId = null;
        if (!is_null($rawCategory)) {
            $raw = trim((string)$rawCategory);
            if ($raw !== '') {
                if (preg_match('/^\d+$/', $raw)) {
                    // numeric id provided
                    $catId = (int) $raw;
                } else {
                    // treat as category NAME → look up id (case-insensitive)
                    $catId = TestCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($raw)])->value('id');
                    // If you want to auto-create missing categories, swap the line above with:
                    // $catId = TestCategory::firstOrCreate(['name' => $raw], ['is_active' => true])->id;
                }
            }
        }

        // If we still don't have a category id, skip the row to avoid DB error
        if (!$catId) {
            return null; // returning null skips insertion for this row
        }

        // Normalize status lightly (default: active)
        $status = strtolower(trim((string)($row['status'] ?? 'active')));
        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'active';
        }

        return new Test([
            'name'             => $row['name'] ?? null,
            'status'           => $status,
            'test_category_id' => $catId,
            'price'            => $row['price'] ?? 0,     // optional
            'note'             => $row['note'] ?? null,   // optional
        ]);
    }
}
