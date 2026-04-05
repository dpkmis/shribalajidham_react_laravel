import React from 'react';
import { FaStar, FaStarHalfAlt, FaGoogle, FaTripadvisor, FaAward, FaThumbsUp } from 'react-icons/fa';
import './GoogleRating.css';

const GoogleRating = () => {
  const ratingBreakdown = [
    { stars: 5, percentage: 58 },
    { stars: 4, percentage: 22 },
    { stars: 3, percentage: 12 },
    { stars: 2, percentage: 5 },
    { stars: 1, percentage: 3 },
  ];

  const guestHighlights = [
    { label: 'Clean Rooms', pct: 94 },
    { label: 'Friendly Staff', pct: 90 },
    { label: 'Great Location', pct: 88 },
    { label: 'Tasty Food', pct: 85 },
    { label: 'Value for Money', pct: 82 },
    { label: 'Peaceful Stay', pct: 91 },
  ];

  return (
    <section className="section rating-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle">— Ratings & Reviews —</span>
          <h2 className="section-title">Trusted by Travellers</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Our guests consistently praise our clean rooms, warm hospitality, and prime
            location near Mathura-Vrindavan's most sacred temples.
          </p>
        </div>

        <div className="rating-cards">
          <div className="rating-card gcard">
            <div className="rcard-header">
              <FaGoogle className="rcard-icon google-color" />
              <h3>Google Reviews</h3>
            </div>
            <div className="rcard-score">
              <span className="rcard-number">4.0</span>
              <div className="rcard-stars">
                <FaStar /><FaStar /><FaStar /><FaStar /><FaStarHalfAlt style={{ opacity: 0.3 }} />
              </div>
              <span className="rcard-count">Based on verified guest reviews</span>
            </div>
            <div className="rating-bars">
              {ratingBreakdown.map((item, i) => (
                <div key={i} className="bar-row">
                  <span className="bar-label">{item.stars} <FaStar /></span>
                  <div className="bar-track">
                    <div className="bar-fill" style={{ width: `${item.percentage}%` }}></div>
                  </div>
                  <span className="bar-pct">{item.percentage}%</span>
                </div>
              ))}
            </div>
          </div>

          <div className="rating-card tcard">
            <div className="rcard-header">
              <FaTripadvisor className="rcard-icon tripadvisor-color" />
              <h3>TripAdvisor</h3>
            </div>
            <div className="rcard-score">
              <span className="rcard-number">4.0</span>
              <div className="rcard-stars">
                <FaStar /><FaStar /><FaStar /><FaStar /><FaStarHalfAlt style={{ opacity: 0.3 }} />
              </div>
              <span className="rcard-count">Rated by international visitors</span>
            </div>
            <div className="ta-badge">
              <FaAward />
              <div>
                <span className="ta-badge-title">Excellent Service</span>
                <span className="ta-badge-sub">Guests love our hospitality</span>
              </div>
            </div>
            <div className="ta-quote">
              <FaThumbsUp className="ta-thumb" />
              <p>"Spacious rooms, exceptional service, and an ideal location for exploring Vrindavan temples."</p>
            </div>
          </div>

          <div className="rating-card hcard">
            <h3>Guest Highlights</h3>
            <p className="hcard-sub">What our guests appreciate most</p>
            <div className="highlight-bars">
              {guestHighlights.map((h, i) => (
                <div key={i} className="hbar-row">
                  <div className="hbar-info">
                    <span className="hbar-label">{h.label}</span>
                    <span className="hbar-pct">{h.pct}%</span>
                  </div>
                  <div className="hbar-track">
                    <div className="hbar-fill" style={{ width: `${h.pct}%` }}></div>
                  </div>
                </div>
              ))}
            </div>
            <div className="overall-satisfaction">
              <span className="os-label">Overall Satisfaction</span>
              <div className="os-bar">
                <div className="os-fill" style={{ width: '92%' }}>
                  <span>92%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default GoogleRating;
