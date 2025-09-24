# Hello World MCP Server

A simple Model Context Protocol (MCP) server that provides a hello world tool. This server serves as a boilerplate template to help developers quickly create and deploy new MCP servers. It demonstrates basic MCP server functionality with a greeting tool and can be easily modified to add custom tools and resources.

## Features

- **Hello World Tool**: Simple greeting tool that says "Hello, {name}" with default "world"
- **Input Validation**: Uses Zod schemas for robust parameter validation
- **Simple and Clean**: Minimal MCP server implementation for demonstration purposes

## Using as a Boilerplate

This project serves as a starting template for building your own MCP servers. To create a custom MCP server:

1. Clone or fork this repository
2. Modify `index.js` to add your own tools using `server.registerTool()`
3. Update `package.json` with your server name and description
4. Customize the README and configuration files as needed

The basic structure includes proper error handling, input validation with Zod, and MCP protocol compliance.

## Hello World Tool

The server provides a simple hello world tool that greets users with a customizable name.

### Tool Usage

The `helloWorld` tool accepts an optional `name` parameter and returns a greeting message.

**Parameters:**
- `name`: Name to greet (optional, defaults to "world")

**Example Response:**
```
Hello, world!
```

## Installation

### Global Installation (Recommended)

1. Install globally via npm:
   ```bash
   npm install -g hello-world-mcp
   ```

2. The `hello-world-mcp` command will be available system-wide

### Local Installation

1. Clone or download this repository
2. Install dependencies:
   ```bash
   npm install
   ```
3. Start the server:
   ```bash
   npm start
   ```

## Usage

### Starting the Server

```bash
npm start
```

### MCP Configuration

#### For Global Installation

Add this to your MCP client configuration:

```json
{
  "mcpServers": {
    "hello-world-mcp": {
      "command": "hello-world-mcp",
      "args": [],
      "env": {},
      "description": "Hello World MCP server with greeting tool"
    }
  }
}
```

#### For Local Installation

Add this to your MCP client configuration:

```json
{
  "mcpServers": {
    "hello-world-mcp": {
      "command": "node",
      "args": ["index.js"],
      "cwd": "/path/to/hello-world-mcp",
      "env": {},
      "description": "Hello World MCP server with greeting tool"
    }
  }
}
```

## Usage Examples

### Hello World Tool

```json
{
  "name": "helloWorld",
  "arguments": {
    "name": "Alice"
  }
}
```

**Response:**
```json
{
  "content": [
    {
      "type": "text",
      "text": "Hello, Alice!"
    }
  ]
}
```

### Default Greeting

```json
{
  "name": "helloWorld",
  "arguments": {}
}
```

**Response:**
```json
{
  "content": [
    {
      "type": "text",
      "text": "Hello, world!"
    }
  ]
}
```

## Available Tools

### Hello World Tool

#### `helloWorld`
A simple greeting tool that says "Hello, {name}" with default "world".

**Parameters:**
- `name`: Name to greet (optional, defaults to "world")

### Hello World Examples

#### Basic Greeting

```json
{
  "name": "helloWorld",
  "arguments": {
    "name": "Alice"
  }
}
```

**Response:**
```json
{
  "content": [
    {
      "type": "text",
      "text": "Hello, Alice!"
    }
  ]
}
```

#### Default Greeting

```json
{
  "name": "helloWorld",
  "arguments": {}
}
```

**Response:**
```json
{
  "content": [
    {
      "type": "text",
      "text": "Hello, world!"
    }
  ]
}
```

## Response Format

The hello world tool returns a simple text response:

```json
{
  "content": [
    {
      "type": "text",
      "text": "Hello, world!"
    }
  ]
}
```

## Dependencies

- `@modelcontextprotocol/sdk`: MCP SDK for server implementation
- `zod`: Schema validation library

## License

ISC