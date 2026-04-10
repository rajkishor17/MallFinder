// Fresh database client for store operations
// This file uses a unique export name to bypass Turbopack caching
import { PrismaClient } from '@prisma/client';

const globalForPrisma = globalThis as unknown as {
  freshPrisma: PrismaClient | undefined;
};

export const freshPrisma = globalForPrisma.freshPrisma ?? new PrismaClient({
  log: ['query', 'info', 'warn', 'error'],
});

if (process.env.NODE_ENV !== 'production') {
  globalForPrisma.freshPrisma = freshPrisma;
}

export default freshPrisma;
