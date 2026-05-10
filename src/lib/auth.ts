import bcrypt from 'bcryptjs';
import { cookies } from 'next/headers';
import { db } from './db';

const SALT_ROUNDS = 10;
const SESSION_COOKIE_NAME = 'admin_session';
const SESSION_DURATION = 24 * 60 * 60 * 1000; // 24 hours

export interface SessionUser {
  id: string;
  email: string;
  name: string | null;
  role: string;
}

// Hash password
export async function hashPassword(password: string): Promise<string> {
  return bcrypt.hash(password, SALT_ROUNDS);
}

// Compare password
export async function comparePassword(password: string, hashedPassword: string): Promise<boolean> {
  return bcrypt.compare(password, hashedPassword);
}

// Create session
export async function createSession(userId: string): Promise<string> {
  const sessionToken = generateToken();
  const expiresAt = new Date(Date.now() + SESSION_DURATION);
  
  // Store session in database (we'll use a simple approach with cookies)
  // For production, you'd want to use a proper session store like Redis
  
  return sessionToken;
}

// Generate random token
function generateToken(): string {
  return Array.from(crypto.getRandomValues(new Uint8Array(32)))
    .map(b => b.toString(16).padStart(2, '0'))
    .join('');
}

// Set session cookie
export async function setSessionCookie(token: string, userId: string) {
  const cookieStore = await cookies();
  const expires = new Date(Date.now() + SESSION_DURATION);
  
  cookieStore.set(SESSION_COOKIE_NAME, JSON.stringify({ token, userId }), {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    expires,
    path: '/',
  });
}

// Get session from cookie
export async function getSession(): Promise<SessionUser | null> {
  try {
    const cookieStore = await cookies();
    const sessionCookie = cookieStore.get(SESSION_COOKIE_NAME);
    
    if (!sessionCookie?.value) {
      return null;
    }
    
    const { userId } = JSON.parse(sessionCookie.value);
    
    const user = await db.user.findUnique({
      where: { id: userId },
      select: {
        id: true,
        email: true,
        name: true,
        role: true,
      },
    });
    
    return user;
  } catch {
    return null;
  }
}

// Clear session
export async function clearSession() {
  const cookieStore = await cookies();
  cookieStore.delete(SESSION_COOKIE_NAME);
}

// Verify user credentials
export async function verifyCredentials(email: string, password: string): Promise<SessionUser | null> {
  const user = await db.user.findUnique({
    where: { email },
  });
  
  if (!user) {
    return null;
  }
  
  const isValid = await comparePassword(password, user.password);
  
  if (!isValid) {
    return null;
  }
  
  return {
    id: user.id,
    email: user.email,
    name: user.name,
    role: user.role,
  };
}

// Create default admin user
export async function createDefaultAdmin() {
  const existingAdmin = await db.user.findUnique({
    where: { email: 'admin@mallfinder.com' },
  });
  
  if (!existingAdmin) {
    const hashedPassword = await hashPassword('admin123');
    await db.user.create({
      data: {
        email: 'admin@mallfinder.com',
        password: hashedPassword,
        name: 'Admin',
        role: 'admin',
      },
    });
    console.log('Default admin user created: admin@mallfinder.com / admin123');
  }
}
