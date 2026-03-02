import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: 20,
  duration: '1m',
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<500'],
  },
};

const baseUrl = __ENV.K6_BASE_URL || 'http://localhost:8080';
const apiToken = __ENV.K6_API_TOKEN || 'fortis-public-api-token';

export default function () {
  const response = http.get(`${baseUrl}/api/v1/stats/public`, {
    headers: {
      Authorization: `Bearer ${apiToken}`,
    },
  });

  check(response, {
    'status is 200': (r) => r.status === 200,
  });

  sleep(1);
}
