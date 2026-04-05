import React from 'react';
import { FaTrain, FaCar, FaPlane, FaBus, FaMapMarkerAlt, FaPhone } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './HowToReach.css';

const HowToReach = () => {
  const { contact } = useSite();
  const phone = contact.phone_display || '+91 96390 66602';
  const routes = [
    {
      icon: <FaTrain />,
      mode: 'By Train',
      from: 'From Delhi / Agra / Jaipur',
      highlight: 'Most Popular',
      details: [
        { label: 'Station', value: 'Mathura Junction (MTJ)' },
        { label: 'Distance', value: '800m from hotel (10 min walk)' },
        { label: 'From Delhi', value: '2-3 hours (Taj Express, Bhopal Shatabdi)' },
        { label: 'From Agra', value: '40 minutes (Multiple trains daily)' },
        { label: 'From Jaipur', value: '4-5 hours (Golden Temple Mail)' },
        { label: 'From Mumbai', value: '16-18 hours (Rajdhani, Superfast)' },
      ],
      tip: `We offer FREE pick-up from Mathura Junction Railway Station. Call ${phone} with your train details!`
    },
    {
      icon: <FaCar />,
      mode: 'By Road',
      from: 'From Delhi / Agra / Jaipur',
      highlight: 'Convenient',
      details: [
        { label: 'From Delhi', value: '180 km via Yamuna Expressway (2.5-3 hrs)' },
        { label: 'From Agra', value: '58 km via NH-44 (1-1.5 hrs)' },
        { label: 'From Jaipur', value: '280 km via NH-21 (4-5 hrs)' },
        { label: 'From Lucknow', value: '400 km via Agra-Lucknow Expressway (5-6 hrs)' },
        { label: 'From Gurgaon', value: '160 km via Yamuna Expressway (2-2.5 hrs)' },
        { label: 'Parking', value: 'Free on-site parking at hotel' },
      ],
      tip: 'Our address for GPS: 580 Shankar Gali, Dhauli Pyau, Mathura — search "Shri Balaji Dham Hotel" on Google Maps.'
    },
    {
      icon: <FaPlane />,
      mode: 'By Air',
      from: 'Nearest Airports',
      highlight: 'For Outstation',
      details: [
        { label: 'Agra Airport', value: '57 km (1 hr 15 min by car)' },
        { label: 'Delhi IGI Airport', value: '165 km (3-3.5 hrs by car)' },
        { label: 'Jaipur Airport', value: '280 km (4.5 hrs by car)' },
        { label: 'Lucknow Airport', value: '400 km (5.5 hrs by car)' },
      ],
      tip: 'We arrange airport pick-up and drop service at reasonable rates. Book in advance for best availability.'
    },
    {
      icon: <FaBus />,
      mode: 'By Bus',
      from: 'UPSRTC & Private Buses',
      highlight: 'Budget Friendly',
      details: [
        { label: 'Bus Stand', value: 'Mathura New Bus Stand (2 km from hotel)' },
        { label: 'From Delhi ISBT', value: '3-4 hours (Frequent buses, ₹200-400)' },
        { label: 'From Agra ISBT', value: '1.5 hours (Every 30 mins, ₹80-150)' },
        { label: 'From Jaipur', value: '5-6 hours (Daily services, ₹400-600)' },
      ],
      tip: 'Auto-rickshaw from Mathura Bus Stand to our hotel costs approximately ₹30-50.'
    }
  ];

  return (
    <section id="how-to-reach" className="section howtoreach-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— How to Reach —</span>
          <h2 className="section-title">Getting to Shri BalaJi Dham Hotel, Mathura</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Conveniently located at Dhauli Pyau, just 800 meters from Mathura Junction Railway Station.
            Here's how to reach us from major cities across India.
          </p>
        </div>

        <div className="reach-grid">
          {routes.map((route, i) => (
            <article key={i} className="reach-card">
              <div className="reach-card-header">
                <div className="reach-icon">{route.icon}</div>
                <div>
                  <h3>{route.mode}</h3>
                  <span className="reach-from">{route.from}</span>
                </div>
                <span className="reach-badge">{route.highlight}</span>
              </div>
              <div className="reach-details">
                {route.details.map((d, j) => (
                  <div key={j} className="reach-detail-row">
                    <span className="reach-detail-label">{d.label}</span>
                    <span className="reach-detail-value">{d.value}</span>
                  </div>
                ))}
              </div>
              <div className="reach-tip">
                <FaMapMarkerAlt className="reach-tip-icon" />
                <p>{route.tip}</p>
              </div>
            </article>
          ))}
        </div>

        <div className="reach-bottom-cta">
          <div className="reach-address-card">
            <FaMapMarkerAlt className="reach-addr-icon" />
            <div>
              <h4>Hotel Address</h4>
              <p>580 Shankar Gali, Natwar Nagar, Dhauli Pyau, Dholi Pyau, Mathura, Uttar Pradesh — 281001</p>
            </div>
          </div>
          <div className="reach-help-card">
            <FaPhone className="reach-help-icon" />
            <div>
              <h4>Need Directions?</h4>
              <p>Call <a href={`tel:${contact.phone}`}>{phone}</a> — we'll guide you to our hotel!</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default HowToReach;
