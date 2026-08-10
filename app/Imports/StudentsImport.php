<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Plain array import (no WithHeadingRow) so the caller controls header
 * matching itself -- real-world uploaded sheets have inconsistent header
 * casing/spacing, and WithHeadingRow's auto-slugified keys are less
 * predictable to match against than doing our own trimmed/lowercased lookup.
 */
class StudentsImport implements ToArray
{
    public function array(array $array)
    {
        return $array;
    }
}
