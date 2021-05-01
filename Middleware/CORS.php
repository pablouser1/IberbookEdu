<?php
namespace Middleware;
use Leaf\Middleware;
class CORS extends Middleware {
    public function call() {
        $client = getenv("INSTANCE_FRONTEND");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400");
        header("Access-Control-Allow-Origin: {$client}");

        if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
            http_response_code(200);
            exit;
        }
    }
}
