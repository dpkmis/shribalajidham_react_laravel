import React, { createContext, useContext, useState, useEffect } from 'react';
import { publicAPI } from '../services/api';

const SiteContext = createContext({});

// Fallback values if API is unavailable
const fallbackMeta = {
  general: {
    site_name: 'Shri BalaJi Dham Hotel',
    tagline: 'Under Divine Observation',
    hero_badge: 'Best Budget Hotel in Mathura',
    short_description: 'Clean AC rooms near Mathura Railway Station with complimentary breakfast, temple tour guidance, and warm hospitality.',
    logo: '',
  },
  contact: {
    phone: '+918755550410',
    phone_display: '+91 8755550410',
    whatsapp: '918755550410',
    email: 'sribalajidhamhotel@gmail.com',
    address: '580 Shankar Gali, Natwar Nagar, Dhauli Pyau, Mathura, Uttar Pradesh — 281001',
    address_short: 'Dhauli Pyau, Mathura, Uttar Pradesh',
    google_maps_url: 'https://maps.google.com/?q=Shri+Balaji+Dham+Hotel+Mathura',
  },
  social: {
    facebook: '', instagram: '', youtube: '', twitter: '',
  },
  hero: {
    hero_title: 'Welcome to Shri BalaJi Dham Hotel in Mathura',
    hero_subtitle: 'Clean & comfortable rooms near Krishna Janmabhoomi with complimentary breakfast, free WiFi, and temple tour guidance.',
    hero_stat_1: '1000+ Happy Pilgrims',
    hero_stat_2: '4.0 Google Rating',
    hero_stat_3: '10 min from Railway Station',
    hero_stat_4: '24/7 Room Service',
  },
  policies: {
    checkin_time: '2:00 PM',
    checkout_time: '12:00 PM',
    nearest_railway: 'Mathura Junction (MTJ) — 800m / 10 min walk',
    nearest_airport: 'Agra Airport (Kheria) — 57 km / 1 hr 15 min',
  },
  booking: {
    min_rate: '2376',
    currency: '₹',
    whatsapp_booking_msg: 'Namaste! I would like to book a room at Shri Balaji Dham Hotel, Mathura. Please share availability and rates.',
  },
  seo: {},
};

export function SiteProvider({ children }) {
  const [meta, setMeta] = useState(fallbackMeta);
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    publicAPI.metadata()
      .then((res) => {
        if (res.success && res.data) {
          // Deep merge: API data on top of fallback
          const merged = { ...fallbackMeta };
          for (const [group, items] of Object.entries(res.data)) {
            merged[group.toLowerCase()] = { ...(merged[group.toLowerCase()] || {}), ...items };
          }
          setMeta(merged);
        }
      })
      .catch(() => {})
      .finally(() => setLoaded(true));
  }, []);

  // Convenience getters
  const get = (group, key, fallback = '') => meta[group]?.[key] || fallback;
  const contact = meta.contact || {};
  const social = meta.social || {};
  const hero = meta.hero || {};
  const general = meta.general || {};
  const policies = meta.policies || {};
  const booking = meta.booking || {};

  return (
    <SiteContext.Provider value={{ meta, loaded, get, contact, social, hero, general, policies, booking }}>
      {children}
    </SiteContext.Provider>
  );
}

export function useSite() {
  return useContext(SiteContext);
}

export default SiteContext;
