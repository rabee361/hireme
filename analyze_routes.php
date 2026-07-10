<?php
$routes = json_decode(file_get_contents('routes_list.json'), true);
$postman = json_decode(file_get_contents('hireme.postman_collection.json'), true);

$postmanUrls = [];
function extractUrls($items, &$postmanUrls) {
    foreach ($items as $item) {
        if (isset($item['item'])) {
            extractUrls($item['item'], $postmanUrls);
        } elseif (isset($item['request']['url'])) {
            $url = is_string($item['request']['url']) ? $item['request']['url'] : $item['request']['url']['raw'];
            // Normalize url
            $url = str_replace('{{base_url}}/', 'api/', $url);
            // Replace path parameters like {{ad_id}} with regex or generic pattern
            $url = preg_replace('/\{\{[^\}]+\}\}/', '{param}', $url);
            $method = strtoupper($item['request']['method']);
            $postmanUrls[] = $method . ' ' . $url;
        }
    }
}
extractUrls($postman['item'], $postmanUrls);

$missingRoutes = [];
foreach ($routes as $route) {
    if (strpos($route['uri'], 'api/') === 0) {
        $methods = explode('|', $route['method']);
        foreach ($methods as $method) {
            if ($method === 'HEAD') continue;
            
            $uri = $route['uri'];
            // Replace Laravel params like {ad} with {param}
            $normalizedUri = preg_replace('/\{[^\}]+\}/', '{param}', $uri);
            
            $signature = $method . ' ' . $normalizedUri;
            
            if (!in_array($signature, $postmanUrls)) {
                $missingRoutes[] = [
                    'method' => $method,
                    'uri' => $uri,
                    'action' => $route['action']
                ];
            }
        }
    }
}

echo json_encode($missingRoutes, JSON_PRETTY_PRINT);
