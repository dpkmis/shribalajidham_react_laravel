import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api/v1';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 15000,
});

// Request interceptor — attach token if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor — extract data
api.interceptors.response.use(
  (response) => response.data,
  (error) => {
    const message = error.response?.data?.message || error.message || 'Something went wrong';
    return Promise.reject({ message, status: error.response?.status });
  }
);

// ── Auth ─────────────────────────────────────────────────
export const authAPI = {
  login: (email, password) =>
    api.post('/auth/login', { email, password, device_name: 'web-browser' }),

  register: (data) =>
    api.post('/auth/register', data),

  logout: () =>
    api.post('/auth/logout'),

  profile: () =>
    api.get('/auth/profile'),
};

// ── Room Types (Master Data) ────────────────────────────
export const roomTypeAPI = {
  list: (params = {}) =>
    api.get('/room-types', { params: { with_features: true, active_only: true, ...params } }),

  get: (id) =>
    api.get(`/room-types/${id}`),
};

// ── Room Features / Amenities ───────────────────────────
export const roomFeatureAPI = {
  list: (params = {}) =>
    api.get('/room-features', { params: { active_only: true, ...params } }),
};

// ── Rooms ────────────────────────────────────────────────
export const roomAPI = {
  list: (params = {}) =>
    api.get('/rooms', { params }),

  available: (params = {}) =>
    api.get('/rooms/available', { params }),

  statusSummary: (params = {}) =>
    api.get('/rooms/status-summary', { params }),
};

// ── Guests ───────────────────────────────────────────────
export const guestAPI = {
  create: (data) =>
    api.post('/guests', data),

  search: (q, params = {}) =>
    api.get('/guests/search', { params: { q, ...params } }),
};

// ── Bookings ─────────────────────────────────────────────
export const bookingAPI = {
  create: (data) =>
    api.post('/bookings', data),

  list: (params = {}) =>
    api.get('/bookings', { params }),

  get: (id) =>
    api.get(`/bookings/${id}`),

  today: (params = {}) =>
    api.get('/bookings/today', { params }),

  checkin: (id) =>
    api.post(`/bookings/${id}/checkin`),

  checkout: (id) =>
    api.post(`/bookings/${id}/checkout`),

  cancel: (id, reason) =>
    api.post(`/bookings/${id}/cancel`, { reason }),
};

// ── Properties ───────────────────────────────────────────
export const propertyAPI = {
  list: () =>
    api.get('/properties'),

  get: (id) =>
    api.get(`/properties/${id}`),
};

// ── Dashboard ────────────────────────────────────────────
export const dashboardAPI = {
  stats: (params = {}) =>
    api.get('/dashboard/stats', { params }),
};

// ── Public (No Auth) ─────────────────────────────────────
export const publicAPI = {
  roomTypes: (params = {}) =>
    api.get('/public/room-types', { params: { with_features: true, active_only: true, ...params } }),

  roomFeatures: (params = {}) =>
    api.get('/public/room-features', { params: { active_only: true, ...params } }),

  availableRooms: (params = {}) =>
    api.get('/public/rooms/available', { params }),

  createBooking: (data) =>
    api.post('/public/booking', data),

  properties: () =>
    api.get('/public/properties'),

  metadata: () =>
    api.get('/public/metadata'),

  tourPackages: () =>
    api.get('/public/tour-packages'),

  festivalOffers: () =>
    api.get('/public/festival-offers'),

  testimonials: () =>
    api.get('/public/testimonials'),

  blogPosts: () =>
    api.get('/public/blog-posts'),

  gallery: () =>
    api.get('/public/gallery'),

  nearbyAttractions: () =>
    api.get('/public/nearby-attractions'),
};

export default api;
