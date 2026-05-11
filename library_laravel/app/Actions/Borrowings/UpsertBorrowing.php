<?php

namespace App\Actions\Borrowings;

use App\Models\FacultyBorrowing;
use Illuminate\Support\Arr;

class UpsertBorrowing
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(?int $borrowId, array $attributes): FacultyBorrowing
    {
        $data = Arr::only($attributes, [
            'faculty_name',
            'faculty_department',
            'faculty_contact',
            'book_title',
            'book_isbn',
            'borrow_date',
            'expected_return_date',
            'notes',
        ]);

        if ($borrowId) {
            $borrowing = FacultyBorrowing::findOrFail($borrowId);
            $borrowing->update($data);
            return $borrowing->refresh();
        }

        return FacultyBorrowing::create($data);
    }
}
