import { redirect } from 'next/navigation';
import { getSession } from '@/lib/auth';
import { AdminLoginForm } from '@/components/auth/AdminLoginForm';

export const metadata = {
  title: 'Admin Login - MallFinder CMS',
  description: 'Sign in to access the MallFinder CMS admin dashboard',
  robots: 'noindex, nofollow',
};

export default async function AdminLoginPage() {
  // Check if already authenticated
  const user = await getSession();

  if (user) {
    redirect('/admin');
  }

  return <AdminLoginForm />;
}
