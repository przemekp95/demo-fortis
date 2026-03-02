import http from 'k6/http';
import { check, fail, sleep } from 'k6';

export const options = {
  scenarios: {
    receipt_submit_burst: {
      executor: 'ramping-arrival-rate',
      startRate: 5,
      timeUnit: '1s',
      preAllocatedVUs: 20,
      maxVUs: 80,
      stages: [
        { target: 30, duration: '30s' },
        { target: 50, duration: '30s' },
        { target: 0, duration: '15s' },
      ],
    },
  },
  thresholds: {
    http_req_failed: ['rate<0.02'],
    http_req_duration: ['p(95)<900'],
  },
};

const baseUrl = __ENV.K6_BASE_URL || 'http://localhost:8080';
const sessionCookie = __ENV.K6_SESSION_COOKIE;
const csrfToken = __ENV.K6_CSRF_TOKEN;

if (!sessionCookie || !csrfToken) {
  fail('Missing K6_SESSION_COOKIE or K6_CSRF_TOKEN environment variable.');
}

export default function () {
  const payload = {
    receipt_number: `K6-${__VU}-${Date.now()}`,
    purchase_amount: '39.99',
    purchase_date: new Date().toISOString().slice(0, 10),
    device_fingerprint: `k6-device-${__VU}`,
  };

  const response = http.post(`${baseUrl}/participant/receipts`, payload, {
    redirects: 0,
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      Cookie: sessionCookie,
    },
  });

  check(response, {
    'submit returns redirect or validation error': (r) => r.status === 302 || r.status === 422,
  });

  sleep(0.2);
}
