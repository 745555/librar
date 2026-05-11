<?php

namespace App\Actions\Books;

use App\Models\Book;
use Illuminate\Support\Arr;

class UpsertBook
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(?int $bookId, array $attributes): Book
    {
        $data = Arr::only($attributes, [
            'book_title',
            'author',
            'isbn',
            'publisher',
            'publication_year',
            'quantity',
            'available_quantity',
            'department_id',
            'custom_department',
            'description',
        ]);

        if ($bookId) {
            $book = Book::findOrFail($bookId);
            $book->update($data);
            return $book->refresh();
        }

        return Book::create($data);
    }
}
