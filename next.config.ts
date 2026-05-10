import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  output: "standalone",
  /* config options here */
  typescript: {
    ignoreBuildErrors: true,
  },
  reactStrictMode: false,
  experimental: {
    // Enable optimizations
    optimizePackageImports: ['lucide-react', 'framer-motion'],
  },
};

export default nextConfig;
