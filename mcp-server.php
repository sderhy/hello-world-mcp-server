#!/usr/bin/env php
<?php
/**
 * Minimal MCP (Model Context Protocol) Server in PHP
 *
 * This server implements the basic MCP protocol over stdio transport.
 * It provides a simple "helloWorld" tool that greets users.
 */

class MCPServer {
    private $serverInfo = [
        'name' => 'hello-world-php-mcp',
        'version' => '1.0.0'
    ];

    private $tools = [];

    public function __construct() {
        // Register the hello world tool
        $this->registerTool('helloWorld', [
            'description' => 'A simple hello world tool that greets the user',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'description' => 'Name to greet, defaults to "world"',
                        'default' => 'world'
                    ]
                ]
            ]
        ], function($params) {
            $name = $params['name'] ?? 'world';
            return [
                'content' => [
                    [
                        'type' => 'text',
                        'text' => "Hello, {$name}!"
                    ]
                ]
            ];
        });
    }

    /**
     * Register a tool with the MCP server
     */
    public function registerTool($name, $schema, $handler) {
        $this->tools[$name] = [
            'schema' => $schema,
            'handler' => $handler
        ];
    }

    /**
     * Handle incoming JSON-RPC requests
     */
    public function handleRequest($request) {
        $method = $request['method'] ?? null;
        $id = $request['id'] ?? null;

        try {
            switch ($method) {
                case 'initialize':
                    return $this->handleInitialize($id, $request['params'] ?? []);

                case 'tools/list':
                    return $this->handleListTools($id);

                case 'tools/call':
                    return $this->handleCallTool($id, $request['params'] ?? []);

                case 'notifications/initialized':
                    // This is a notification, no response needed
                    return null;

                default:
                    return $this->errorResponse($id, -32601, "Method not found: {$method}");
            }
        } catch (Exception $e) {
            return $this->errorResponse($id, -32603, "Internal error: " . $e->getMessage());
        }
    }

    /**
     * Handle initialize request
     */
    private function handleInitialize($id, $params) {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => [
                'protocolVersion' => '2024-11-05',
                'capabilities' => [
                    'tools' => (object)[]
                ],
                'serverInfo' => $this->serverInfo
            ]
        ];
    }

    /**
     * Handle list tools request
     */
    private function handleListTools($id) {
        $toolsList = [];
        foreach ($this->tools as $name => $tool) {
            $toolsList[] = [
                'name' => $name,
                'description' => $tool['schema']['description'],
                'inputSchema' => $tool['schema']['inputSchema']
            ];
        }

        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => [
                'tools' => $toolsList
            ]
        ];
    }

    /**
     * Handle tool call request
     */
    private function handleCallTool($id, $params) {
        $toolName = $params['name'] ?? null;
        $arguments = $params['arguments'] ?? [];

        if (!isset($this->tools[$toolName])) {
            return $this->errorResponse($id, -32602, "Tool not found: {$toolName}");
        }

        $tool = $this->tools[$toolName];
        $result = call_user_func($tool['handler'], $arguments);

        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result
        ];
    }

    /**
     * Create an error response
     */
    private function errorResponse($id, $code, $message) {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ];
    }

    /**
     * Send a response to stdout
     */
    private function sendResponse($response) {
        if ($response !== null) {
            fwrite(STDOUT, json_encode($response) . "\n");
            fflush(STDOUT);
        }
    }

    /**
     * Log debug messages to stderr
     */
    private function log($message) {
        fwrite(STDERR, "[MCP Server] {$message}\n");
        fflush(STDERR);
    }

    /**
     * Start the server and listen for requests
     */
    public function start() {
        $this->log("Starting PHP MCP Server...");
        $this->log("Server: {$this->serverInfo['name']} v{$this->serverInfo['version']}");

        // Read from stdin line by line
        while (!feof(STDIN)) {
            $line = fgets(STDIN);
            if ($line === false || trim($line) === '') {
                continue;
            }

            $this->log("Received: " . trim($line));

            $request = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->log("JSON decode error: " . json_last_error_msg());
                continue;
            }

            $response = $this->handleRequest($request);
            if ($response !== null) {
                $this->log("Sending: " . json_encode($response));
                $this->sendResponse($response);
            }
        }

        $this->log("Server shutting down...");
    }
}

// Start the server
$server = new MCPServer();
$server->start();
