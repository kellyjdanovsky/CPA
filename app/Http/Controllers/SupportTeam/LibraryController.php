<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookRequest;
use App\Models\MyClass;
use App\User;
use App\Helpers\Qs;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        $books = Book::with('my_class')->get();
        $loans = BookRequest::with(['book', 'user'])->where('returned', 0)->get();
        
        $totalBooks = $books->sum('total_copies');
        $issuedBooks = $books->sum('issued_copies');
        $availableBooks = $totalBooks - $issuedBooks;
        
        $overdueLoans = $loans->filter(function($loan) {
            return \Carbon\Carbon::parse($loan->end_date)->isPast();
        });

        return view('pages.support_team.library.index', compact('books', 'loans', 'overdueLoans', 'totalBooks', 'availableBooks', 'issuedBooks'));
    }

    public function create()
    {
        $classes = MyClass::all();
        return view('pages.support_team.library.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'book_type' => 'required|string|max:100',
            'location' => 'nullable|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'my_class_id' => 'nullable|exists:my_classes,id',
        ]);

        $data['issued_copies'] = 0;
        Book::create($data);

        return Qs::goWithSuccess();
    }

    public function edit($id)
    {
        $book = Book::findOrFail(Qs::decodeHash($id));
        $classes = MyClass::all();
        return view('pages.support_team.library.edit', compact('book', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail(Qs::decodeHash($id));

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'book_type' => 'required|string|max:100',
            'location' => 'nullable|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'my_class_id' => 'nullable|exists:my_classes,id',
        ]);

        $book->update($data);

        return Qs::goWithSuccess();
    }

    public function destroy($id)
    {
        $book = Book::findOrFail(Qs::decodeHash($id));
        $book->delete();

        return Qs::goWithSuccess();
    }

    public function loanForm($book_id)
    {
        $book = Book::findOrFail(Qs::decodeHash($book_id));
        $users = User::whereIn('user_type', ['student', 'teacher'])->get();
        return view('pages.support_team.library.loans', compact('book', 'users'));
    }

    public function issueLoan(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $book = Book::findOrFail($data['book_id']);

        if ($book->total_copies - $book->issued_copies <= 0) {
            return Qs::goWithDanger('Aucun exemplaire disponible.');
        }

        $data['returned'] = 0;
        $data['status'] = 'Active';

        BookRequest::create($data);

        $book->increment('issued_copies');

        return Qs::goWithSuccess();
    }

    public function returnBook(Request $request, $request_id)
    {
        $loan = BookRequest::findOrFail(Qs::decodeHash($request_id));

        if ($loan->returned == 0) {
            $loan->update(['returned' => 1, 'status' => 'Returned']);
            $loan->book->decrement('issued_copies');
        }

        return Qs::goWithSuccess();
    }

    public function exportExcel(Request $request)
    {
        // This is a minimal implementation relying on basic PhpSpreadsheet usage
        $books = Book::with('my_class')->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Titre');
        $sheet->setCellValue('B1', 'Auteur');
        $sheet->setCellValue('C1', 'Catégorie');
        $sheet->setCellValue('D1', 'Emplacement');
        $sheet->setCellValue('E1', 'Exemplaires total');
        $sheet->setCellValue('F1', 'Exemplaires disponibles');
        
        $row = 2;
        foreach ($books as $book) {
            $sheet->setCellValue('A'.$row, $book->name);
            $sheet->setCellValue('B'.$row, $book->author);
            $sheet->setCellValue('C'.$row, $book->book_type);
            $sheet->setCellValue('D'.$row, $book->location);
            $sheet->setCellValue('E'.$row, $book->total_copies);
            $sheet->setCellValue('F'.$row, ($book->total_copies - $book->issued_copies));
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Catalogue_Bibliotheque.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function printCard($id)
    {
        $loan = BookRequest::with(['book', 'user'])->findOrFail(Qs::decodeHash($id));
        return view('pages.support_team.library.print_card', compact('loan'));
    }
}
