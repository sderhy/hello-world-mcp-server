#!/usr/bin/env node
import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { z } from "zod";

const server = new McpServer({
  name: "hello-world-mcp",
  version: "1.0.0"
});

server.registerTool(
  "helloWorld",
  {
    title: "Hello World Tool",
    description: "A simple hello world tool that greets the user",
    inputSchema: {
      name: z.string().optional().default("world").describe("Name to greet, defaults to 'world'")
    }
  },
  async ({ name = "world" }) => {
    const greeting = `Hello, ${name}!`;

    return {
      content: [{
        type: "text",
        text: greeting
      }]
    };
  }
);

async function startServer() {
  try {
    const transport = new StdioServerTransport();
    await server.connect(transport);
  } catch (error) {
    console.error(`Fatal error during server initialization: ${error.message}`);
    process.exit(1);
  }
}

startServer();