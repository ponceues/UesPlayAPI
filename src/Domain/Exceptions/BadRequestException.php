<?php
namespace UesPlay\Domain\Exceptions;

use Exception;
use Illuminate\Http\Request;

class BadRequestException extends Exception {

    public function report(): void{}

    public function render(Request $request)
    {
        return response()->json([
            "code"=> 400,
            "status"=> "Bad request",
            "message"=> $this->message
        ],400);
    }
}
