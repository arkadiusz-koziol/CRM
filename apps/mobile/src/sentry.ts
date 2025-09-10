
import * as Sentry from 'sentry-expo';
import { getMobileEnv } from '@skytech/config';

const env = getMobileEnv();

Sentry.init({
  dsn: env.SENTRY_DSN,
  enableInExpoDevelopment: true,
  debug: env.FEATURE_DEBUG_LOGS,
  environment: env.SENTRY_ENVIRONMENT,
  tracesSampleRate: 1.0,
  beforeSend(event) {
    // Filter out development logs in production
    if (env.SENTRY_ENVIRONMENT === 'production' && event.level === 'debug') {
      return null;
    }
    return event;
  },
});
