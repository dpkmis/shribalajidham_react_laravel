import React from 'react';
import { FaBed, FaUtensils, FaShieldAlt, FaHandsHelping, FaAward, FaCheck } from 'react-icons/fa';
import { useSite } from '../context/SiteContext';
import './About.css';

const About = () => {
  const { general, contact, policies } = useSite();

  const highlights = [
    { icon: <FaBed />, title: 'Spotless Clean Rooms', desc: 'Hygienic, well-maintained AC rooms with fresh linen and daily housekeeping' },
    { icon: <FaUtensils />, title: 'Pure Veg Satvik Meals', desc: 'Homely vegetarian cuisine with complimentary breakfast for all guests' },
    { icon: <FaShieldAlt />, title: 'Safe & Secure Stay', desc: 'CCTV surveillance, safe-deposit box, and 24-hour front desk reception' },
    { icon: <FaHandsHelping />, title: 'Heartfelt Hospitality', desc: 'Warm, family-like service rooted in the devotional spirit of Braj Bhoomi' },
  ];

  const usp = [
    policies.nearest_railway ? `${policies.nearest_railway.split('—')[1]?.trim() || '10 min walk'} from Mathura Junction Railway Station` : '10 minutes walk from Mathura Junction Railway Station',
    'Easy access to Krishna Janmabhoomi & Dwarkadhish Temple',
    'Complimentary breakfast with local & continental options',
    'Free high-speed WiFi throughout the property',
    'Pick-up & drop service to Railway Station and Bus Stand',
    'Expert temple tour guidance for Mathura & Vrindavan darshan',
  ];

  return (
    <section id="about" className="section about-section">
      <div className="about-bg-pattern"></div>
      <div className="container">
        <div className="about-layout">
          <div className="about-visual">
            <div className="about-img-stack">
              <div className="about-img-primary">
                <img
                  src={process.env.PUBLIC_URL + '/images/hotel-entrance.jpeg'}
                  alt={`${general.site_name} entrance in Mathura`}
                  loading="lazy"
                />
              </div>
              <div className="about-img-accent">
                <img
                  src={process.env.PUBLIC_URL + '/images/room-family.jpeg'}
                  alt={`Clean AC rooms at ${general.site_name} Mathura`}
                  loading="lazy"
                />
              </div>
            </div>
            <div className="about-exp-card">
              <FaAward className="exp-icon" />
              <div>
                <span className="exp-number">#1</span>
                <span className="exp-text">Budget Hotel<br/>Near Mathura Jn.</span>
              </div>
            </div>
          </div>

          <div className="about-content">
            <span className="section-subtitle">— About Us —</span>
            <h2 className="section-title" style={{ textAlign: 'left' }}>
              Best Budget Hotel in<br/>
              Mathura Near Railway Station
            </h2>
            <div className="section-divider" style={{ margin: '18px 0 24px', marginLeft: 0 }}>
              <span className="divider-dot" style={{ left: 0, transform: 'none' }}></span>
            </div>

            <p className="about-text">
              Welcome to <strong>{general.site_name}</strong> — Mathura's most trusted
              budget-friendly hotel for pilgrims, families, and travellers. Located at
              <strong> {contact.address_short}</strong>,
              our hotel offers the perfect stay for devotees visiting the sacred city of
              Lord Krishna. Whether you are here for <strong>Krishna Janmabhoomi darshan</strong>,
              Dwarkadhish Temple, or planning a day trip to Vrindavan — we are your ideal base.
            </p>
            <p className="about-text">
              Our commitment to <strong>cleanliness, comfort, and devotion</strong> sets us
              apart. Every room is spotlessly maintained with modern amenities including
              AC, free WiFi, and LED TV. Our in-house restaurant serves fresh, pure
              vegetarian Satvik meals. Whether you are visiting Mathura for religious darshan,
              a family vacation, or the grand festivals of <strong>Holi and Janmashtami</strong> —
              {general.site_name} is your home away from home in the holy city.
            </p>

            <div className="about-usp-list">
              {usp.map((item, i) => (
                <div key={i} className="usp-item">
                  <FaCheck className="usp-check" />
                  <span>{item}</span>
                </div>
              ))}
            </div>

            <div className="about-highlights">
              {highlights.map((h, i) => (
                <div key={i} className="highlight-card">
                  <div className="highlight-icon">{h.icon}</div>
                  <div>
                    <h4>{h.title}</h4>
                    <p>{h.desc}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default About;
