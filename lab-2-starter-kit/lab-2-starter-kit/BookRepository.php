<?php

require_once 'Book.php';

class BookRepository {

	private string $filename;

	/**
	 * @param string $theFilename
	 */
	public function __construct(string $theFilename) {
		$this->filename = $theFilename;
	}

	/**
	 * @return array of Book objects
	 */
	public function getAllBooks(): array {
		// If the file doesn't exist, we have no books to read!
		if (!file_exists($this->filename)) {
			return [];
		}

		// If we get a falsy value back from file_get_contents, we won't have anything to parse to JSON
		$fileContents = file_get_contents($this->filename);
		if (!$fileContents) {
			return [];
		}

		// The string happens to be in JSON format, so pass it to json_decode
		// The 2nd parameter requests an associative array return value
		$decodedBooks = json_decode($fileContents, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return []; // A JSON error occurred in our parsing attempt -- return empty to the caller
		}

		// Create an empty list and fill the Book objects with the JSON
		$books = [];
		foreach ($decodedBooks as $bookData) {
			$books[] = (new Book())->fill($bookData);
		}

		// Return the array of Books back to the caller
		return $books;
	}

	/**
	 * @param Book $book
	 */
    public function saveBook(Book $book): void {
        // 1. read json file
        $fileContents = file_get_contents($this->filename);

        // 2. json encode
        $books = json_decode($fileContents, true);

        // 3. add the new book to the list
        $books[] = $book;

        // 4. write the list of books back to file
        file_put_contents($this->filename, json_encode($books, JSON_PRETTY_PRINT));
    }

	 /**
	  * Retrieves the book with the given ISBN, or null if no book with the specified ISBN is found.
	  * Note: for the purposes of this lab you may return the first occurrence if there are multiple books with the same ISBN in the file.
	  *
	  * @param string $isbn
	  * @return Book|null
	  */
    public function getBookByISBN(string $isbn): Book|null {
        //read json file to get the data of books, if exists
        $books = $this->getAllBooks();
        //compare the isbn, return matched book object if both isbn are the same
        foreach($books as $book) {
            if($book->getInternationalStandardBookNumber() === $isbn) {
                return $book;
            }
        }
        return null;
    }

	/**
	 * Retrieves the book with the given title, or null if no book of that title is found.
	 * Note: for the purposes of this lab you may return the first occurrence if there are multiple books of the same title.
	 *
	 * @param string $title
	 * @return Book|null
	 */
	public function getBookByTitle(string $title): Book|null {
        //read json file to get the data of books, if exists
        $books = $this->getAllBooks();
        //compare the title, return matched book object if titles are both the same
        foreach($books as $book) {
            if($book->getName() === $title) {
                return $book;
            }
        }
        return null;
	}

	/**
	 * Updates the book in the file with the given $isbn (the contents of that book is replaced by $newBook in the file)
	 * Hint: are you seeing the file have indexes added to the JSON? Look into https://www.php.net/manual/en/function.array-values.php
	 * @param string $isbn
	 * @param Book $newBook
	 */
	public function updateBook(string $isbn, Book $newBook): void {
        //read json file to get the data of books, if exists
        //put the indexes into the array of books
        $books = array_values($this->getAllBooks());
        //get selected book by isbn
        $selectedBook = $this->getBookByISBN($isbn);
        //replace the book which has the same isbn as the selected book with the new book
        for($i=0;$i<count($books);$i++){
            if($selectedBook == $books[$i]){
                $books[$i] = $newBook;
            }
        }
        //write the updated data to the json file
        file_put_contents($this->filename, json_encode($books, JSON_PRETTY_PRINT));
	}

	/**
	 * Deletes the book in the file with the given $isbn.
	 * Seeing indexes be added to the JSON? Look into https://www.php.net/manual/en/function.array-values.php
	 * @param string $isbn
	 */
	public function deleteBookByISBN(string $isbn): void {
		//read json file to get the data of books, if exists
        //put the indexes into the array of books
        $books = array_values($this->getAllBooks());
        //get selected book by isbn
        $selectedBook = $this->getBookByISBN($isbn);
        //delete the book which has the same isbn as the selected book
        for($i=0;$i<count($books);$i++){
            if($selectedBook == $books[$i]){
                unset($books[$i]);
            }
        }
        //write the updated data to the json file
        file_put_contents($this->filename, json_encode($books, JSON_PRETTY_PRINT));
	}
}

