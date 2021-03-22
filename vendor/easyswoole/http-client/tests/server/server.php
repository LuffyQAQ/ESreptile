<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

$server = new Swoole\Websocket\Server("127.0.0.1", 9510);

$server->on('open', function ($server, $req) {
    echo "connection open: {$req->fd}\n";
});

$server->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
    $auth = $request->header['authorization'] ?? '';
    if ($auth) {
        $info = base64_decode(ltrim($auth, 'Basic '));
        [$user, $password] = explode(':', $info);
        if ($user == 'admin' && $password == '111111') {
            $response->end('success');
        } else {
            $response->end('error');
        }
    } else {
        $action = $request->get['action'] ?? 'json';
        switch ($action) {
            case 'json':
                $array = [
                    'title' => 'easyswoole',
                    'desc' => 'swoole framework'
                ];
                $response->end(json_encode($array));
                break;
            case 'jsonp':
                $array = [
                    'title' => 'easyswoole',
                    'desc' => 'swoole framework'
                ];
                $json = json_encode($array);
                $response->end("callback({$json})");
                break;
            case 'xml':
                $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
                $xml .= "<test>\n";
                $xml .= "<title>easyswoole</title>\n";
                $xml .= "<desc>swoole framework</desc>\n";
                $xml .= "</test>\n";
                $response->header('Content-Type', 'text/xml');
                $response->end($xml);
                break;
            default:
                $response->end('easyswoole');
        }
    }


});

$server->on('open', function (Swoole\WebSocket\Server $server, Swoole\Http\Request $request) {
    if (isset($request->header['aaa'])) {
        $server->push($request->fd, json_encode($request->header));
    }

    if (isset($request->cookie['ca-1'])) {
        $server->push($request->fd, json_encode($request->cookie));
    }
});

$server->on('message', function ($server, $frame) {
    echo "received message: {$frame->data}\n";
    $data = json_decode($frame->data, true);
    $server->push($frame->fd, 'call hello with arg:' . json_encode($data['content']));
});

$server->on('close', function ($server, $fd) {
    echo "connection close: {$fd}\n";
});

$server->start();