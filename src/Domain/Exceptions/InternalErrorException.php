<?php
namespace UesPlay\Domain\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InternalErrorException extends Exception {
    public function report(): void{}

    public function render(Request $request)
    {
        return response()->json([
            "code"=> 500,
            "status"=> "Internal error",
            "message"=> $this->message
        ],500);
    }
}
