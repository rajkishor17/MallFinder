import { PrismaClient } from '@prisma/client';

// Separate Prisma client for store operations to avoid Turbopack caching issues
// This ensures the Store model is always available
const globalForStorePrisma = globalThis as unknown as {
  storePrisma: PrismaClient | undefined
}

export const storeDb = globalForStorePrisma.storePrisma ?? new PrismaClient();

if (process.env.NODE_ENV !== 'production') {
  globalForStorePrisma.storePrisma = storeDb;
}
