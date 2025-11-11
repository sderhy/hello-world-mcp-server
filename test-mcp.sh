#!/bin/bash
# Test script for the PHP MCP server

echo "Testing PHP MCP Server..."
echo ""

# Test 1: Initialize
echo "Test 1: Initialize"
echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test-client","version":"1.0.0"}}}' | php mcp-server.php | head -1
echo ""

# Test 2: List Tools
echo "Test 2: List Tools"
(
  echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test-client","version":"1.0.0"}}}'
  echo '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}'
) | php mcp-server.php | tail -1
echo ""

# Test 3: Call helloWorld with default
echo "Test 3: Call helloWorld (default)"
(
  echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test-client","version":"1.0.0"}}}'
  echo '{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"helloWorld","arguments":{}}}'
) | php mcp-server.php | tail -1
echo ""

# Test 4: Call helloWorld with custom name
echo "Test 4: Call helloWorld with custom name"
(
  echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test-client","version":"1.0.0"}}}'
  echo '{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"helloWorld","arguments":{"name":"PHP Developer"}}}'
) | php mcp-server.php | tail -1
echo ""

echo "Tests completed!"
