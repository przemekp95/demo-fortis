import http from 'k6/http';
import { check, fail, sleep } from 'k6';

export const options = {
  vus: 15,
  duration: '1m',
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<700'],
  },
};

const baseUrl = __ENV.K6_BASE_URL || 'http://localhost:8080';
const sessionCookie = __ENV.K6_ADMIN_SESSION_COOKIE;

if (!sessionCookie) {
  fail('Missing K6_ADMIN_SESSION_COOKIE environment variable.');
}

export default function () {
  const response = http.get(`${baseUrl}/admin/dashboard`, {
    redirects: 0,
    headers: {
      Cookie: sessionCookie,
    },
  });

  check(response, {
    'dashboard returns 200': (r) => r.status === 200,
  });

  sleep(1);
}
