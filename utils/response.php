<?php 
class Response {


    public function send($state, $message, $extras = []){
        $response = array_merge(
        [
            'state' => $state,
            'message' => $message
        ],
        $extras
    );
        echo (json_encode($response));
      }
}

?>