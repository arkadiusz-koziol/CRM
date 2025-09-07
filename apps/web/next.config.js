/** @type {import('next').NextConfig} */
const nextConfig = {
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
