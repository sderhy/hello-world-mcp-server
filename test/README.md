# Hello World MCP Server - PHP Implementation

A minimal Model Context Protocol (MCP) server implementation in pure PHP with no dependencies. This demonstrates how MCP works at a fundamental level.

## How MCP Works

The Model Context Protocol (MCP) enables AI assistants like Claude to interact with external tools and data sources. Here's the architecture:

```
┌─────────────┐         JSON-RPC over stdio        ┌─────────────┐
│             │ ────────────────────────────────> │             │
│  MCP Client │                                    │  MCP Server │
│ (Claude)    │ <──────────────────────────────── │    (PHP)    │
└─────────────┘                                    └─────────────┘
```

### Key Concepts

1. **Transport**: Communication happens via stdio (standard input/output)
2. **Protocol**: JSON-RPC 2.0 format for all messages
3. **Tools**: Functions the server exposes that the client can call
4. **Schema**: Each tool has an input schema describing its parameters

### Message Flow

1. **Initialize**: Client sends initialize request with capabilities
2. **List Tools**: Client requests available tools
3. **Call Tool**: Client invokes a tool with arguments
4. **Response**: Server returns results

## Files

- `mcp-server.php` - The MCP server implementation
- `mcp-config.json` - Configuration for Claude Code
- `test-mcp.sh` - Test script to verify the server works
- `README.md` - This file

## Prerequisites

You need PHP installed on your system (PHP 7.4 or higher recommended).

### Install PHP on macOS

```bash
# Using Homebrew
brew install php

# Verify installation
php --version
```

### Install PHP on Linux

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install php-cli

# Fedora/CentOS
sudo dnf install php-cli

# Verify installation
php --version
```

## Testing the Server

Run the test script to verify the server works correctly:

```bash
./test-mcp.sh
```

You should see JSON responses for:
- Server initialization
- List of available tools
- Tool execution results

## Manual Testing

You can also test manually by piping JSON-RPC requests:

```bash
# Initialize the server
echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1.0.0"}}}' | php mcp-server.php

# List available tools
echo '{"jsonrpc":"2.0","id":1,"method":"tools/list","params":{}}' | php mcp-server.php

# Call the helloWorld tool
echo '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"helloWorld","arguments":{"name":"PHP"}}}' | php mcp-server.php
```

## Using with Claude Code

To use this MCP server with Claude Code:

1. **Option A**: Copy the configuration to Claude Code's global config

   ```bash
   # Location of Claude Code config (adjust path if needed)
   # macOS/Linux: ~/.claude-code/mcp.json
   # Windows: %APPDATA%\claude-code\mcp.json
   ```

   Add this to the `mcpServers` section:

   ```json
   {
     "mcpServers": {
       "hello-world-php": {
         "command": "php",
         "args": ["mcp-server.php"],
         "cwd": "/Users/sergederhy/work/hello-world-mcp/test",
         "description": "Hello World MCP server in PHP"
       }
     }
   }
   ```

2. **Option B**: Use the local config file

   The `mcp-config.json` file in this directory can be used as a reference.

3. **Restart Claude Code** to load the new MCP server

4. **Verify**: Claude Code should now have access to the `helloWorld` tool

## Understanding the Code

### Server Structure

```php
class MCPServer {
    // Server metadata
    private $serverInfo = ['name' => '...', 'version' => '...'];

    // Registered tools
    private $tools = [];

    // Register a tool
    public function registerTool($name, $schema, $handler) { ... }

    // Handle incoming requests
    public function handleRequest($request) { ... }

    // Start listening on stdio
    public function start() { ... }
}
```

### Request Handling

The server handles these JSON-RPC methods:

1. **initialize** - Handshake and capability negotiation
2. **tools/list** - Returns available tools and their schemas
3. **tools/call** - Executes a tool with given arguments

### Adding New Tools

To add a new tool, register it in the constructor:

```php
$this->registerTool('myTool', [
    'description' => 'Description of what this tool does',
    'inputSchema' => [
        'type' => 'object',
        'properties' => [
            'param1' => [
                'type' => 'string',
                'description' => 'Parameter description'
            ]
        ]
    ]
], function($params) {
    // Your tool logic here
    return [
        'content' => [
            ['type' => 'text', 'text' => 'Result']
        ]
    ];
});
```

## Architecture Decisions

This implementation is intentionally minimal to demonstrate MCP concepts:

1. **No dependencies**: Pure PHP, no composer packages
2. **Single file**: All code in one file for easy understanding
3. **Synchronous**: Simple blocking I/O for clarity
4. **Basic validation**: Minimal error handling to keep code readable

For production use, consider:
- Adding input validation
- Better error handling
- Logging to files instead of stderr
- Using async I/O for better performance
- Adding more sophisticated tool schemas

## Common Issues

### PHP not found
```bash
# Install PHP first (see Prerequisites section)
brew install php  # macOS
```

### Permission denied
```bash
chmod +x mcp-server.php
chmod +x test-mcp.sh
```

### Server not responding
Check stderr output for debug messages:
```bash
echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{}}' | php mcp-server.php 2>&1
```

## Next Steps

1. **Experiment**: Modify the helloWorld tool to do something different
2. **Add Tools**: Create new tools (calculator, file reader, etc.)
3. **Connect to Claude**: Configure Claude Code to use this server
4. **Learn More**: Read the [MCP documentation](https://modelcontextprotocol.io/)

## License

ISC
