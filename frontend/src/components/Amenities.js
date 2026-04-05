import React, { useState, useEffect } from 'react';
import {
  FaWifi, FaParking, FaUtensils, FaConciergeBell,
  FaShieldAlt, FaSnowflake, FaTv, FaCar,
  FaPray, FaFirstAid, FaCoffee, FaSuitcase,
  FaBroom, FaLock, FaPhone, FaBed, FaBath, FaSpinner, FaCheck
} from 'react-icons/fa';
import { publicAPI } from '../services/api';
import './Amenities.css';

// Fallback amenities if API is unavailable
const fallbackAmenities = [];

// Map icon names from backend to React Icons
const iconMap = {
  'FaWifi': <FaWifi />, 'FaSnowflake': <FaSnowflake />, 'FaTv': <FaTv />,
  'FaBath': <FaBath />, 'FaConciergeBell': <FaConciergeBell />,
  'FaParking': <FaParking />, 'FaBroom': <FaBroom />, 'FaUtensils': <FaUtensils />,
  'FaShieldAlt': <FaShieldAlt />, 'FaPray': <FaPray />, 'FaCar': <FaCar />,
  'FaLock': <FaLock />, 'FaCoffee': <FaCoffee />, 'FaBed': <FaBed />,
  'FaFirstAid': <FaFirstAid />, 'FaPhone': <FaPhone />, 'FaSuitcase': <FaSuitcase />,
  // Legacy mappings
  'bx bx-wifi': <FaWifi />, 'bx bx-wind': <FaSnowflake />, 'bx bx-building': <FaBath />,
};

const Amenities = () => {
  const [amenities, setAmenities] = useState(fallbackAmenities);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    publicAPI.roomFeatures()
      .then((res) => {
        if (res.success && res.data && res.data.length > 0) {
          const apiAmenities = res.data.map((f) => ({
            icon: iconMap[f.icon] || iconMap[f.code] || <FaCheck />,
            title: f.name,
            desc: f.description || '',
            highlight: false,
          }));
          // Merge: API features first, then remaining fallback amenities
          const apiNames = new Set(apiAmenities.map(a => a.title.toLowerCase()));
          const remaining = fallbackAmenities.filter(a => !apiNames.has(a.title.toLowerCase()));
          setAmenities([...apiAmenities, ...remaining]);
        }
      })
      .catch(() => {
        // Keep fallback
      })
      .finally(() => setLoading(false));
  }, []);

  return (
    <section id="amenities" className="section amenities-section">
      <div className="amenities-pattern"></div>
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— Our Facilities —</span>
          <h2 className="section-title">Best-in-Class Amenities</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            From spotlessly clean rooms to warm homely meals — we provide everything a pilgrim
            or traveller needs for a comfortable and worry-free stay in Mathura and Vrindavan.
          </p>
        </div>
        {loading ? (
          <div style={{ textAlign: 'center', padding: '60px 0' }}>
            <FaSpinner className="fa-spin" style={{ fontSize: '2rem', color: 'var(--gold)' }} />
          </div>
        ) : (
          <div className="amenities-grid">
            {amenities.map((item, i) => (
              <div key={i} className={`amenity-card ${item.highlight ? 'amenity-highlight' : ''}`}>
                <div className="amenity-icon-wrap">{item.icon}</div>
                <h4>{item.title}</h4>
                <p>{item.desc}</p>
                {item.highlight && <div className="amenity-shine"></div>}
              </div>
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export default Amenities;
