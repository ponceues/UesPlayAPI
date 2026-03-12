<?php

namespace UesPlay\Domain\Exceptions;
use Exception;
use Illuminate\Http\Request;

class NotFoundException extends Exception {
    
    public function report(): void{}

    public function render(Request $request)
    {
        return response()->json([
            "code"=> 404,
            "status"=> "Recurso no encontrado",
            "message"=> $this->message
        ],404);
    }
}
