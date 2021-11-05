<?php

namespace App\Response;
class Response {
    private $message;
    private $status;
    private $data;
    public function __construct($message=null,$data=null,$status=null)
    {
        $this->message= $message;
        $this->status = $status;
        $this->data = $data;
    }
    public function createJsonResponse(){
        return response()->json(['message'=>$this->message,'status' => $this->status,'data' =>$this->data]);
    }
}