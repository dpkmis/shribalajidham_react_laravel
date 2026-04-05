import React, { useState, useEffect } from 'react';
import { FaCalendarAlt, FaStar, FaArrowRight, FaPhone, FaFire } from 'react-icons/fa';
import { publicAPI } from '../services/api';
import { useSite } from '../context/SiteContext';
import './FestivalOffers.css';

const fallbackFestivals = [
  {
    name: 'Janmashtami 2026',
    nameHindi: 'जन्माष्टमी 2026',
    date: 'August 2026',
    highlight: 'Biggest Festival',
    description: 'Experience the grand birthday celebration of Lord Krishna in his birthplace! Midnight darshan at Krishna Janmabhoomi, 48-hour celebrations, cultural programs, and special decorations across Mathura-Vrindavan.',
    includes: ['2 Nights Stay at Shri BalaJi Dham', 'Midnight Darshan at Krishna Janmabhoomi', 'Vrindavan Temple Tour', 'All Meals (Pure Veg Satvik)', 'AC Transport & Guide', 'Special Festival Puja Arrangement'],
    price: '6,999',
    perNight: '3,499',
    bgColor: 'linear-gradient(135deg, #1a1a2e, #16213e)',
    accentColor: '#FFD700'
  },
  {
    name: 'Holi in Mathura 2027',
    nameHindi: 'मथुरा की होली 2027',
    date: 'March 2027',
    highlight: 'Most Colorful',
    description: 'Mathura-Vrindavan is the Holi capital of India! Experience Lathmar Holi in Barsana, Phoolon ki Holi at Banke Bihari, and the grand Holi celebrations at Dwarkadhish Temple — nowhere else in the world!',
    includes: ['3 Nights Stay at Shri BalaJi Dham', 'Lathmar Holi at Barsana & Nandgaon', 'Phoolon ki Holi at Banke Bihari Temple', 'Dwarkadhish Temple Holi Celebration', 'All Meals & Refreshments', 'AC Transport for All Events'],
    price: '8,999',
    perNight: '2,999',
    bgColor: 'linear-gradient(135deg, #e91e63, #ff5722)',
    accentColor: '#fff'
  },
  {
    name: 'Diwali in Mathura 2026',
    nameHindi: 'मथुरा में दिवाली 2026',
    date: 'October 2026',
    highlight: 'Festival of Lights',
    description: 'Witness the spectacular Diwali celebrations in Mathura and Vrindavan! Thousands of diyas at Vishram Ghat, beautifully illuminated temples, fireworks, and special puja at Krishna Janmabhoomi.',
    includes: ['2 Nights Stay at Shri BalaJi Dham', 'Diwali Aarti at Vishram Ghat', 'Krishna Janmabhoomi Darshan', 'Vrindavan Temple Light Tour', 'Special Diwali Dinner', 'AC Transport & Guide'],
    price: '5,999',
    perNight: '2,999',
    bgColor: 'linear-gradient(135deg, #f57c00, #ffb300)',
    accentColor: '#fff'
  },
  {
    name: 'Braj 84 Kos Parikrama',
    nameHindi: 'ब्रज 84 कोस परिक्रमा',
    date: 'Year-round (Oct-Mar Best)',
    highlight: 'Spiritual Circuit',
    description: 'The sacred 268 km circumambulation of Braj Bhoomi covering Mathura, Vrindavan, Govardhan, Barsana, Nandgaon, Radha Kund, and all 12 forests where Lord Krishna performed his divine leelas.',
    includes: ['3 Nights Stay at Shri BalaJi Dham', 'Complete Braj Parikrama by AC Vehicle', 'Govardhan Parikrama', 'Barsana & Nandgaon Visit', 'Radha Kund & Shyam Kund', 'All Meals & Expert Guide'],
    price: '5,999',
    perNight: '1,999',
    bgColor: 'linear-gradient(135deg, #2e7d32, #4caf50)',
    accentColor: '#fff'
  }
];

const FestivalOffers = () => {
  const { contact } = useSite();
  const [festivals, setFestivals] = useState(fallbackFestivals);

  useEffect(() => {
    publicAPI.festivalOffers()
      .then((res) => {
        const data = res.data?.data || res.data;
        if (Array.isArray(data) && data.length > 0) {
          const mapped = data.map((item) => ({
            name: item.name,
            nameHindi: item.hindi_name,
            date: item.festival_month,
            highlight: item.highlight_badge,
            description: item.description,
            includes: item.includes || [],
            price: Number(item.price).toLocaleString('en-IN'),
            perNight: Number(item.per_night).toLocaleString('en-IN'),
            nights: item.nights,
            image: item.image,
            bgColor: `linear-gradient(135deg, ${item.gradient_from}, ${item.gradient_to})`,
            accentColor: '#fff'
          }));
          setFestivals(mapped);
        }
      })
      .catch(() => {
        // Keep fallback data on error
      });
  }, []);

  return (
    <section id="festival-offers" className="section festival-section">
      <div className="container">
        <div className="section-header">
          <span className="section-subtitle" style={{ color: 'var(--gold)' }}>— Festival Specials —</span>
          <h2 className="section-title">Festival Packages & Seasonal Offers</h2>
          <div className="section-divider"></div>
          <p className="section-description">
            Mathura comes alive during festivals! Book our special festival packages for
            Janmashtami, Holi, Diwali, and Braj Parikrama — complete with stay, darshan, and meals.
          </p>
        </div>

        <div className="festival-grid">
          {festivals.map((fest, i) => (
            <article key={i} className="festival-card">
              <div className="festival-card-top" style={{ background: fest.bgColor }}>
                <span className="festival-badge"><FaFire /> {fest.highlight}</span>
                <h3 className="festival-name">{fest.name}</h3>
                <span className="festival-name-hindi">{fest.nameHindi}</span>
                <div className="festival-date">
                  <FaCalendarAlt /> {fest.date}
                </div>
              </div>
              <div className="festival-card-body">
                <p className="festival-desc">{fest.description}</p>
                <h4>Package Includes:</h4>
                <ul className="festival-includes">
                  {fest.includes.map((item, j) => (
                    <li key={j}><FaStar className="fest-star" /> {item}</li>
                  ))}
                </ul>
                <div className="festival-pricing">
                  <div className="festival-price">
                    <span className="fp-label">Package from</span>
                    <span className="fp-amount">&#8377;{fest.price}</span>
                    <span className="fp-per">per person</span>
                  </div>
                  <a href="{`https://wa.me/${contact.whatsapp || '919639066602'}`}?text=Hi%2C%20I%20want%20to%20book%20the%20{encodeURIComponent(fest.name)}%20package%20at%20Shri%20Balaji%20Dham%20Hotel%20Mathura" target="_blank" rel="noopener noreferrer" className="btn-primary festival-btn">
                    Book Now <FaArrowRight />
                  </a>
                </div>
              </div>
            </article>
          ))}
        </div>

        <div className="festival-notice">
          <FaPhone className="festival-notice-icon" />
          <div>
            <h4>Book Festival Packages Early — Rooms Fill Fast!</h4>
            <p>
              Call <a href="{`tel:${contact.phone}`}">{contact.phone_display}</a> or
              <a href="{`https://wa.me/${contact.whatsapp || '919639066602'}`}" target="_blank" rel="noopener noreferrer"> WhatsApp us</a> to
              check availability and reserve your festival stay. Advance booking recommended for Holi and Janmashtami (2-3 months prior).
            </p>
          </div>
        </div>
      </div>
    </section>
  );
};

export default FestivalOffers;
