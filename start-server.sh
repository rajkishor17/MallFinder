#!/bin/bash
cd /home/z/my-project

# Generate Prisma client if needed
if [ ! -f "node_modules/.prisma/client/index.js" ]; then
    mv .config .config.bak 2>/dev/null || true
    npx prisma generate --schema=./prisma/schema.prisma
    mv .config.bak .config 2>/dev/null || true
fi

# Clean .next cache if corrupted
rm -rf .next 2>/dev/null

# Start server with auto-restart
echo "Starting MallFinder server..."
while true; do
    npm run dev
    echo "Server crashed, restarting in 3 seconds..."
    sleep 3
done
