FROM node:20-alpine

# Install pnpm
RUN npm install -g pnpm@9.0.0

# Set working directory
WORKDIR /app

# Copy package files
COPY package.json pnpm-lock.yaml pnpm-workspace.yaml ./
COPY turbo.json ./

# Copy all package.json files for workspace dependencies
COPY packages/ ./packages/
COPY apps/ ./apps/

# Install dependencies
RUN pnpm install --no-frozen-lockfile

# Copy source code
COPY . .

# Build packages
RUN pnpm build --filter=@skytech/*

# Expose ports
EXPOSE 3000 8081 19000 19001 19002

# Default command (can be overridden)
CMD ["pnpm", "dev"]
