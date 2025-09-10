/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  experimental: {
    typedRoutes: true,
  },
  transpilePackages: [
    '@skytech/api-sdk',
    '@skytech/theme',
    '@skytech/ui',
    '@skytech/config',
    '@skytech/i18n',
  ],
};

module.exports = nextConfig;
